<?php


namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthService
{
    protected ?Authenticatable $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function getAuthToken(): ?string
    {
        return auth()->user()->createToken('authToken')->accessToken;
    }
}
