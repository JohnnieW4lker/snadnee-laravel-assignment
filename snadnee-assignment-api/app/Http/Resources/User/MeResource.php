<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
{
    private string $accessToken;

    public function __construct($resource, string $accessToken)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
    }

    public function toArray(Request $request): array
    {
        return [
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'accessToken' => $this->accessToken,
        ];
    }
}
