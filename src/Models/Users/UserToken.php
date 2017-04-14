<?php 

namespace App\Models\Users;

use App\Models\BaseModel;

class UserToken extends BaseModel
{
	protected $table = 'user_token';
	protected $column = ['user_id', 'token', 'login_at', 'expire_at'];

	public function setToken($id)
	{
		$data = [
			'user_id'	=> $id,
			'token'		=> md5(openssl_random_pseudo_bytes(8)),
			'login_at'	=> date('Y-m-d H:i:s'),
			'expire_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
			];

		$findUserId = $this->find('user_id', $id);

		if($findUserId || $findUserId['expire_at'] < strtotime("now")) {
			$data = array_reverse($data);
			$pop = array_pop($data);

			$this->update($data, 'user_id', $id);
		} else {
			$this->create($data);
		}
	}
}