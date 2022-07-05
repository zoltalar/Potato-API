<?php

declare(strict_types = 1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Schema;
use Str;

abstract class Base extends Model
{
    const DEFAULT_STRING_LENGTH = 255;

    /**
     * Relationship constraints prohibiting the model from being deleted.
     *
     * @var array
     */
    protected $constraints = [];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    /**
     * Active scope which automatically selects active only models (to be used in front-end).
     *
     * @param   Builder $query
     * @return  Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    /**
     * Select model's specific columns by exclusion.
     *
     * @param   Builder $query
     * @param   array $except
     * @return  Builder
     */
    public function scopeExcept(Builder $query, array $except = []): Builder
    {
        return $query->select(array_diff(Schema::getColumnListing($this->getTable()), $except));
    }

    /**
     * Dynamic scope for performing query string aware orderBy statements.
     *
     * @param   Builder $query
     * @param   string $column sort column
     * @param   string $direction sort direction
     * @return  Builder
     */
    public function scopeOrders(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        $column = ($request = request())->get('order_by', $column);
        $direction = $request->get('direction', $direction);

        return $query->orderBy($column, $direction);
    }

    /**
     * Dynamic scope for performing search against specified columns in a model.
     *
     * @param   Builder $query
     * @param   array $columns
     * @param   ?string $phrase
     * @param   ?array $callbacks
     * @return  Builder
     */
    public function scopeSearch(Builder $query, array $columns, string $phrase = null, array $callbacks = []): Builder
    {
        if (empty($phrase)) {
            $phrase = request()->get('search');
        }

        $phrase = (string) $phrase;

        if (is_numeric($phrase) || filter_var($phrase, FILTER_VALIDATE_EMAIL)) {
            $words = [$phrase];
        } else {
            $words = str_word_count($phrase, 1, Language::diacritics());
        }

        foreach ($words as $word) {
            $word = trim($word);

            foreach ($callbacks as $callback)
            {
                if (function_exists($callback)) {
                    $word = call_user_func($callback, $word);
                }
            }

            $operator = 'like';

            if (is_numeric($word)) {
                $operator = '=';
            } else {
                $word = "%$word%";
            }

            $query->where(function($builder) use ($columns, $operator, $word) {
                foreach ($columns as $column) {
                    $builder->orWhere($column, $operator, $word);
                }
            });
        }

        return $query;
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    /**
     * Determine if model contains an attribute.
     *
     * @param   string $key
     * @return  bool
     */
    public function hasAttribute(string $key): bool
    {
        return Schema::hasColumn($this->getTable(), $key);
    }

    /**
     * Override
     *
     * @throws  Exception
     *
     * @param   bool $constraints run through delete constraints to check if model can be deleted.
     * @return  bool|null
     */
    public function delete($constraints = false)
    {
        if ($this->exists) {

            if ($constraints) {

                foreach ($this->constraints as $constraint) {
                    $related = $this->{$constraint};

                    if ($related !== null) {

                        if (isset($related->id) && $related->id !== null) {
                            return false;
                        } elseif ($related->count() > 0) {
                            return false;
                        }
                    }
                }
            }

            if ($this->hasAttribute('system') && $this->system == 1) {
                return false;
            }
        }

        return parent::delete();
    }
}
