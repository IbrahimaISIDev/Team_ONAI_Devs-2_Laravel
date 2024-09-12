<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Vous pouvez ajuster cela en fonction de votre logique
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'montantDu' => 'required|numeric|min:0',
            'montantRestant' => 'required|numeric|min:0',
            'client_id' => 'required|exists:clients,id',
            'articles' => 'required|array',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'articles.*.id.exists' => 'L\'article sélectionné n\'existe pas.',
        ];
    }
}

