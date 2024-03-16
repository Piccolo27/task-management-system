<?php

namespace App\Services;

use App\Contracts\Repositories\AuthRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Contracts\Services\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepository;

    /**
     * Create a new AuthService instance
     *
     * @param AuthRepositoryInterface $authRepository
     */
    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param $credentials
     * @return JsonResponse
     */
    public function login($credentials): JsonResponse
    {
        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'error' => config('constants.errors.INCORRECT_EMAIL_PASSWORD')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->responseWithToken($token);
    }

    /**
     * Log the user out and invalidate the jwt token
     *
     * @return void
     */
    public function logout(): void
    {
        JWTAuth::parseToken()->invalidate();
    }

    /**
     * To send password reset email to user
     *
     * @param array $credentials
     * @return JsonResponse
     */
    public function sendResetPasswordLink(array $credentials): JsonResponse
    {
        $status = Password::sendResetLink($credentials);

        return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => config('constants.success.PASSWORD_RESET_LINK')], JsonResponse::HTTP_OK)
                : response()->json(['error' => config('constants.errors.email.INCORRECT'), JsonResponse::HTTP_NOT_FOUND]);
    }

    /**
     * To reset user account password
     *
     * @param array $credentials
     * @return JsonResponse
     */
    public function resetPassword(array $credentials): JsonResponse
    {
        $status = $this->authRepository->saveNewPassword($credentials);

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => config('constants.success.PASSWORD_RESET')], JsonResponse::HTTP_OK)
            : response()->json(['error' => config('constants.errors.SOMETHING_WRONG'), JsonResponse::HTTP_INTERNAL_SERVER_ERROR]);
    }

    /**
     * Get the response array structure with token
     *
     * @param string $token
     * @return JsonResponse
     */
    public function responseWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token
        ], JsonResponse::HTTP_OK);
    }
}
