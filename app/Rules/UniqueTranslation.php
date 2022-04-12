<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Base;
use App\Models\Category;
use App\Models\Language;
use App\Models\Translation;
use Arr;
use Illuminate\Contracts\Validation\Rule;

class UniqueTranslation implements Rule
{
    /** @var array */
    protected $data = [];

    /** @var Language */
    protected $language;

    /** @var Base */
    protected $translatable;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->initialize();
    }

    protected function initialize(): void
    {
        $data = $this->data;
        $type = Arr::get($data, 'translatable_type');

        $this->language = Language::find(Arr::get($data, 'language_id'));

        if ($type == Translation::TYPE_CATEGORY) {
            $this->translatable = Category::find(Arr::get($data, 'translatable_id'));
        }
    }

    public function passes($attribute, $value): bool
    {
        $language = $this->language;
        $translatable = $this->translatable;

        if ($language !== null && $translatable !== null) {
            return $translatable->translation($language) === null;
        }

        return true;
    }

    public function message(): string
    {
        return __('messages.unique_translation_error');
    }
}
