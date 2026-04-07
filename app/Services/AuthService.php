<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use App\Mail\PasswordResetCodeMail;
use Carbon\Carbon;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        // Check if email already exists
        $existingUser = $this->userRepository->findByEmail($data['email']);
        if ($existingUser) {
            // If user exists but email is not verified, we can allow "re-registration"
            // by deleting the old unverified user and creating a new one
            if (is_null($existingUser->email_verified_at)) {
                // Delete the unverified user and all their related data
                $existingUser->roles()->detach();
                $existingUser->permissions()->detach();
                $this->userRepository->delete($existingUser->id);
            } else {
                // Email is verified - cannot register again
                throw new \Exception(__('auth.email_exists_verified'));
            }
        }

        $data['password'] = Hash::make($data['password']);
        $data['type'] = $data['type'] ?? 'student'; // Default to student
        $data['email_verified_at'] = null; // Not verified initially

        $user = $this->userRepository->create($data);

        // Assign default role (student)
        $studentRole = \App\Models\Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        // Generate and send verification code
        $this->sendVerificationCode($user->email);

        // Do NOT create token yet - only after verification
        // Just return minimal info
        return [
            'email' => $user->email
        ];
    }

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception(__('auth.invalid_credentials'));
        }

        // Check if user is verified
        if (is_null($user->email_verified_at)) {
            throw new \Exception(__('auth.email_not_verified'));
        }

        // Check if user is active
        if ($user->status !== 'active') {
            throw new \Exception(__('auth.account_inactive'));
        }

        // Delete old tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
        return true;
    }

    public function getProfile($user)
    {
        // Load relationships
        $user->load(['roles', 'permissions']);
        return $user;
    }

    /**
     * Generate and send email verification code
     */
    public function sendVerificationCode(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception(__('auth.user_not_found'));
        }

        // Delete old verification codes
        EmailVerificationCode::where('email', $email)->delete();

        // Generate new code (6 digits)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save code
        EmailVerificationCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        // LOG CODE FOR DEVELOPMENT (only if debug mode or specific env flag)
        if (config('app.debug') || env('SHOW_VERIFICATION_CODES_IN_LOGS', false)) {
            \Log::info("=== VERIFICATION CODE for {$email} === Code: {$code} === Expires: " . Carbon::now()->addMinutes(15)->format('Y-m-d H:i:s'));
        }

        // Send email
        try {
            Mail::to($email)->send(new EmailVerificationMail($code, $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
            // Re-throw if in production to let user know
            if (!config('app.debug')) {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(string $email, string $code)
    {
        $verification = EmailVerificationCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
            throw new \Exception(__('auth.invalid_or_expired_code'));
        }

        // Find user and verify
        $user = $this->userRepository->findByEmail($email);
        $this->userRepository->update($user->id, [
            'email_verified_at' => Carbon::now()
        ]);

        // Delete used code
        $verification->delete();

        // Create token now that email is verified
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Send password reset code
     */
    public function sendPasswordResetCode(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            // Don't reveal that user doesn't exist
            return true;
        }

        // Delete old codes
        \App\Models\PasswordResetCode::where('email', $email)->delete();

        // Generate code (6 digits)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save code
        \App\Models\PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        // LOG CODE FOR DEVELOPMENT (only if debug mode or specific env flag)
        if (config('app.debug') || env('SHOW_VERIFICATION_CODES_IN_LOGS', false)) {
            \Log::info("=== PASSWORD RESET CODE for {$email} === Code: {$code} === Expires: " . Carbon::now()->addMinutes(15)->format('Y-m-d H:i:s'));
        }

        // Send email
        try {
            Mail::to($email)->send(new PasswordResetCodeMail($code, $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            // Re-throw if in production to let user know
            if (!config('app.debug')) {
                throw $e;
            }
        }

        return true;
    }

    /**
     * Reset password with code
     */
    public function resetPasswordWithCode(string $email, string $code, string $newPassword)
    {
        $resetCode = \App\Models\PasswordResetCode::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetCode) {
            throw new \Exception(__('auth.invalid_or_expired_code'));
        }

        // Find user
        $user = $this->userRepository->findByEmail($email);

        // Update password
        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword)
        ]);

        // Invalidate all tokens
        $user->tokens()->delete();

        // Delete used code
        $resetCode->delete();

        return true;
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);
        return $user ? !is_null($user->email_verified_at) : false;
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception(__('auth.user_not_found'));
        }

        // Check if already verified
        if (!is_null($user->email_verified_at)) {
            throw new \Exception(__('auth.already_verified'));
        }

        // Delete old codes and send new one
        EmailVerificationCode::where('email', $email)->delete();

        return $this->sendVerificationCode($email);
    }
}
