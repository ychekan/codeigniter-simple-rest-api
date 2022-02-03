<?php

namespace App\Models;

use CodeIgniter\Events\Events;
use CodeIgniter\Model;
use App\Entities\User;
use Exception;
use Fluent\Auth\Contracts\UserProviderInterface;
use Fluent\Auth\Contracts\VerifyEmailInterface;
use Fluent\Auth\Traits\UserProviderTrait;
use Tatter\Relations\Traits\ModelTrait;

class UserModel extends Model implements UserProviderInterface
{
    use UserProviderTrait;
    use ModelTrait;

    protected $DBGroup = 'default';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $with = 'roles';
    /**
     * The format that the results should be returned as.
     * Will be overridden if the as* methods are used.
     *
     * @var User
     */
    protected $returnType = User::class;
    protected $useAutoIncrement = true;
    protected $insertID = 0;

    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [
        "email",
        "username",
        "name",
        "role_id",
        "hash",
        "verified_at",
        "deleted_at",
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'date';
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
        'hashPassword', 'generateHash'
    ];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public $has_one = [
        // define the relationship to the boss
        'roles' => array(
            'class' => 'RolesModel',
        )
    ];


    /**
     * @param array $data
     * @return array
     */
    public function hashPassword(array $data): array
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }
        $data['data']['password'] = $this->hashPasswordString($data['data']['password']);

        return $data;
    }

    /**
     * @param $str
     * @return string
     */
    public function hashPasswordString($str): string
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }

    /**
     * @param array $data
     * @return array
     */
    public function generateHash(array $data = []): array
    {
        if (isset($data['data']['verified_at'])) {
            return $data;
        }
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
        if (!isset($data['data']['verified_at'])) {
            Events::trigger(
                VerifyEmailInterface::class,
                $data['data']['email'],
                $data['data']['name'],
                $data['data']['hash']
            );
        }
    }

    /**
     * @param string $emailAddress
     * @return array|object
     * @throws Exception
     */
    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) {
            throw new Exception('User does not exist for specified email address');
        }
        return $user;
    }

    /**
     * @param string $hash
     * @return array|object
     * @throws Exception
     */
    public function findUserByHash(string $hash)
    {
        $user = $this
            ->asArray()
            ->where(['hash' => $hash])
            ->first();

        if (!$user) {
            throw new Exception('User does not exist for specified hash');
        }
        return $user;
    }

    /**
     * @param string $hash
     * @return bool
     * @throws Exception
     */
    public function existUserByHash(string $hash): bool
    {
        return !!$this
            ->asArray()
            ->where(['hash' => $hash])
            ->first();
    }
}
