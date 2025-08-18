<?php

namespace SrvKit\Auth\Config;

use CodeIgniter\Events\Events;
use SrvKit\Auth\Authentication\EmailProvider;

// When user_registered event is triggered, run this
Events::on('user_registered', function ($user) {
    EmailProvider::sendSignUpReport($user->email);
    // EmailProvider::sendEmailVerificationLink($user->email);
});

Events::on('login', function ($user) {
    $data = [

    ];
    EmailProvider::sendLoginReport($user->email, $data);
});