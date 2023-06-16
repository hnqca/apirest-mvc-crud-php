<?php

    namespace App\Core;
    use App\Helpers\Env;
    use App\Sdk\JsonWebToken\JWT;
    use App\Exceptions\AuthorizationException;

    abstract class Controller {

        protected string $authorizationBearer;
        protected int    $endpointAccess;

        /**
         * Responsável por capturar o token "Authorization Bearer" do cabeçalho da requisição.
         * Lança uma exceção se o token não for encontrado no cabeçalho.
         * Realiza a sanitização do token removendo possíveis caracteres especiais.
         */
        function __construct()
        {
            $headers = getallheaders();

            if (!isset($headers['Authorization'])) {
                throw new AuthorizationException("Authorization Bearer não foi encontrado no header da requisição.");
            }

            $this->authorizationBearer = str_replace('Bearer ', '', $headers['Authorization']);
            $this->authorizationBearer = htmlspecialchars($this->authorizationBearer);
        }

        /**
         * Define o nível de acesso do endpoint e realiza as verificações necessárias.
         * @param integer $access (O nível de acesso necessário para o endpoint).
         * Lança exceções se o token não for válido ou se o usuário não tiver permissão para acessar o endpoint.
         */
        public function setAccessToEndpoint(int $access)
        {
            $this->endpointAccess = $access;

            $this->checkBearerIsValid();
            $this->checkPermissionBearer();
        }

        /**
         * Verifica se o token Bearer é válido.
         * Lança uma exceção se o token não for válido.
         */
        private function checkBearerIsValid()
        {
            $tokenIsValid = JWT::validate(Env::get("JWT_SECRET_KEY"), $this->authorizationBearer);

            if (!$tokenIsValid) {
                throw new AuthorizationException("Bearer token não é válido.");
            }
        }

        /**
         * Verifica se o usuário possui a permissão necessária para acessar o endpoint.
         * Lança uma exceção se o usuário não tiver permissão para acessar o endpoint.
         */
        private function checkPermissionBearer()
        {
            $decode = JWT::decode($this->authorizationBearer);

            if (!is_numeric($decode->access) OR $decode->access < $this->endpointAccess) {
                throw new AuthorizationException("sem permissão para esse endpoint.");
            }
        }

    }
