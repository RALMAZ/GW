<?php
// Комментирую как хочу, проект некомерческий, идите в пень
use Routers\Router;

Router::get('/', function() {
	// Пробрасываем шутейку для апишки
	echo 'Шутейка';
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
		$db->table('ra_users')->where([['login', $send_login], ['pass', $send_pass]])->get();
		if ($db->getCount() > 0) {
			// Проверяем есть ли уже токен
			$current = $db->table('ra_tokens')->where('login', $send_login)->get();
			if ($db->getCount() > 0) {
				foreach ($current as $row) {
            		$curr_token = $row->token;
            	}
				// Выдаем уже готовый токен
				echo $curr_token;
			} else {
				// Или выдаем новый
				$newtoken = uniqid('token_');
				// Хранится в формате - токен-логин-время последней активности
				$tokenExpired = $config['token_expired']; // Время истечения токена от последней активности в часах
				$last_active = time(); //Y-m-d H:i:s
	
				$db->insert('ra_tokens',
            	[
            	    'token' => $newtoken,
            	    'login' => $send_login,
            	    'last_active' => $last_active
            	]);
	
				echo $newtoken;
			}
			
		} else {
			echo 'UNDEAD_TOKEN';
		}
	}
});

Router::post('/auth/register', function() {
	// Регистрируем
	// Потом, все потом) 
	$db = DB::getInstance();
});

Router::post('/auth/exit', function() {
	// Убиваем токен и шлем ответ на очистку локалсторейдж и vuex, разлогин
	$db = DB::getInstance();
});

Router::post('/auth/token', function() {
	$token = $_POST['token'];
	$login = $_POST['login'];
	$db = DB::getInstance();
	$db->table('ra_tokens')->where([['login', $login], ['token', $token]])->get();
	if ($db->getCount() > 0) {
		echo 'Токен активен';
	} else {
		echo 'Токен неактивен';
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