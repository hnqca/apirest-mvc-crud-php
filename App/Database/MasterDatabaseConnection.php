<?php

    namespace App\Database;
    use App\Helpers\Env;

    class MasterDatabaseConnection {

        private $connection;
        private $dns = null;

        public function __construct(string $dns = null)
        {
            $this->dns = $dns;
        }

        public function setDatabase(array $database)
        {
            $db = json_decode(json_encode($database), false);
            $this->dns = "{$db->drive}:dbname={$db->name};host={$db->host};user={$db->user};port={$db->port}";
            
            return $this;
        }

        public function getConn()
        {
            return $this->connection ? $this->connection : $this->initConnection();
        }

        private function getDNS()
        {
            if ($this->dns) {
                return $this->dns;
            }

            return $this->dns = "".Env::get('DB_DRIVE').":dbname=".Env::get('DB_NAME').";host=".Env::get('DB_HOST').";user=".Env::get('DB_USER').";port=".Env::get('DB_PORT')."";
        }

        private function initConnection()
        {
            $pdo = new \PDO($this->getDNS());
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->connection = $pdo;
        }

    }