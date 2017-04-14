<?php 

$app->group('/api', function() use ($app, $container) {
	$app->post('/register', 'App\Controllers\UserController:register')->setName('user.register');

	$app->post('/login', 'App\Controllers\UserController:login')->setName('user.login');

	$app->group('', function() use ($app, $container) {
		$app->get('[/]', 'App\Controllers\HotelController:index')->setName('hotel.index');

		$app->get('/search', 'App\Controllers\HotelController:searchByDate')->setName('hotel.search');

		$app->post('/logout', 'App\Controllers\UserController:logout')->setName('user.logout');

		$app->group('', function() use ($app,$container) {
			$app->post('/addhotel', 'App\Controllers\HotelController:add')->setName('hotel.add');

			$app->put('/updatehotel/{id}', 'App\Controllers\HotelController:update')->setName('hotel.update');

			$app->delete('/deletehotel/{id}', 'App\Controllers\HotelController:delete')->setName('hotel.delete');
		})->add(new \App\Middlewares\AdminMiddleware($container));
	})->add(new \App\Middlewares\AuthToken($container));
});