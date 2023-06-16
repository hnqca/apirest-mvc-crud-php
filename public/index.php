<?php

    date_default_timezone_set('America/Sao_Paulo');

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');

    require_once __DIR__ . '/../vendor/autoload.php';

    use CoffeeCode\Router\Router;

    use App\Helpers\Env;
    use App\Controllers\ErrorController;
    use App\Exceptions\RouterException;

    try {

        $router = new Router(Env::get('APP_URL'));

        /**
         * Controllers
         */
        $router->namespace("App\Controllers")->group(null);

        /**
         * Create a new user:
         */
        $router->post("/users", "UserController:create");

        /**
         * Get user by ID:
         */
        $router->get("/users/{userId}", "UserController:readById");

        /**
         * Get all users:
         */
        $router->get("/users", "UserController:readAll");

        /**
         * Get all users using limit:
         */
        $router->get("/users/limit/{limit}", "UserController:readAll");

        /**
         * Update user:
         */
        $router->put("/users/{userId}", "UserController:update");

        /**
         * Delete user:
         */
         $router->delete("/users/{userId}", "UserController:delete");

        $router->dispatch();

        if ($router->error()) {
            throw new RouterException($router->error());
        }
    
    } catch (\Exception $e) {
        return ErrorController::getErrorMessage($e);
    }