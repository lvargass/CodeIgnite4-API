<?php 

use \Firebase\JWT\JWT;
use \Config\Services;
use \App\Models\UserModel;

use CodeIgniter\HTTP\RequestInterface;

function getJWTFromRequest($authHeader): string {
    if (is_null($authHeader)) {
        throw new Exception("Error Processing JWT Request", 1);
    }

    return explode(' ', $authHeader)[1];
}

function getSignedJWT(string $email): string {
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $key = Services::getSecretKey();

    $payload = [
        'email' =>  $email,
        'iat'   =>  $issuedAtTime,
        'exp'   =>  $tokenExpiration
    ];
    // Consultando el toke
    $jwt = JWT::encode($payload, $key, 'HS256');
    // Retornando la firma el token
    return $jwt;
}
// Funcion que valida que el token request enviado le pertenece a algun usuario en nuestra base de datos
function validateJWTFromRequest(string $encodeToken) {
    $key = Services::getSecretKey();
    $decodeToken = JWT::decode($encodeToken, $key, ['HS256']);
    $userModel = new UserModel();

    $userModel->findUserByEmailAddress($decodeToken->email);
}
// Validamos el api del request
function validateApiKeyFromRequest(RequestInterface $request) {
    // Obteniendo el APi key
    $apiKey = $request->getHeaderLine('x-api-key');
    $country = $request->getHeaderLine('Country');
    // Validaciones
    if ($country == null)
        throw new Exception('Country header is missing');
    if ($apiKey != getenv('X-API-KEY'))
        throw new Exception('Invalid API key'. $apiKey);
}