<?php

namespace App\Http\Resources;

use App\Traits\Resources\HasDates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Note
 */
class NoteResource extends JsonResource
{   
    use HasDates;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'id' => $this->id,
            'note' => $this->note,
            'user' => UserResource::make($this->whenLoaded('user')),
            'user_id' => $this->user_id,
            'created_at' => $this->asDate($this->created_at)    ,
            'updated_at' => $this->asDate($this->updated_at),
            'deleted_at' => $this->when($this->deleted_at !== null, $this->asDate($this->deleted_at)),
            'notable' => $this->whenLoaded('notable'),
        ];
    }
}
