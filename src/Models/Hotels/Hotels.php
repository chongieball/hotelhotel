<?php 

namespace App\Models\Hotels;

use App\Models\BaseModel;

class Hotels extends BaseModel
{
	protected $table = 'hotels';
	protected $column = ['id', 'name', 'rooms', 'available_at', 'expire_at'];

	public function add(array $data)
	{
		$data = [
			'name'			=> $data['name'],
			'rooms'			=> !$data['rooms'] ? 1 : $data['rooms'],
			'available_at'	=> !$data['available_at'] ? date('Y-m-d H:i:s') : $data['available_at'],
			'expire_at'		=> !$data['expire_at'] ? date('Y-m-d H:i:s', strtotime('+1 year')) : $data['expire_at']
		];

		$this->create($data);

		return $this->db->lastInsertId();

	}

	public function findByDate($date)
	{
		$qb = $this->db->createQueryBuilder();
		$get = $qb->select($this->column)
		   ->from($this->table)
		   ->where('available_at <= :date')
		   ->andWhere('expire_at >= :date')
		   ->setParameter(':date', $date)
		   ->execute();

		return $get->fetchAll();
	}
}