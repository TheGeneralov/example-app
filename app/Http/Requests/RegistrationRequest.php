<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\Shop\RegistrationDto;
use App\Enums\RolesEnum;

class RegistrationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:App\Models\User,email',
            'phone' => 'required|string|min:9|max:16',
            'password' => 'required|string|min:6|max:255',
            'name_shop' => 'required|string',
            'utm_source' => 'nullable',
            'utm_medium' => 'nullable',
            'utm_campaign' => 'nullable',
            'utm_content' => 'nullable',
            'utm_term' => 'nullable',
        ];
    }

    public function toDto(): RegistrationDto
    {
        return new RegistrationDto(
            $this->input('email'),
            $this->input('password'),
            $this->input('name'),
            $this->input('phone'),
            $this->input('name_shop'),
            $this->input('utm_source'),
            $this->input('utm_medium'),
            $this->input('utm_campaign'),
            $this->input('utm_content'),
            $this->input('utm_term'),
            RolesEnum::MANAGER()
        );
    }
}
