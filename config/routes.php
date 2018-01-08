<?php
// Комментирую как хочу, проект некомерческий, идите в пень
use raelgc\view\Template;
use NoahBuscher\Macaw\Macaw;

Macaw::get('/', function() {
	// Пробрасываем шутейку для апишки
});

Macaw::post('/auth/login', function() {
	// Авторизуем
	if (isset($_POST['login']) && isset($_POST['pass'])) {
		// Чистим, красим, поливаем..
		$send_login = htmlspecialchars($_POST['login']);
		$send_pass = htmlspecialchars($_POST['pass']);
		$send_login = trim($send_login);
		$send_pass = trim($send_pass);

		// Достаем соль и щедро посыпаем
		$config = parse_ini_file('../config/config.ini');
		$salt = $config['salt'];
		$send_pass = sha1($salt . $send_pass);

		$db = DB::getInstance();
		$db->table('users')->where([['login', $send_login], ['pass', $send_pass]])->get();
		$result = $db->getCount();
		if ($result == 1) {
			// Если ок - генерируем-сохраняем-выдаем токен
			// Хранится в формате - токен-логин-время последней активности-время истечения
			echo 'Токен 09345340953409593045';
		} else {
			echo 'DEAD_TOKEN';
		}
	}
});

Macaw::post('/auth/register', function() {
	// Регистрируем
	// Потом, все потом) 
	$db = DB::getInstance();
});

// Кабинет пользователя
require_once '../game/Cabinet.php';

// + Работа с миром (картой)

// + Работа с событиями

// + Генерация сущностей

// + Точка божественного рандома объектов

// + Функции зданий

// + Рейтинги + награды за рейтинги

// + Рандом храмов

Macaw::error(function() {
    echo 'Тут будет 404 страница с рожами :)';
});
Macaw::dispatch();