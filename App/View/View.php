<?php

    namespace App\View;

    class View {

        public static function renderJson($content, string $title = "data")
        {
            $content = json_decode(json_encode($content), true);
        
            header("Content-Type: application/json");
            echo json_encode([$title => $content], JSON_PRETTY_PRINT);
        }
    }