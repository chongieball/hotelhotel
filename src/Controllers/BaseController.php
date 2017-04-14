<?php

namespace App\Controllers;

use Slim\Container;

abstract class BaseController
{
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function __get($property)
	{
		if ($this->container->{$property}) {
			return $this->container->{$property};
		}
	}

	public function responseWithJson(array $data)
	{
		return $this->response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
	}

	public function responseDetail($status, $message, $data, array $meta = null)
	{
		$response = [
			'status'	=> $status,
			'message'	=> $message,
			'data'		=> $data,
			'meta'		=> $meta,
		];

		if ($meta == null) {
			array_pop($response);
		}

		return $this->responseWithJson($response);
	}

	public function paginate($total, $perPage, $currentPage, $totalPage)
	{
		return [
			'paginate'	=> [
				'total_data'	=> $total,
				'per_page'		=> $perPage,
				'current_page'	=> $currentPage,
				'total_page'	=> $totalPage,
			],
		];
	}
}
