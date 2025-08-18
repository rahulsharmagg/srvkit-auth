<?php

namespace SrvKit\Auth\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;
use SrvKit\Auth\Helpers\AvatarHelper;

class User extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'is_email_verified' => 'bool'
    ];

    public function getSanitized(): array
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'verifedEmail' => $this->is_email_verified,
            'avatar' => $this->avatarURL
        ];
    }

    public function getAvatarURL(): string
    {
        return AvatarHelper::toUrl($this->avatar);
    }
}