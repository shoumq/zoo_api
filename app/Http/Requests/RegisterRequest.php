<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50|min:1',
            'surname' => 'required|string|max:50|min:1',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => 'required',
            'phone' => 'max:20',
            'address' => 'required',
        ];
    }
}
