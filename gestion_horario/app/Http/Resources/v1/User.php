<?php
namespace App\Http\Resources\V1;
use Illuminate\Http\Resources\Json\JsonResource;
class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'user_name' => $this->user_name,
            'professor_code' => $this->professor_code,
            'id' => $this->id,
        ];
    }
}