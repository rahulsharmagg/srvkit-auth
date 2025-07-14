<?php

namespace SrvKit\Auth\Entities;

use DateTime;
use Exception;
use CodeIgniter\Entity\Entity;
use SrvKit\Auth\Models\UserModel;

class Token extends Entity
{
    protected $datamap = [];
    protected $dates   = ['expires_at', 'created_at'];
    protected $casts   = [];

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
