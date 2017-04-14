<?php 

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AdminMiddleware extends BaseMiddleware
{
	public function __invoke($request, $response, $next)
	{
		$token = $request->getHeader('Authorization')[0];

		$userToken = new \App\Models\Users\UserToken($this->container->db);
		$findToken = $userToken->find('token', $token);

		$users = new \App\Models\Users\Users($this->container->db);
		$findUser = $users->find('id', $findToken['user_id']);

		if (!$findUser || $findUser['is_admin'] == 0) {
			$data['status'] = 401;
			$data['message'] = 'You are not Admin';

			return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
		}

		$response = $next($request, $response);

		return $response;
	}
}