<?php
// Начинаем свистопляску
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin');
header('Content-Type: application/json');
require_once '../core/App.php';