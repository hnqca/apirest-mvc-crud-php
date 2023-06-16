<?php

    namespace App\Helpers;

    abstract class Env {

        public static function get(string $name)
        {
            $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
            $dotenv->load();
           
            return getenv($name);
        }

    }