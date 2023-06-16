<?php

    namespace App\Models;
    use App\Database\MasterLayer;

    class User extends MasterLayer {

        function __construct()
        {
            parent::__construct("users", "id", true);
        }

        public function setFirstName(string $firstName)
        {
            $this->setName('first_name', $firstName);
        }

        public function setLastName(string $lastName)
        {
            $this->setName('last_name', $lastName);
        }

        public function setEmail(string $email)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("email inválido!");
            }
            
            $this->data->email = $email;
        }

        private function setName(string $field, string $name)
        {
            if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
                throw new \InvalidArgumentException("'{$field}' contém caracteres inválidos!");
            }

            if (mb_strlen($name) < 2) {
                throw new \InvalidArgumentException("'{$field}' precisa conter pelo menos 2 caracteres.");
            }

            $this->data->$field = $name;
        }

    }