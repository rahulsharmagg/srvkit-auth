<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SrvKit\Auth\Config;


use SrvKit\Auth\Filters\TemplinkFilter;

class Registrar
{
    /**
     * Registers the SrvKit filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'verifytemp'     => TemplinkFilter::class,
            ],
        ];
    }

    public static function Validation(): array
        {
            return [
                'login' => [
                    'username' => [
                        'label' => 'Username',
                        'rules' => 'required|min_length[4]|max_length[50]',
                        'errors' => [
                            'required' => 'A username is required to continue.',
                            'min_length' => 'Too short — username must have 3 or more characters.',
                            'max_length' => 'Too long — username can be up to 50 characters only.',
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

    
}