<?php 

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthToken extends BaseMiddleware
{
	public function __invoke($request, $response, $next)
	{
		$token = $request->getHeader('Authorization')[0];

		$userToken = new \App\Models\Users\UserToken($this->container->db);

		$findUser = $userToken->find('token', $token);

		$now = date('Y-m-d H:i:s');

		if (!findUser || $findUser['expire_at'] < $now ) {
			$data['status'] = 401;
			$data['message'] = 'Not Authorized';

			return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
		}

		$response = $next($request, $response);

		//tambah waktu token
		$addTime['expire_at'] = date('Y-m-d H:i:s', strtotime($findUser['expire_at'].'+2 hour'));

		$userToken->update($addTime, 'user_id', $findUser['user_id']);

		return $response;
	}
	
}