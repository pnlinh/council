<?php

namespace App\Filters;

use App\User;
use Illuminate\Http\Request;

/**
 * Filters
 */
abstract class Filters
{
    protected $request;
    protected $builder;
    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        $this->getFilters()
            ->filter(function ($filter, $value) {
                return method_exists($this, $filter);
            })->each(function ($filter, $value) {
                $this->$filter($value);
            });

        return $this->builder;
    }

    private function getFilters()
    {
        return collect(array_filter($this->request->only($this->filters)))->flip();
    }
}
