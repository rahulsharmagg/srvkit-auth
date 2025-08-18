<?php

namespace SrvKit\Auth\Validation;

class SignupRules
{
    public function valid_username(string $value, ?string &$error = null): bool
    {
        $isValid = (bool) preg_match('/^(?=.*[a-z])[a-z0-9._]+$/', $value);

        if(!$isValid) {
            $error = 'Invalid Username';
        }

        return $isValid;
    }
}
