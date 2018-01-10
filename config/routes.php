<?php
// Комментирую как хочу, проект некомерческий, идите в пень
use Routers\Router;

Router::get('/', function() {
	// Главная
	echo 'API открыта для всех и каждого, а документацию не завезли :)';
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
			$ip = $_SERVER['REMOTE_ADDR'];
			$current = $db->table('ra_tokens')->where([['login', $send_login], ['ip', $ip]])->get();
			if ($db->getCount() > 0) {
				foreach ($current as $row) {
            		$curr_token = $row->token;
            	}
				// Выдаем уже готовый токен
				echo $curr_token;
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
            	    'login' => $send_login,
            	    'ip' => $new_ip,
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
	$token = trim(htmlspecialchars($_POST['token']));
	$login = trim(htmlspecialchars($_POST['login']));
	$db = DB::getInstance();
	$db->table('ra_tokens')->where([['login', $login], ['token', $token]])->get();
	if ($db->getCount() > 0) {
		echo 'access';
	} else {
		echo 'false';
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