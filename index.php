<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$allowed_gateways = [
    1 =>'workers',
    2 =>'shifts'
];

$parts = explode('/', $_SERVER['REQUEST_URI']);

// Check first the allowed gateways.
if (array_search($parts[1], $allowed_gateways) >= 1) {
    array_splice($parts, 0, 1);
} else if (array_search($parts[2], $allowed_gateways) >= 1) {
    array_splice($parts, 0, 2);
}

if (array_search($parts[0], $allowed_gateways) === false) {
    http_response_code(404);
    exit;
}

$id = $parts[1] ?? null;

$database = new Database('localhost', 'shiftsplanner', 'root', '');

switch ($parts[0]) {
    case 'workers':
        $gateway = new WorkerGateway($database);
        $controller = new WorkerController($gateway);
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
        break;

    case 'shifts':
        $gateway = new ShiftGateway($database);
        $controller = new ShiftController($gateway);
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
        break;
}