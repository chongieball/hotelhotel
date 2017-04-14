<?php 

namespace App\Models\Users;

use App\Models\BaseModel;

class Users extends BaseModel
{
	protected $table = 'users';
	protected $column = ['id', 'username', 'email', 'name', 'password', 'is_admin'];

	public function register(array $data)
	{
		$data = [
			'username'	=> $data['username'],
			'email'		=> $data['email'],
			'name'		=> $data['name'],
			'password'	=> password_hash($data['password'], PASSWORD_BCRYPT),
			];
		$this->create($data);

		return $this->db->lastInsertId();
	}

}