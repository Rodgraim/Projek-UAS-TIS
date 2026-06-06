<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user');
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable|min:6',
            'role' => 'required|in:admin,user',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422));
    }
}