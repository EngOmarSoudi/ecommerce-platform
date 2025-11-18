<?php

namespace App\Services\Auth;

interface OTPServiceInterface
{
    /**
     * Generate an OTP for a phone number
     *
     * @param string $phoneNumber
     * @return string
     */
    public function generateOTP(string $phoneNumber): string;

    /**
     * Verify an OTP for a phone number
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function verifyOTP(string $phoneNumber, string $otp): bool;

    /**
     * Send OTP to phone number
     *
     * @param string $phoneNumber
     * @param string $otp
     * @return bool
     */
    public function sendOTP(string $phoneNumber, string $otp): bool;
}