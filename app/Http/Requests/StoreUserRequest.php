<?php

namespace App\Http\Requests;

use App\Enums\EtatEnum;
use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users|max:255',
            'role_id' => 'required|numeric|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'etat' => 'required|string|in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases())),
            'password' => ['required', 'string', 'min:8', 'confirmed', new CustumPasswordRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Cet login est déjà utilisé.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.exists' => 'Le rôle spécifié n\'existe pas.',
            'photo.image' => 'La photo doit être une image.',
            'photo.mimes' => 'La photo doit être de type jpeg, png, jpg ou gif.',
            'etat.required' => 'L\'état est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(), StateEnum::ECHEC, 422));
    }
}
