<?php

// Si el método es OPTIONS, devolver inmediatamente los encabezados sin ejecutar más código.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
$JWT_SECRET = getenv('JWT_SECRET');

$method = new HTTPMethod();
$methodR = $method->getMethod();
//  echo '<pre>'; print_r($method->getMethod()); echo '</pre>';+

if (!empty($routesArray[1])) {
    switch ($routesArray[1]) {

        case 'checkToken':
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            sendJsonResponse(400, null, 'Token no proporcionado en el encabezado.');
            return;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $key = $JWT_SECRET;

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Verificar si el token ha expirado
            if (isset($decoded->exp) && $decoded->exp < time()) {
                throw new Exception('Token expirado.');
            }

            $userId = $decoded->data->userId;

            $usuario = ORM::for_table('users')->find_one($userId);

            if ($usuario) {
                sendJsonResponse(200, [
                    'user' => [
                        'id' => $usuario->id,
                        'email_user' => $usuario->email_user,
                        'name_user' => $usuario->name_user,
                        'type_user' => $usuario->type_user,
                        'avatar_user' => $usuario->avatar_user
                    ]
                ], 'Token válido.');
            } else {
                sendJsonResponse(403, null, 'Usuario no encontrado.');
            }
        } catch (Exception $e) {
            sendJsonResponse(401, null, 'Token inválido o expirado.');
        }
    } else {
        sendJsonResponse(405, null, 'Método no permitido.');
    }
    break;

        default:
            echo 'default';
            break;
    }
}
