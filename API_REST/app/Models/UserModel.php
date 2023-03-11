<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    
    protected $allowedFields = [
        'custumerName',
        'profilePic',
        'userEmail',
        'phoneNumber',
        'userPassword',
        'status'
    ];

    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    // Antes de insertar llama este callback
    protected $beforeInsert  = [ 'beforeInsert' ];

    protected function beforeInsert(array $user) {
        return $this->updatePasswordWithHashed($user);
    }

    public function findUserByEmailAddress(string $email) {
        $user = $this->asArray()->where([
            'userEmail' =>  $email
        ])->first();

        if (!$user) throw new \Exception('User does not existe for this email');

        return $user;
    }

    // Esta funcion convierte encrypta la contraseña
    private function updatePasswordWithHashed(array $user): array {
        if (isset($user['data']['userPassword'])) {
            $plaintPassword = $user['data']['userPassword'];

            $user['data']['userPassword'] = password_hash($plaintPassword, PASSWORD_BCRYPT);
        }

        return $user;
    }
}