<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class UserModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "email",
        "username",
        "name",
        "password",
        "role_id",
        "hash",
        "verified_at",
        "deleted_at",
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [
        'hashPassword', 'generateHash'
    ];
    protected $afterInsert = [
        'sendConfirmEmail'
    ];
    protected $beforeUpdate = [
        'hashPassword'
    ];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * @param array $data
     * @return array
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function generateHash($data): array
    {
        if (!isset($data['data']['hash'])) {
            helper('text');

            $data['data']['hash'] = random_string('alpha', 32);

            return $data;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    public function sendConfirmEmail(array $data)
    {
        $email = Services::email();

        $email->setFrom('gromret@gmail.com', 'CodeIgniter Project');
        $email->setTo($data['data']['email']);

        $email->setSubject('Email Test');
        $email->setMessage(view('emails/confirm-email', [
            'name' => $data['data']['name'],
            'hash' => $data['data']['hash'],
        ]));

        $email->send();
    }
}
