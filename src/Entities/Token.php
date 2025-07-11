<?php

namespace SrvKit\Auth\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

class Token extends Entity
{
    protected $datamap = [];
    protected $dates   = ['expires_at', 'created_at'];
    protected $casts   = [];
}
