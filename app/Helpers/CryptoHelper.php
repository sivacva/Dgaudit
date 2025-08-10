<?php

namespace App\Helpers;

class CryptoHelper {
    public static function decryptPassword($encryptedPassword) {
        $secretKey = base64_decode(env('AES_SECRET_KEY')); // Decode the Base64 key
        $iv = env('AES_IV'); // IV must be 16 characters

        if (!$secretKey || strlen($iv) !== 16) {
            return 'Error: Invalid secret key or IV length';
        }

        // Convert encrypted password from Base64
        $encryptedPassword = base64_decode($encryptedPassword);
        if (!$encryptedPassword) {
            return 'Error: Invalid Base64 encoding';
        }
        $decrypted = openssl_decrypt(
            $encryptedPassword,
            'AES-256-CBC',
            $secretKey,
            OPENSSL_RAW_DATA,  // Ensure this is an integer
            $iv
        );
        

        return $decrypted ?: 'Error: Decryption failed';
    }
}