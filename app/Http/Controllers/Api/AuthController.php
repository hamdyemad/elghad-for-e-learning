<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\ResendVerificationRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());

            return $this->createdResponse($result, __('auth.register_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Login user
     * POST /api/auth/login
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());

            return $this->successResponse($result, __('auth.login_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 401);
        }
    }

    /**
     * Logout user
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->successResponse([], __('auth.logout_success'));
    }

    /**
     * Get authenticated user profile
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $this->authService->getProfile($request->user());

        return $this->successResponse(new UserResource($user), __('auth.user_retrieved'));
    }

    /**
     * Send password reset code
     * POST /api/auth/password/forgot
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $this->authService->sendPasswordResetCode($request->email);

            return $this->successResponse([], __('auth.reset_code_sent'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Reset password with code
     * POST /api/auth/password/reset
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $result = $this->authService->resetPasswordWithCode(
                $request->email,
                $request->code,
                $request->password
            );

            if (!$result) {
                return $this->errorResponse(__('auth.reset_failed'), [], 400);
            }

            return $this->successResponse([], __('auth.reset_success'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Verify email with code
     * POST /api/auth/email/verify
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        try {
            $result = $this->authService->verifyEmail($request->email, $request->code);

            return $this->successResponse([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => $result['token_type']
            ], __('auth.email_verified'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Resend verification code
     * POST /api/auth/email/resend
     */
    public function resendVerification(ResendVerificationRequest $request)
    {
        try {
            $this->authService->resendVerificationCode($request->email);

            return $this->successResponse([], __('auth.verification_code_resent'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), [], 400);
        }
    }

    /**
     * Check if email is verified
     * GET /api/auth/email/check/{email}
     */
    public function checkVerification($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse(__('auth.invalid_email'), [], 400);
        }

        $isVerified = $this->authService->isEmailVerified($email);

        return $this->successResponse([
            'email' => $email,
            'is_verified' => $isVerified
        ], __('auth.verification_status_retrieved'));
    }
}

