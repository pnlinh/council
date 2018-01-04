<?php

namespace App\Rules;

use App\Inspections\Spam;

/**
 * SpamFree
 */
class SpamFree
{
    public function passes($attribute, $value)
    {
        try {
            return ! resolve(Spam::class)->detect($value);
        } catch (\Exception $e) {
            return false;
        }
    }
}
