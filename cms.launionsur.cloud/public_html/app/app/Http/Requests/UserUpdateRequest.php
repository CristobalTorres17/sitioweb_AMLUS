<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->route('user');
        return $this->user() && $this->user()->can('update', $user);
    }

    public function rules()
    {
        $user = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($user ? $user->id : 'NULL'),
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'is_active' => 'boolean',
            'locked_until' => 'nullable|date',
        ];
    }
}
