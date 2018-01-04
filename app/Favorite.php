<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
    }

    public function favorited()
    {
        return $this->morphTo();
    }
}
