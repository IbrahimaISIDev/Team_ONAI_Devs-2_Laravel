<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'montant' => $this->montant,
            'montantDu' => $this->montantDu,
            'montantRestant' => $this->montantRestant,
            'client' => new ClientResource($this->whenLoaded('client')),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
        ];
    }
}
