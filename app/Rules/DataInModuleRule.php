<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DataInModuleRule implements Rule
{
    /**
     * @var array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\Request|string|null
     */
    private $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = request();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $type = $this->request->get('type');

        if (in_array($type, ['banners', 'sliders'])) {
            return is_numeric($value);
        } elseif (in_array($type, ['products', 'categories'])) {
            return is_array($value);
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'invalid data type';
    }
}
