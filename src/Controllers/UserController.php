<?php 

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserController extends BaseController
{
	public function register(Request $request, Response $response)
	{
		$rules = [
			'required'	=> [
				['username'],
				['email'],
				['name'],
				['password'],
			],
			'alphaNum'	=> [
				['username'],
			],
			'email'	=> [
				['email'],
			],
			'lengthMin'	=> [
				['username', 6],
				['password', 6],
			],
		];

		$this->validator->rules($rules);

		$this->validator->labels([
			'username'	=> 'Username',
			'email'		=> 'Email',
			'name'		=> 'Name',
			'password'	=> 'Password',
		]);

		if ($this->validator->validate()) {
			$user = new \App\Models\Users\Users($this->db);
			$register = $user->register($request->getParsedBody());

			$findUserAfterRegister = $user->find('id', $register);

			$data = $this->responseDetail(201, 'Register Success, Please Login', $findUserAfterRegister);
		} else {
			$data = $this->responseDetail(400, 'Errors', $this->validator->errors());
		}

		return $data;
	}

	public function login(Request $request, Response $response)
	{
		$user = new \App\Models\Users\Users($this->db);

		$login = $user->find('username', $request->getParam('username'));

		if (empty($login)) {
			$data = $this->responseDetail(401, 'Error', 'Username is not Registered');
		} else {
			$check = password_verify($request->getParam('password'), $login['password']);

			if ($check) {
				$token = new \App\Models\Users\UserToken($this->db);
				$token->setToken($login['id']);

				$getToken = $token->find('user_id', $login['id']);

				$key = [
					'key'	=> $getToken,
				];
				$data = $this->responseDetail(201, 'Login Success', $login, $key);
			} else {
				$data = $this->responseDetail(401, 'Error', 'Wrong Password');
			}
		}

		return $data;
	}

	public function logout(Request $request, Response $response)
	{
		$token = $request->getHeader('Authorization')[0];

		$userToken = new \App\Models\Users\UserToken($this->db);
		$findUser = $userToken->find('token', $token);
		$userToken->delete('user_id',$findUser['user_id']);

		return $this->responseDetail(200, 'Success', 'Logout Success');
	}
}