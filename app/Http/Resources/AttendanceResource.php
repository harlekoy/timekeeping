<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'ip_address' => $this->ip_address,
            'alias'      => $this->ip->name ?? null,
            'time'       => $this->time->toW3cString() ?? null,
            'type'       => $this->type,
            'location'   => $this->location,
            'notes'      => $this->notes,
            'entry'      => $this->entry,
        ];
    }
}
