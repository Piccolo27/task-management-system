<?php

namespace App\Repositories;

use App\Contracts\Repositories\AuthRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Reset user account password
     *
     * @param array $credentials
     * @return mixed $status
     */
    public function saveNewPassword(array $credentials): mixed
    {
        return Password::reset($credentials, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);

            $user->save();

            event(new PasswordReset($user));
        });
    }
}
