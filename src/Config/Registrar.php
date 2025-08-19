<?php

declare(strict_types=1);

namespace SrvKit\Auth\Config;

use SrvKit\Auth\Collectors\Auth;
use SrvKit\Auth\Filters\AccessFilter;
use SrvKit\Auth\Filters\LoggedInFilter;
use SrvKit\Auth\Filters\TemplinkFilter;
use SrvKit\Auth\Filters\UserFilter;
use SrvKit\Auth\Validation\LoginRules;
use SrvKit\Auth\Validation\SignupRules;

class Registrar
{
    /**
     * Registers the SrvKit filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'verifytemp' => TemplinkFilter::class,
                'user'       => UserFilter::class,
                'access'     => AccessFilter::class,
                'loggedin'   => LoggedInFilter::class
            ],
        ];
    }

    public static function Validation(): array
        {
            return [
                'ruleSets' => [
                    SignupRules::class,
                    LoginRules::class
                ],

                /**
                 * Login Rule: Validates username and password
                 */
                'login' => [
                    'username' => [
                        'label' => 'Username',
                        'rules' => 'required|username_or_email|min_length[3]',
                        'errors' => [
                            'required' => 'A {field} is required to continue.',
                            'min_length' => 'Too short — username must have 3 or more characters.',
                        ]
                    ],
                    'password' => [
                        'label' => 'Password',
                        'rules' => 'required|min_length[6]',
                        'errors' => [
                            'required' => 'Password must be at least 6 characters long.',
                            'min_length' => 'Password must be at least 6 characters long.',
                        ]
                    ]
                ],

                /**
                 * Signup Rule: Validates name, username, password, email etc.
                 */
                'signup' => [
                    'name' => [
                        'label' => 'Name',
                        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[A-Za-z\s\.\'-]+$/]',
                        'errors' => [
                            'required' => '{field} is required to continue signup.',
                            'min_length' => 'Too short - name must have 2 or more characters.',
                            'regex_match' => 'Invalid name `{value}`.'
                        ]
                    ],
                    'username' => [
                        'label' => 'Username',
                        'rules' => 'required|valid_username|is_unique[users.username,id,{id}]',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'rules' => 'required|min_length[4]|max_length[100]',
                    ],
                    'email' => [
                        'label' => 'Email',
                        'rules' => 'required|valid_email'
                    ],
                    'role' => [
                        'label' => 'Role',
                        'rules' => 'permit_empty|in_list[member,author,admin,owner]',
                    ]
                ],

                /**
                 * Used by update new password
                 */
                'new-password' => [
                    'password' => [
                        'label' => 'New Password',
                        'rules' => 'required|min_length[6]|regex_match[/^(?=.*\d)(?=.*[a-z])(?=.*[\W_])(?!.*\s).+$/]',
                        'errors' => [
                            'required' => '{field} is required.',
                            'min_length' => 'The {field} must be at least 6 characters long.'
                        ]
                    ],
                    'passconf' => [
                        'label' => 'Confirm New Password',
                        'rules' => 'matches[password]',
                        'errors' => [
                            'matches' => 'The {field} is required & must match the new password.'
                        ]
                    ]
                ]
            ];
        }

    public static function Toolbar(): array
    {
        return [
            'collectors' => [
                Auth::class
            ],
        ];
    }

    public static function Publishers(): array
    {
        return [
            'auth' => \SrvKit\Auth\Publishers\AuthPublisher::class
        ];
    }
}
