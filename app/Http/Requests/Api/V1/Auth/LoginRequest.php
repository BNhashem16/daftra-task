<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', new PasswordRule($this->email)],
        ];
    }
}
