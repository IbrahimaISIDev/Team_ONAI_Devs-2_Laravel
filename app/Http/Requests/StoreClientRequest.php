<?php

namespace App\Http\Requests;

use App\Enums\EtatEnum;
use App\Enums\StateEnum;
use App\Rules\EmailRule;
use App\Rules\TelephoneRule;
use App\Rules\CustumPasswordRule;
use App\Traits\RestResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;
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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'adresse' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRule(),'unique:clients,telephone'],
            'email' => ['required',new EmailRule(),'unique:clients,email'],

            'user' => ['sometimes','array'],
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            'user.role_id' => ['required_with:user', 'numeric', 'exists:roles,id'],
            'user.password' => ['required_with:user', new CustumPasswordRule(),'confirmed'],
            'user.photo' => ['required_with:user', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], 
            'etat' => ['string', 'in:' . implode(',', array_map(fn($case) => $case->value, EtatEnum::cases()))],
        ];
/*
        if ($this->filled('user')) {
            $userRules = (new StoreUserRequest())->Rules();
            $rules = array_merge($rules, ['user' => 'array']);
            $rules = array_merge($rules, array_combine(
                array_map(fn($key) => "user.$key", array_keys($userRules)),
                $userRules
            ));
        }
*/
      //  dd($rules);

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
            'user.photo.required_with' => "La photo est obligatoire lorsque les informations de l'utilisateur sont fournies.",

        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}