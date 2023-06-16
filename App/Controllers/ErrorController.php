<?php

    namespace App\Controllers;
    use App\View\View;

    class ErrorController {

        private static function getExceptions()
        {
            return [
                'PDOException'                          => "database",
                'InvalidArgumentException'              => "invalid_argument",
                'App\Exceptions\RouterException'        => "router",
                'App\Exceptions\AuthorizationException' => "authorization"
            ];
        }

        public static function getErrorMessage(\Exception $e)
        {
            $exceptions    = self::getExceptions();
            $exceptionName = get_class($e);

            return View::renderJSON([
                'status'     => "error",
                'type_error' => $exceptions[$exceptionName] ?? "unknown",
                'message'    => $e->getMessage()
            ]);
        }

    }