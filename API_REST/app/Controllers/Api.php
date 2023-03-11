<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    public function index() {}

    public function login() {
    // Programando las reglas de debe seguir para validar los campos
        try {
            //code...
            $rules = [
                'userEmail'     =>  'required|valid_email|min_length[6]',
                'userPassword'  =>  'required|min_length[8]|max_length[255]'
            ];

            $input = $this->request->getPost();
            // return $this->getResponse([
            //     'code' => 2,
            //     'message'   =>  $input
            // ]);
    
            // Aplicando las reglas
            if (!$this->validateRequest($input, $rules)) {
                $errors = $this->validator->getErrors();
                // Return by default
                return $this->getResponse([
                    'code' => 0,
                    'message'   =>  $errors
                ], ResponseInterface::HTTP_BAD_REQUEST);
            }
            // Todo bien
            return $this->getJWTForUSer($input['userEmail'], ResponseInterface::HTTP_OK, 'login', $input['userPassword']);
        } catch (\Exception $e) {
            return $this->getResponse([
                'code'  =>  0,
                'message' => $e->getMessage(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    public function register() {
        // Programando las reglas de debe seguir para validar los campos
        try {
            //code...
            $rules = [
                'userName'      =>  'required',
                'userPhoneNumber' =>  'required',
                'userEmail'     =>  'required|valid_email|is_unique[users.userEmail]',
                'userPassword'  =>  'required|min_length[8]|max_length[255]',
                'userProfilePic' =>  'required'
            ];
    
            $input = $this->request->getPost();
    
            // Caplicando las reglas
            if (!$this->validateRequest($input, $rules)) {
                $errors = $this->validator->getErrors();
                $code = 0;
                // Verificando si el error es del emaily si es que ya existe
                if (isset($errors['userEmail'])) $code = 2;
                // Return by default
                return $this->getResponse([
                    'code' => $code,
                    'message'   =>  $errors
                ]);
            }
            // Decode base64
            $imageDecode = base64_decode(str_replace('data:image/png;base64,', '', $input['userProfilePic']));
            $name = substr(number_format(time() * rand(),0,'',''),0,10). '.png';

            file_put_contents('uploads/'.$name, $imageDecode); // Guardando la imagen
            // Instanciando el usuario
            $userModel = new UserModel;
            // Guardando la informacion validada
            $userModel->save([
                // Deberia de ser customerName*
                'custumerName'  =>  $input['userName'],
                'phoneNumber'  =>  $input['userPhoneNumber'],
                'userEmail'  =>  $input['userEmail'],
                'userPassword'  =>  $input['userPassword'],
                'profilePic'  =>  $name
            ]);
    
            return $this->getJWTForUSer($input['userEmail'], ResponseInterface::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->getResponse([
                'code'  =>  0,
                'message' => $e->getMessage(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    private function getJWTForUSer(string $email, int $responseCode = ResponseInterface::HTTP_OK, string $type = 'register', string $password = null) {
        try {
            $userModel = new UserModel;

            $user = $userModel->findUserByEmailAddress($email);
            // Validando que la contraseña no este incorrecta, simpre que el usuario se valla a loguear
            if ($type == 'login') {
                if (!\password_verify($password, $user['userPassword'])) {      
                    return $this->getResponse([
                        'code'  =>  2
                    ]);
                }
            }
            // Quitando la contraseña y otros campos
            unset($user['id']);
            
            unset($user['userPassword']);
            unset($user['created_at']);
            unset($user['updated_at']);
            // Importo el helper
            helper('jwt');
            // Envio la respuesta
            return $this->getResponse([
                'token' =>  getSignedJWT($email),
                'User'  =>  $user,
                'code'  =>  1,
                'message' => 'User authenticate successfully',
            ]);
        } catch (\Exception $e) {
            return $this->getResponse([
                'code'  =>  3,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
