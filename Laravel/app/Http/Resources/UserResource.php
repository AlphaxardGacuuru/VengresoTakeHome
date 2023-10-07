<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if user is logged in
        $username = auth('sanctum')->user()
        ? auth('sanctum')->user()->username
        : '@guest';

        return [
            "id" => $this->id,
            "name" => $this->name,
            "username" => $this->username,
            "email" => $this->email,
            "phone" => $this->phone,
            "avatar" => $this->avatar,
            "backdrop" => $this->backdrop,
            "accountType" => $this->account_type,
            "dob" => $this->dob,
            "bio" => $this->bio,
            "withdrawal" => $this->withdrawal,
            "createdAt" => $this->created_at,
        ];
    }
}
