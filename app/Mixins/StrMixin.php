<?php

declare(strict_types = 1);

namespace App\Mixins;

use Closure;

class StrMixin
{
    public function phrases(): Closure
    {
        /**
         * Retrieve phrases from multi-line string.
         *
         * @param   string $string
         * @return  string
         */
        return function($string)
        {
            $phrases = [];

            if ( ! empty($string)) {
                $string = str_replace("\n\r", "\n" , $string);
                $rows = explode("\n", $string);

                foreach ($rows as $row) {
                    $array = preg_split('/;|,/', $row);

                    foreach ($array as $phrase) {
                        $phrases[] = trim($phrase);
                    }
                }

                $phrases = array_unique($phrases, SORT_STRING);
            }

            return $phrases;
        };
    }

    public function stripNonDigits(): Closure
    {
        /**
         * Remove non-numeric characters from string.
         *
         * @param   string $value
         * @return  string
         */
        return function($value)
        {
            return preg_replace('/[^0-9]/', '', $value);
        };
    }
}
