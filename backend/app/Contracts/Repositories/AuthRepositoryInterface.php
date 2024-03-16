<?php

namespace App\Contracts\Repositories;

interface AuthRepositoryInterface
{
    public function saveNewPassword(array $credentials);
}
