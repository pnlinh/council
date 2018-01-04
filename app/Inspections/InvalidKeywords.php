<?php

namespace App\Inspections;

use Exception;

/**
 * InvalidKeywords
 */
class InvalidKeywords
{
    protected $keywords = [
        'yahoo customer support',
    ];

    public function detect($body)
    {
        foreach ($this->keywords as $keyword) {
            if (str_contains(strtolower($body), $keyword)) {
                throw new Exception('Spam');
            }
        }
    }
}
