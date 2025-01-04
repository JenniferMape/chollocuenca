<?php

class Account
{

    public function getAccount($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);
        return $usuario ? $this->convertObj($usuario) : null;
    }

    public function updateAccount($data)
    {
        $usuario = ORM::for_table('users')->find_one($data['id']);

        if ($usuario) {
            $usuario->name_user = $data['name_user'] ?? $usuario->name_user;
            $usuario->email_user = $data['email_user'] ?? $usuario->email_user;

            if (!empty($data['password_user'])) {
                $usuario->password_user = password_hash($data['password_user'], PASSWORD_DEFAULT);
            }

            if ($usuario->type_user === 'COMPANY' && !empty($data['cif_user'])) {
                $usuario->cif_user = $data['cif_user'];
            }

            $usuario->avatar_user = $data['avatar_user'] ?? $usuario->avatar_user;

            $usuario->save();

            return true;
        } else {
            return false;
        }
    }


    public function deleteAccount($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            if ($usuario->type_user === 'CLIENT') {

                $this->deleteAvatar($usuario->id);

                // Eliminar cuenta del cliente
                $usuario->delete();
                return true;
            } elseif ($usuario->type_user === 'COMPANY') {
                $this->deleteAvatar($usuario->id);

                if(!$this->deleteOffersCompany($usuario->id)){
                    return false;
                }

                $usuario->delete();
                return true;
            }
            return false;
        }
    }

    public function getAvatar($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            $avatar = $usuario->avatar_user;
            return empty($avatar) ? null : $avatar;
        } else {
            return null;
        }
    }

    public function updateAvatar($id, $avatar = null)
    {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            $uploadDir = realpath(__DIR__ . '/../uploads/avatars/') . '/';

            if (is_array($avatar) && isset($avatar['name']) && !empty($avatar['name'])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $mimeType = mime_content_type($avatar['tmp_name']);

                if (in_array($extension, $allowedExtensions) && in_array($mimeType, $allowedMimeTypes)) {
                    $newFileName = $id . '.' . $extension;
                    $uploadPath = $uploadDir . $newFileName;

                    if ($usuario->avatar_user && file_exists($uploadDir . basename($usuario->avatar_user))) {
                        unlink($uploadDir . basename($usuario->avatar_user));
                    }

                    if (move_uploaded_file($avatar['tmp_name'], $uploadPath)) {
                        $usuario->avatar_user = URL.'/uploads/avatars/' . $newFileName;
                    } else {
                        return 'Error al mover el archivo subido.';
                    }
                } else {
                    return 'Tipo de archivo no permitido. Solo se permiten archivos JPG, PNG o GIF.';
                }
            }

            $usuario->save();
            return true;
        } else {
            return 'Usuario no encontrado.';
        }
    }

    public function deleteAvatar($id)
    {
        $usuario = ORM::for_table('users')->find_one($id);

        if ($usuario) {
            $uploadDir = realpath(__DIR__ . '/../uploads/avatars/') . '/';

            if ($usuario->avatar_user && file_exists($uploadDir . basename($usuario->avatar_user))) {
                unlink($uploadDir . basename($usuario->avatar_user));

                $usuario->avatar_user = null;
                $usuario->save();

                return ['success' => 'Avatar eliminado exitosamente.'];
            } else {
                return ['error' => 'No se encontró el avatar o el archivo no existe.'];
            }
        } else {
            return ['error' => 'Usuario no encontrado.'];
        }
    }

    public function deleteOffersCompany($companyId)
    {
        try {
            $companyDirectory = $_SERVER['DOCUMENT_ROOT'] . "/uploads/offers/" . $companyId;
    
            if (is_dir($companyDirectory)) {
                $this->deleteFolderRecursively($companyDirectory);
            } else {
                error_log("Directorio no encontrado: " . $companyDirectory);
            }
    
            ORM::for_table('offers')
                ->where('id_company_offer', $companyId)
                ->delete_many();
    
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function deleteFolderRecursively($folderPath)
    {
        $files = array_diff(scandir($folderPath), ['.', '..']);
    
        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
    
            if (is_dir($filePath)) {
                $this->deleteFolderRecursively($filePath);
            } else {
                if (!unlink($filePath)) {
                    error_log("No se pudo eliminar el archivo: " . $filePath);
                }
            }
        }
    
        // Eliminar la carpeta una vez que está vacía
        if (!rmdir($folderPath)) {
            error_log("No se pudo eliminar la carpeta: " . $folderPath);
        }
    }

    private function convertObj($obj)
    {
        return [
            'id' => $obj->id ?? null,
            'name_user' => $obj->name_user ?? null,
            'email_user' => $obj->email_user ?? null,
            'cif_user' => $obj->cif_user ?? null,
            'avatar_user' => $obj->avatar_user ?? null,
            'type_user' => $obj->type_user ?? null,
            'created_user' => $obj->created_user ?? null,
            'updated_user' => $obj->updated_user ?? null
        ];
    }


    public function validarCIF($cif)
    {
        $cifRegex = '/^[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J]$/i';

        if (preg_match($cifRegex, $cif)) {
            $control = 'JABCDEFGHI';
            $sumaPar = 0;
            $sumaImpar = 0;

            for ($i = 1; $i < 8; $i++) {
                $numero = (int) $cif[$i];

                if ($i % 2 == 0) {
                    $sumaPar += $numero;
                } else {
                    $imp = 2 * $numero;
                    if ($imp > 9) $imp = 1 + ($imp - 10);
                    $sumaImpar += $imp;
                }
            }

            $sumaTotal = $sumaPar + $sumaImpar;

            $digitoControl = (10 - ($sumaTotal % 10)) % 10;
            $letraControl = $control[$digitoControl];

            return strtoupper($cif[8]) == $letraControl;
        }
        return false;
    }
}
