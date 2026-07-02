<?php

namespace App\Services;

class Notification
{
    /**
     * Firebase Service Account JSON Path
     */
    private string $serviceAccountFile;

    /**
     * Firebase Project ID
     */
    private string $projectId = 'codzshop-1e8e6';

    public function __construct()
    {
        $this->serviceAccountFile = storage_path(
            'app/firebase/codzshop-1e8e6-firebase-adminsdk-fbsvc-1c624ff6da.json'
        );
    }

    /**
     * Generate Firebase Access Token
     */
    private function getAccessToken(): ?string
    {
        if (!file_exists($this->serviceAccountFile)) {
            throw new \Exception('Firebase service account file not found.');
        }

        $key = json_decode(
            file_get_contents($this->serviceAccountFile),
            true
        );

        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $jwtPayload = [
            'iss'   => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => time() + 3600,
            'iat'   => time(),
        ];

        $base64UrlHeader = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode(json_encode($jwtHeader))
        );

        $base64UrlPayload = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode(json_encode($jwtPayload))
        );

        openssl_sign(
            $base64UrlHeader . '.' . $base64UrlPayload,
            $signature,
            $key['private_key'],
            OPENSSL_ALGO_SHA256
        );

        $base64UrlSignature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($signature)
        );

        $jwt = $base64UrlHeader . '.'
            . $base64UrlPayload . '.'
            . $base64UrlSignature;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($response, true);

        return $response['access_token'] ?? null;
    }

    /**
     * Send Firebase Push Notification
     */
    public function sendFCMNotification(
        string $token,
        string $title,
        string $message
    ): array {

        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to generate access token.',
            ];
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $message,
                ],
            ],
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'success' => false,
                'message' => curl_error($ch),
            ];
        }

        curl_close($ch);

        return [
            'success' => true,
            'response' => json_decode($response, true),
        ];
    }
}
