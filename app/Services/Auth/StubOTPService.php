<?php

namespace App\Services\Auth;

class StubOTPService implements OTPServiceInterface
{
    /**
     * Generate an OTP for a phone number
     *
     * @param string $phoneNumber
     * @return string
     */
    public function generateOTP(string $phoneNumber): string
    {
        // In a real implementation, this would generate a random OTP
        // For now, we'll return a fixed OTP for testing
        return '123456';
    }

    /**
     * Verify an OTP for a phone number
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function verifyOTP(string $phoneNumber, string $otp): bool
    {
        // In a real implementation, this would check against stored OTPs
        // For now, we'll just check if it matches our stub OTP
        return $otp === '123456';
    }

    /**
     * Send OTP to phone number
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function sendOTP(string $phoneNumber, string $otp): bool
    {
        // In a real implementation, this would send an SMS
        // For now, we'll just log it and return true
        \Log::info("OTP sent to {$phoneNumber}: {$otp}");
        return true;
    }
}