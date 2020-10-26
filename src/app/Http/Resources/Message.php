<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->fromUser;

        return [
            'id' => $this->id,
            'message' => $this->content,
            'from_user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ];
    }
}
