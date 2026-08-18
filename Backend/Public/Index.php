<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/Core/Response.php';
require_once __DIR__ . '/../App/Core/Auth.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

//========================== CORS ========================================

Response::cors();

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

//========================= ROTAS ========================================

$routes = require_once '../Routes/Api.php';

$pdo = Database::getConnection();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$uri = strtok($uri, '?');

$routeFound = false;

foreach ($routes[$method] ?? [] as $routePath => $action) {
    
    // Suporte a rotas dinâmicas como /api/tasks/{id}
    $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routePath);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $uri, $matches)) {
        $routeFound = true;
        
        $controllerName = $action[0];
        $methodName     = $action[1];

        // Inclui o Controller
        require_once "../App/Controllers/{$controllerName}.php";

        $controller = new $controllerName($pdo);

        // Dados da requisição (JSON ou form)
        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        // Chama o método do controller
        if (!empty($params = array_slice($matches, 1))) {
            // Rotas com parâmetro (ex: /api/tasks/123)
            $controller->$methodName($params[0], $data);
        } else {
            // Rotas normais (login, register, index, etc)
            $controller->$methodName($data);
        }

        break;
    }
}

if (!$routeFound) {
    Response::json([
        'success' => false,
        'message' => 'Rota não encontrada: ' . $uri
    ], 404);
}