<?php

function getJWTFromRequest($authencationHeader): string
{
    if(is_null($authencationHeader)) {
        throw new Exception('Missing or invalid JWT in request');
    }

    return explode(' ', $authencationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    $key = \Config\Services::getSecretKey();
    $decodedToken = \Firebase\JWT\JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new \App\Models\UserModel();
    $userModel->findUserByEmailAddress($decodedToken->email);
}

function getSignedJWTForUser(string $email)
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration
    ];

    $jwt = \Firebase\JWT\JWT::encode($payload, \Config\Services::getSecretKey());

    return $jwt;
}