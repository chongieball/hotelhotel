<?php 

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HotelController extends BaseController
{
	public function index(Request $request, Response $response)
	{
		$hotels = new \App\Models\Hotels\Hotels($this->db);

		$getHotel = $hotels->getAll();

		//count total hotels
		$countHotels = count($getHotel);

		if ($getHotel) {
			$page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
			$get = $hotels->paginate($page, $getHotel, 10);
			if ($get) {
				$data = $this->responseDetail(200, 'Data Available', $get, $this->paginate($countHotels, 10, $page, ceil($countHotels/10)));
			} else {
				$data = $this->responseDetail(404, 'Error', 'Data Not Found');
			}
		} else {
			$data = $this->responseDetail(204, 'Success', 'No Content');
		}
		
		return $data;
	}

	public function add(Request $request, Response $response)
	{
		$rules = [
			'required'	=> [
				['name'],
			]
		];

		$this->validator->rules($rules);

		$this->validator->labels([
			'name'	=> 'Hotel Name',
		]);

		if ($this->validator->validate()) {
			$hotel = new \App\Models\Hotels\Hotels($this->db);
			$add = $hotel->add($request->getParsedBody());

			$findHotelAfterAdd = $hotel->find('id', $add);

			$data = $this->responseDetail(201, 'Success Add Hotel', $findHotelAfterAdd);
		} else {
			$data = $this->responseDetail(400, 'Errors', $this->validator->errors());
		}

		return $data;
	}

	public function update(Request $request, Response $response, $args)
	{
		$hotel = new \App\Models\Hotels\Hotels($this->db);
		$findHotel = $hotel->find('id', $args['id']);

		if ($findHotel) {
			$hotel->update($request->getParsedBody(), 'id', $args['id']);
			$afterUpdate = $hotel->find('id', $args['id']);

			$data = $this->responseDetail(200, 'Success Update Data', $afterUpdate);
		} else {
			$data = $this->responseDetail(404, 'Error', 'Data Not Found');
		}

		return $data;
	}

	public function delete(Request $request, Response $response, $args)
	{
		$hotel = new \App\Models\Hotels\Hotels($this->db);
		$findHotel = $hotel->find('id', $args['id']);

		if ($findHotel) {
			$hotel->delete('id', $args['id']);

			$data = $this->responseDetail(200, 'Success', 'Data Has Been Delete');
		} else {
			$data = $this->responseDetail(404, 'Error', 'Data Not Found');
		}

		return $data;
	}

	public function searchByDate(Request $request, Response $response)
	{
		$hotels = new \App\Models\Hotels\Hotels($this->db);
		$findHotel = $hotels->findByDate($request->getQueryParam('date'));

		if ($findHotel) {
			$page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');

			$get = $hotels->paginate($page, $findHotel, 10);

			//count total hotels
			$countHotels = count($get);

			if ($get) {
				$data = $this->responseDetail(200, 'Data Available', $get, $this->paginate($countHotels, 10, $page, ceil($countHotels/10)));
			} else {
				$data = $this->responseDetail(404, 'Error', 'Data Not Found');
			}
		} else {
			$data = $this->responseDetail(204, 'Success', 'No Content');
		}

		return $data;
	}
}