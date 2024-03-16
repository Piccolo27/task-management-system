<?php

namespace App\Contracts\Services;

interface AuthServiceInterface
{
    public function login(array $credentials);
    public function logout();
    public function sendResetPasswordLink(array $credentials);
    public function resetPassword(array $credentials);
    public function responseWithToken(string $token);
}
