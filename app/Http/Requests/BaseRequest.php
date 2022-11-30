<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Поле :attribute является обязательным',
            '*.string' => 'В поле :attribute ожидается строка',
            '*.alpha_num' => 'В поле :attribute ожидается только буквенно-цифровые символы',
            '*.email' => 'В поле :attribute ожидается адрес электронной почты',
            '*.min' => 'Минимальная длина :attribute должна составлять :min знаков',
            '*.max' => 'Максимальная длина :attribute должна быть не больше :max знаков',
            '*.unique' => 'Поле :attribute не является уникальным',
            '*.alpha_dash' => 'Поле :attribute может содержать только буквенно-цифровые символы, тире и подчеркивания.'
        ];
    }
}
