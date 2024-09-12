<?php

namespace App\Http\Requests;

use App\Enums\EtatEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;

class UpdateClientRequest extends StoreClientRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        // Modifier les règles pour la mise à jour
        $rules['surname'] = ['sometimes', 'string', 'max:255', 'unique:clients,surname,' . $this->route('id')];
        $rules['telephone'] = ['sometimes', new TelephoneRule()];

        // Rendre les champs optionnels pour la mise à jour
        $rules['user'] = ['sometimes', 'array'];
        $rules['user.nom'] = ['sometimes', 'string'];
        $rules['user.prenom'] = ['sometimes', 'string'];
        $rules['user.login'] = ['sometimes', 'string'];
        $rules['user.role_id'] = ['sometimes', 'numeric', 'exists:roles,id'];
        $rules['user.password'] = ['sometimes', new CustumPasswordRule(), 'confirmed'];

        return $rules;
    }
}