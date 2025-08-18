<?php

namespace SrvKit\Auth\Validation;

class LoginRules
{
    /**
     * Validates if the input is either a valid email or username.
     * Returns true if valid and sets data for credentials.
     *
     * @param string|null $input The input string to validate
     * @param string|null $error The error message (passed by reference)
     * @param array $data The data array to modify with guessed type
     * @return bool
     */
    public function username_or_email(string $value, ?string &$error = null, array $data = []): bool
    {
        // Trim input to remove whitespace
        $value = trim($value ?? '');

        // Check if input is empty
        if (empty($value)) {
            $error = 'The username is required.';
            return false;
        }

        // Check if input matches email pattern
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $data['email'] = $value;
            $data['username'] = null;
            return true;
        }

        // Basic username pattern: alphanumeric, underscores, dots, 3-30 characters
        if (preg_match('/^(?=[a-z])([a-z0-9._]){3,50}$/', $value)) {
            $data['username'] = $value;
            $data['email'] = null;
            return true;
        }

        // If neither pattern matches, return false with error
        $error = 'The username must be a valid email or username (3-30 characters, alphanumeric, underscores, or dots).';

        return false;
    }
}
