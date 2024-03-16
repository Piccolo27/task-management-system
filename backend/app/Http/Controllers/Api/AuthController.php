<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Contracts\Services\AuthServiceInterface;
use App\Http\Requests\Api\ForgotPasswordRequest;

class AuthController extends Controller
{
    protected AuthServiceInterface $authService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'sendResetPasswordLink', 'resetPassword']]);
        $this->authService = $authService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        return $this->authService->login($credentials);
    }

    /**
     * Log the user out
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'Successfully logged out'], JsonResponse::HTTP_OK);
    }

    /**
     * Refresh a token
     *
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        return $this->authService->responseWithToken(auth()->refresh());
    }

    /**
     * To send reset password link to user
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function sendResetPasswordLink(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->authService->sendResetPasswordLink($request->validated());
    }

    /**
     * To reset password of user account
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        return $this->authService->resetPassword($credentials);
    }
}
