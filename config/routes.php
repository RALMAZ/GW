<?php
use View\Tpl;
use Routers\Router;

Router::get('/', function() {
	// Главная
	print_r('42');
	
});

Router::post('/auth/login', function() {
	// Авторизуем
	if (isset($_POST['login']) && isset($_POST['pass'])) {
		// Чистим, красим, поливаем..
		$send_login = trim(htmlspecialchars($_POST['login']));
		$send_pass = trim(htmlspecialchars($_POST['pass']));

		// Достаем соль и щедро посыпаем
		$config = parse_ini_file('../config/config.ini');
		$salt = $config['salt'];
		$send_pass = md5($salt.$send_pass);

		$db = DB::getInstance();
		$getid = $db->table('ra_users')->where([['login', $send_login], ['pass', $send_pass]])->get();
		if ($db->getCount() > 0) {
			foreach ($getid as $ro) {
            	$id_user = $ro->id;
            }
			// Проверяем есть ли уже токен
			$ip = $_SERVER['REMOTE_ADDR'];
			$current = $db->table('ra_tokens')->where([['login', $send_login], ['ip', $ip]])->get();
			if ($db->getCount() > 0) {
				foreach ($current as $row) {
            		$curr_token = $row->token;
            	}
				// Выдаем уже готовый токен
				$tpl = new Tpl("../tpl/token.html");
				$tpl->LOGIN = $send_login;
    			$tpl->BALANCE = '1';
    			$tpl->block("USERINFO");

				$tpl->TOKEN = $curr_token;
				$tpl->show();
			} else {
				// Или выдаем новый

				// Месим строку токена
				$token_time = time();
				$token_rand1 = rand(1,8999);
				$token_rand2 = rand(245,6783);
				$token_mix = $token_rand1.$token_time.$token_rand2;
				$newtoken = 'token_'.md5($token_mix);

				// Хранится в формате - токен-логин-время последней активности
				$tokenExpired = $config['token_expired']; // Время истечения токена от последней активности в часах (использую позже)
				$last_active = time(); //Y-m-d H:i:s потом
				$new_ip = $_SERVER['REMOTE_ADDR'];
	
				$db->insert('ra_tokens',
            	[
            	    'token' => $newtoken,
            	    'id_user' => $id_user,
            	    'login' => $send_login,
            	    'ip' => $new_ip,
            	    'last_active' => $last_active
            	]);
	
				$tpl = new Tpl("../tpl/token.html");
				$tpl->LOGIN = $send_login;
    			$tpl->BALANCE = '1';
    			$tpl->block("USERINFO");

				$tpl->TOKEN = $newtoken;
				$tpl->show();
			}
			
		} else {
			$tpl = new Tpl("../tpl/token.html");
			$tpl->TOKEN = 'DEAD_TOKEN';
			$tpl->show();
		}
	}
});

Router::post('/auth/register', function() {
	// Регистрируем
	// Потом, все потом) 
	$db = DB::getInstance();
	// Как поля нужны хм

	/*
		Однозначно вход по емейлу, для востановления
		Логин и емейл вместе не светится
		При логинке надо заблочить доступ если попыток больше 5
	*/
});

Router::post('/auth/exit', function() {
	// При выходе получаем токен
	$token = trim(htmlspecialchars($_POST['token']));
	$ip = $_SERVER['REMOTE_ADDR'];
	$db = DB::getInstance();

	$token_data = $db->table('ra_tokens')->where([['token', $token], ['ip', $ip]])->get();

	if ($db->getCount() > 0) {
		// Если такой токен существует получаем его инфомрацию
		foreach ($token_data as $row) {
			$id_token = $row->id;
			$id_user = $row->id_user;
          	$login = $row->login;
          	$last_active = $row->last_active;
        }

        // Перед удалением токена сохраним пользователю время его последней активности
		$db->update('ra_users',
		[
			'last_active' => $last_active
		],$id_user);

		// Удаляем токен
		$db->delete('ra_tokens',$id_token);
	}
});

Router::post('/auth/token', function() {
	// Получаем токен
	$token = trim(htmlspecialchars($_POST['token']));
	$ip = $_SERVER['REMOTE_ADDR'];
	$last_active = time();
	$db = DB::getInstance();

	// Проверяем токен
	$token = $db->table('ra_tokens')->where([['token', $token], ['ip', $ip]])->get();
	if ($db->getCount() > 0) {
		foreach ($token as $row) {
			$id_token = $row->id;
        }

		$db->update('ra_tokens',
		[
			'last_active' => $last_active
		],$id_token);

		echo 'access';
	} else {
		echo 'denied';
	}
});

// Кабинет пользователя
require_once '../game/Cabinet.php';

// + Работа с миром (картой)

// + Работа с событиями

// + Генерация сущностей - отряды могут носить добычу

// + Точка божественного рандома объектов

// + Функции зданий

// + Рейтинги + награды за рейтинги

// + Рандом храмов

// + Драки между отрядами которых слишком много в одном месте

// + Боты - главы Орды со своими войсками, главы Альянса, главы нейтралов

// + Союзы со своими крепостями и различными постройками в них

Router::error(function() {
    echo 'Тут будет 404 страница с рожами :)';
});
Router::dispatch();