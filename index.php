<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode('/', $_SERVER['REQUEST_URI']);

// Check first the array.
if ($parts[1] == 'workers') {
    array_splice($parts, 0, 1);
} else if ($parts[2] == 'workers') {
    array_splice($parts, 0, 2);
}

if ($parts[0] != 'workers') {
    http_response_code(404);
    exit;
}

$id = $parts[1] ?? null;

$database = new Database('localhost', 'shiftsplanner', 'root', '');
$gateway = new WorkerGateway($database);
$controller = new WorkerController($gateway);
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);