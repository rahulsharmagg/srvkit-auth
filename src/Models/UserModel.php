<?php

namespace SrvKit\Auth\Models;

use CodeIgniter\Model;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Helpers\AvatarHelper;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = User::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 
        'username', 
        'email', 
        'avatar', 
        'password', 
        'role', 
        'requested_role', 
        'is_role_approved', 
        'is_email_verified',
        'email_verification_date',
        'role_approved_date',
        'requested_role_date'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => [
            'label' => 'Name',
            'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[A-Za-z\s\.\'-]+$/]',
        ],
        'username' => [
            'label' => 'Username',
            'rules' => 'required|regex_match[/^[A-Za-z0-9\_]+$/]|min_length[4]|max_length[20]|is_unique[users.username,id,{id}]',
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email'
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required|min_length[6]',
        ],
        'role' => [
            'label' => 'Role',
            'rules' => 'permit_empty|in_list[member,author,admin,owner]',
        ],
        'requested_role' => [
            'label' => 'Requested Role',
            'rules' => 'permit_empty|in_list[author,admin]',
        ],
    ];
    protected $validationMessages   = [
        'name' => [
            'regex_match' => 'Name can only contain letters, spaces, apostrophes, hyphens, and periods.',
        ],
        'username' => [
            'is_unique' => 'This username is already taken.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'avatar'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword', 'avatar'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash the password before saving it to the database.
     *
     * @param array $data
     * @return array
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * Generate avatar based on name
     * @param  array  $data
     * @return array
     */
    protected function avatar(array $data)
    {
        log_message('debug', 'Calling Avatar');
        if (isset($data['data']['name'])) {
            $data['data']['avatar'] = AvatarHelper::generateAvatar($data['data']['name']);
        }

        return $data;
    }
}
