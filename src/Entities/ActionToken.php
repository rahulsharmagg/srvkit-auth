<?php

namespace SrvKit\Auth\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;
use DateTime;
use Exception;
use SrvKit\Auth\Models\UserModel;

class ActionToken extends Entity
{
    protected $datamap = [];
    protected $dates   = ['expires_at', 'used_at', 'created_at'];
    protected $casts   = [
        'used' => 'bool'
    ];

    public function isExpired(): bool
    {
        if(!isset($this->attributes['expires_at'])){
            throw new Exception('No expires_at filed found in this data.');
        }

        $now = new DateTime();
        return $this->expires_at < $now;
    }

    public function getUser(): User
    {
        $userModel = new UserModel();
        return $userModel->find($this->user_id);
    }
}