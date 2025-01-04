<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


include('./helpers/HTTPMethod.php');
include('./models/account.php');
include('./helpers/response.php');
include('./models/register.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Firebase\JWT\JWT;
$JWT_SECRET = 'jny6i$ocue9w';


$method = new HTTPMethod();
$methodR = $method->getMethod();
$register = new Register();
//  echo '<pre>'; print_r($method->getMethod()); echo '</pre>';+

if (!empty($routesArray[1])) {
    switch ($routesArray[1]) {

        case 'login':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $input = file_get_contents('php://input');

                $data = json_decode($input, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }

                if (empty($data['email_user']) || empty($data['password_user'])) {
                    sendJsonResponse(400, null, 'Todos los campos son obligatorios.');
                    return;
                }

                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }
         
                if (!$JWT_SECRET) {
                    sendJsonResponse(500, null, 'Clave secreta de JWT no configurada.');
                    return;
                }
                $usuario = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();

                if ($usuario && password_verify($data['password_user'], $usuario->password_user)) {
                   
                    $payload = [
                        'iss' => 'https://chollocuenca.site/', 
                        'aud' => 'https://chollocuenca.site/',
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + 3600,
                        'data' => [
                            'userId' => $usuario->id
                        ]
                    ];
                   
                    // Generar el token
                    $token = JWT::encode($payload, $JWT_SECRET, 'HS256');
                    
                   
                    sendJsonResponse(200, [
                        'user' => [
                            'id' => $usuario->id,
                            'email_user' => $usuario->email_user,
                            'name_user' => $usuario->name_user,
                            'type_user' => $usuario->type_user,
                            'avatar_user' => $usuario->avatar_user,
                        ],
                        'token' => $token
                    ], 'Inicio de sesión exitoso.');
                } else {
                    sendJsonResponse(403, null, 'Correo electrónico o contraseña incorrectos.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }

            break;

        case 'recovery':
        
            //GENERO UNA NUEVA CONTRASEÑA, SE MANDA AL EMAIL (COMPROBAR QUE EXISTA ANTES) Y SE GUARDA LA NUEVA CONTRASEÑA EN LA BD
               if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $input = file_get_contents('php://input');
                $data = json_decode($input, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }
        
                if (empty($data['email_user'])) {
                    sendJsonResponse(400, null, 'El campo de email es obligatorio.');
                    return;
                }
        
                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }

                $user = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();
        
                if (!$user) {
                    sendJsonResponse(404, null, 'No se encontró un usuario con ese email.');
                    return;
                }
 
                $newPassword = $register->generateRandomPassword();

                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
                $user->password_user = $hashedPassword;
        
                if ($user->save()) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = getenv('MAIL_HOST');
                        $mail->SMTPAuth = true;
                        $mail->Username = getenv('MAIL_USERNAME');
                        $mail->Password = getenv('MAIL_PASSWORD');
                        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') == 'TLS' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = getenv('MAIL_PORT');
        
                        // Remitente y destinatario
                        $mail->setFrom(getenv('MAIL_USERNAME'), 'Chollo Cuenca');
                        $mail->addAddress($data['email_user']); 
        
                        // Contenido del correo
                        $mail->isHTML(true);
                        $mail->Subject = 'Recuperacion de cuenta - CholloCuenca';
                        $mail->Body = "
                            <h2>Recuperación de cuenta</h2>
                            <p>Tu nueva contraseña es: <strong>$newPassword</strong></p>
                            <p>Recuerda cambiarla por una contraseña más segura una vez inicies sesión.</p>
                        ";
        
                        $mail->send();
                        sendJsonResponse(200, null, 'Se ha enviado una nueva contraseña a tu correo.');
                    } catch (Exception $e) {
                        sendJsonResponse(500, null, 'No se pudo enviar el correo.');
                        error_log('Error al enviar el correo: ' . $mail->ErrorInfo);
                    }
                } else {
                    sendJsonResponse(500, null, 'Error al actualizar la contraseña.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
            break;
        case 'new':
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $input = file_get_contents('php://input');

                $data = json_decode($input, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    sendJsonResponse(400, null, 'Formato de JSON inválido.');
                    return;
                }

                if (
                    empty($data['name_user']) || empty($data['email_user']) ||
                    empty($data['password_user']) || empty($data['type_user'])
                ) {
                    sendJsonResponse(400, null, 'Todos los campos obligatorios deben ser completados.');
                    return;
                }

                if (!filter_var($data['email_user'], FILTER_VALIDATE_EMAIL)) {
                    sendJsonResponse(400, null, 'El formato del email no es válido.');
                    return;
                }

                if (strlen($data['password_user']) < 8) {
                    sendJsonResponse(400, null, 'La contraseña debe tener al menos 8 caracteres.');
                    return;
                }

                $existingUser = ORM::for_table('users')->where('email_user', $data['email_user'])->find_one();
                if ($existingUser) {
                    sendJsonResponse(409, null, 'El correo electrónico ya está registrado.');
                    return;
                }

                $usuario = ORM::for_table('users')->create();

                $usuario->name_user = $data['name_user'];
                $usuario->email_user = $data['email_user'];
                $usuario->password_user = password_hash($data['password_user'], PASSWORD_DEFAULT);
                $usuario->type_user = $data['type_user'];

                if ($data['type_user'] === 'COMPANY') {
                    if (empty($data['cif_user'])) {
                        sendJsonResponse(400, null, 'El CIF es requerido para los usuarios tipo \'EMPRESA\'.');
                        return;
                    }
                    $usuario->cif_user = $data['cif_user'];
                }

                $usuario->avatar_user = isset($data['avatar_user']) ? $data['avatar_user'] : null;

                if ($usuario->save()) {
                    $payload = [
                        'iss' => 'https://chollocuenca.site/',
                        'aud' => 'https://chollocuenca.site/',
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + 3600,
                        'data' => [
                            'userId' => $usuario->id
                        ]
                    ];
                    
                    // Generar el token
                    $token = JWT::encode($payload, $JWT_SECRET, 'HS256');

                    sendJsonResponse(200, [
                        'user' => [
                            'id' => $usuario->id,
                            'email_user' => $usuario->email_user,
                            'name_user' => $usuario->name_user,
                            'type_user' => $usuario->type_user,
                            'avatar_user' => $usuario->avatar_user
                        ],
                        'token' => $token
                    ], 'Usuario guardado correctamente.');
                } else {
                    sendJsonResponse(500, null, 'Error al guardar el usuario.');
                }
            } else {
                sendJsonResponse(405, null, 'Método no permitido.');
            }
            break;


        default:
            echo 'default';
            break;
    }
     function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $password;
    }
}

