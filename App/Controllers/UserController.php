<?php

    namespace App\Controllers;
    use App\Core\Controller;
    use App\Models\User;
    use App\View\View;

    class UserController extends Controller {

        /**
         * Método responsável por cadastrar o usuário no banco de dados.
         */
        public function create(array $data)
        {
            parent::setAccessToEndpoint(2);

            $user   = $this->defineUserData((new User), $data, true);
            $userId = $user->save();

            if (!$userId) {
                throw new \PDOException("não foi possível cadastrar o usuário");
            }

            return View::renderJSON([
                'status'  => "success",
                'message' => "usuário cadastrado com sucesso!",
                'userId'  => $userId
            ]);
        }

        /**
         * Método responsável por atualizar os dados do usuário no banco de dados.
         */
        public function update(array $data)
        {
            parent::setAccessToEndpoint(2);

            $user = $this->findUser($data['userId']);
            $user = $this->defineUserData($user, $data);

            if (!$user->save()) {
                throw new \PDOException("não foi possível atualizar os dados do usuário");
            }

            return View::renderJSON([
                'status'  => "success",
                'message' => "usuário atualizado com sucesso!",
                'userId'  => $user->id
            ]);
        }

        /**
         * Remove um usuário específico com base no seu ID
         */
        public function delete(array $data)
        {
            parent::setAccessToEndpoint(2);

            $user = $this->findUser($data['userId']);

            if (!$user->delete()) {
                throw new \PDOException("não foi possível deletar o usuário");
            }

            return View::renderJSON([
                'status'  => "success",
                'message' => "usuário deletado com sucesso!",
                'userId'  => (int) $data['userId']
            ]);
        }

        /**
         * Obtém os detalhes de um usuário específico com base no seu ID
         */
        public function readById(array $data)
        {
            parent::setAccessToEndpoint(1);

            $user = $this->findUser($data['userId']);

            return View::renderJSON([
                'status' => "success",
                'user'   => $user->data
            ]);
        }

        /**
         * Especifica a quantidade máxima de registros a serem listados
         */
        private function readAllWithLimit(User $users, int $limit = 0)
        {
            if (!is_numeric($limit) OR $limit < 1) {
                throw new \InvalidArgumentException("Defina um limit válido!");
            }

            return $users->limit($limit);
        }
        
        /**
         * Obtém a lista de todos os usuários cadastrados
         */
        public function readAll(array $data)
        {
            parent::setAccessToEndpoint(1);

            $users = (new User)->find();

            if (isset($data['limit'])) {
                $users = $this->readAllWithLimit($users, (int) $data['limit']);
            }

            return View::renderJSON([
                'status' => "success",
                'users'  => $users->select(true)
            ]);
        }

        private function findUser($userId = null)
        {
            if (!is_numeric($userId) OR $userId < 1) {
                throw new \InvalidArgumentException("necessário informar a ID do usuário.");
            }

            $user = (new User)->findById($userId);

            if (!$user->id) {
               throw new \InvalidArgumentException("usuário não encontrado.");
            }

            return $user;
        }

        private function defineUserData(User $user, array $data, bool $isRequired = false)
        {
            $requiredFields = [
                'first_name' => "setFirstName",
                'last_name'  => "setLastName",
                'email'      => "setEmail"
            ];

            foreach ($requiredFields as $field => $setterMethod) {

                if ($isRequired AND (empty($data[$field]))) {
                    $requiredFieldNames = implode(", ", array_keys($requiredFields));
                    throw new \InvalidArgumentException("Informe todos os dados obrigatórios! Campos obrigatórios: '{$requiredFieldNames}'.");
                }

                if (!empty($data[$field])) {
                    $user->$setterMethod($data[$field]);
                }
            }

            return $user;
        }

    }