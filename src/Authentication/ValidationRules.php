<?php

namespace SrvKit\Auth\Authentication;

class ValidationRules{

	/**
	 * Validation rule for login
	 * @return array
	 */
	public static function forLogin(): array
	{
		return array(
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
		);
	}

	/**
	 * Validation rules for signup
	 * @return array
	 */
	public static function forSignup(): array
	{
		return array(
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
		);
	}

	/**
	 * Validation rule for new password
	 * @return array
	 */
	public static function forNewPassword(): array
	{
		return array(
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
		);
	}
}