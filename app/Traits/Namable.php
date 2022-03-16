<?php

declare(strict_types = 1);

namespace App\Traits;

trait Namable
{
    /**
     * Retrieve person's full name.
     *
     * @param   bool $standard
     * @return  string
     */
    public function fullName(bool $standard = true): string
    {
        $name = '';

        if ($standard) {
            if ( ! empty($this->first_name)) {
                $name .= $this->first_name;
                $name .= ' ';
            }

            $name .= $this->last_name;
        } else {
            $name .= $this->last_name;

            if ( ! empty($this->first_name)) {
                $name .= ', ';
                $name .= $this->first_name;
            }
        }

        return $name;
    }
}