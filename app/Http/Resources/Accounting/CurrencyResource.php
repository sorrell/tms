<?php

namespace App\Http\Resources\Accounting;

use App\Facades\AliasResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Accounting\Currency
 */
class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
