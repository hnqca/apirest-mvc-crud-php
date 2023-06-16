<?php

    namespace App\Sdk\JsonWebToken;

    /**
     * Classe responsável por gerar, decodificar e validar tokens JWT.
    */
    class JWT {

        private static function validateAlgorithm(string $algorithm)
        {
            $algorithm = strtoupper($algorithm);

            $whiteList = ["HS256", "HS384", "HS512", "RS256", "RS384", "RS512", "ES256", "ES384", "ES512", "PS256", "PS384", "PS512"];
            
            if (!in_array($algorithm, $whiteList)) {
                throw new \InvalidArgumentException("Enter a valid alogirithm: " . implode(', ', $whiteList));
            }

            return $algorithm;
        }

        private static function validatePayload(array $payload = [])
        {
            if (empty($payload)) {
                throw new \InvalidArgumentException("payload cannot be empty.");
            }

            return $payload;
        }

        private static function getTokenParts(string $token)
        {
            $tokenParts = explode('.', $token);
            
            if (count($tokenParts) !== 3) {
                throw new \InvalidArgumentException("Enter a JWT token to decode");
            }

            return $tokenParts;
        }

        private static function calculateSignature($secretKey, $header, $payload) {
            return base64_encode(hash_hmac('sha256', "{$header}.{$payload}", $secretKey, true));
        }

        private static function tokenHasExpired(string $token): bool
        {
            $payload = json_decode(base64_decode(explode('.', $token)[1]), true);

            if (!isset($payload['exp'])) {
                return true;
            }

            return $payload['exp'] > time();
        }

        public static function encode(string $secretKey, array $payload, string $alg = "HS256")
        {
            $header = [
                'alg' => self::validateAlgorithm($alg),
                'typ' => 'JWT'
            ];

            $payload   = self::validatePayload($payload);
            $header    = base64_encode(json_encode($header));
            $payload   = base64_encode(json_encode($payload));
            $signature = self::calculateSignature($secretKey, $header, $payload);

            return "{$header}.{$payload}.{$signature}";
        }

        public static function decode(string $token)
        {
            $tokenParts = self::getTokenParts($token);
            $payload    = $tokenParts[1];

            return json_decode(base64_decode($payload));
        }

        public static function validate(string $secretKey, string $token)
        {
            $tokenParts = self::getTokenParts($token);

            if (!self::tokenHasExpired($token)) {
                return false;
            }

            $header            = $tokenParts[0];
            $payload           = $tokenParts[1];
            $signatureProvided = $tokenParts[2];

            $signature = self::calculateSignature($secretKey, $header, $payload);

            return hash_equals($signature, $signatureProvided);
        }

    }