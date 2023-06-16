<?php

    namespace App\Database;
    use App\Database\MasterDatabaseConnection;
    use PDO;

    class MasterPDO {

        protected $table;
        protected $where        = null;
        protected $groupBy      = null;
        protected $orderBy      = null;
        protected $limit        = null;
        protected $placeholders = [];

        protected $conn;
        protected $fetchStyle;

        function __construct(\PDO $conn = null, $fetchStyle = "FETCH_OBJ")
        {
            $this->setConnection($conn);
            $this->setFetchStyle($fetchStyle);
        }

        private function setConnection($conn)
        {
            $this->conn = $conn ? $conn : (new MasterDatabaseConnection)->getConn();
        }

        private function setFetchStyle(string $fetchStyle)
        {
            $this->fetchStyle = ($fetchStyle === "FETCH_OBJ") ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
        }

        // limpa o paramêtro para impedir XSS Attack.
        public static function doCleanParam($param) 
        {
            return is_string($param) ? strip_tags(htmlspecialchars($param)) : $param;
        }

        // Define a tabela que será manipulada.
        public function from(string $table)
        {
            $this->placeholders = [];
            $this->table = self::doCleanParam($table);
            return $this;
        }

        public function where($data)
        {
            if(is_string($data)){
                $column = self::doCleanParam($data);
                $this->where = "WHERE {$column}";
                return $this;
            }

            foreach ($data as $key => $value) {
                $column      = $key;
                $condition   = $value[0];
                $singleValue = $value[1];
                $clauses[]   = "{$column} {$condition} :{$column}_where";
                $this->placeholders[] = [":{$column}_where", $singleValue];
            }

            $this->where = "WHERE " . implode(' AND ', $clauses);
            return $this;
        }

        public function in(array $data)
        {
            $i = 1;
            foreach ($data as $value) {
                $value = self::doCleanParam($value);
                $clauses[] = ":in_{$i}";
                $this->placeholders[] = [":in_{$i}", $value];
                $i++;
            }

            $clauses     = implode(', ', $clauses);
            $this->where = "{$this->where} IN ({$clauses})";
            return $this;
        }

        public function notin(array $data)
        {
            $i = 1;
            foreach ($data as $value) {
                $value = self::doCleanParam($value);
                $clauses[] = ":notin_{$i}";
                $this->placeholders[] = [":notin_{$i}", $value];
                $i++;
            }

            $clauses     = implode(', ', $clauses);
            $this->where = "{$this->where} NOT IN ({$clauses})";
            return $this;
        }

        public function between($value1, $value2)
        {
            $value1 = self::doCleanParam($value1);
            $value2 = self::doCleanParam($value2);

            $this->where = "{$this->where} BETWEEN :between_1 AND :between_2";
            $this->placeholders[] = [":between_1", $value1];
            $this->placeholders[] = [":between_2", $value2];
            return $this;
        }

        public function groupBy(array $data)
        {
            $columns = implode(', ',  array_map('self::doCleanParam', $data));

            $this->groupBy = "GROUP BY {$columns}";
            return $this;
        }

        public function orderBy(array $data)
        {

            foreach ($data as $column => $value) {

                $column = self::doCleanParam($column);
                $value  = self::doCleanParam($value);

                $clauses[] = "{$column} {$value}";
            }

            $this->orderBy = 'ORDER BY ' . implode(', ', $clauses);
            return $this;
        }

        // Define o limite de registros a serem manipulados.
        public function limit(int $limit)
        {
            $limit = self::doCleanParam($limit);

            $this->limit = "LIMIT {$limit}";
            return $this;
        }

        // Seta os bindvalues dos valores.
        private function setBindValues($stmt, array $data = [])
        {
            if (empty($data)) {
                return $stmt;
            }

            foreach ($data as $value) {
                $stmt->bindValue($value[0], self::doCleanParam($value[1]));
            }

            return $stmt;
        }

        // Limpa os dados de saída, evitando XSS attack.
        private function sanitizeObjectData($data, $typeSelect)
        {
            if ($typeSelect === "fetch") {
                return (object) array_map('self::doCleanParam', get_object_vars($data));
            }

            $result = [];
            foreach ($data as $object) {
                $sanitizedObject = new \stdClass();
                foreach ($object as $key => $value) {
                    $sanitizedObject->$key = self::doCleanParam($value);
                }
                $result[] = $sanitizedObject;
            }
            
            return $result;
        }        
        

        // Limpa os dados de saída, evitando XSS attack.
        private function sanitizeArrayData($array)
        {
            $result = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->sanitizeArrayData($value);
                } else {
                    $result[$key] = call_user_func('self::doCleanParam', $value);
                }
            }
            return $result;
        }

        private function sanitizeData($data, $typeSelect)
        {
            if ($this->fetchStyle === PDO::FETCH_OBJ) {
                return $this->sanitizeObjectData($data, $typeSelect); 
            }

            return $this->sanitizeArrayData($data);
        }
        
        // Responsável por realizar uma consulta no banco de dados.
        public function select(bool $selectAll = false)
        {
            $sql  = "SELECT * FROM {$this->table} {$this->where} {$this->groupBy} {$this->orderBy} {$this->limit}";
            $stmt = $this->conn->prepare($sql);

            $stmt = $this->setBindValues($stmt, $this->placeholders);
            $stmt->execute();

            $typeSelect = $selectAll ? "fetchAll" : "fetch"; 

            $data = $stmt->$typeSelect($this->fetchStyle);

            if (!$data) {
                return false;
            }

            return $this->sanitizeData($data, $typeSelect);
        }

        // Responsável por inserir registros no banco de dados.
        public function insert(array $data)
        {
            $columns      = implode(', ',  array_map('self::doCleanParam', array_keys($data)));
            $placeholders = ':' . str_replace(" ", ":", $columns);

            foreach ($data as $column => $value) {
                $this->placeholders[] = [":{$column}", $value];
            }

            $sql  = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->conn->prepare($sql);

            $stmt = $this->setBindValues($stmt, $this->placeholders);
            $stmt->execute();

            return $this->conn->lastInsertId() ? $this->conn->lastInsertId() : false;
        }

        // Responsável por deletar registros na tabela.
        public function delete()
        {
            $sql  = "DELETE FROM {$this->table} {$this->where} {$this->groupBy} {$this->orderBy} {$this->limit}";
            $stmt = $this->conn->prepare($sql);

            $stmt = $this->setBindValues($stmt, $this->placeholders);
        
            return $stmt->execute() ? true : false;
        }

        // Responsável por atualizar registros na tabela.
        public function update(array $data)
        {
            $sets = implode(', ', array_map(function($column, $value) {
                
                $column = self::doCleanParam($column);
                $value  = self::doCleanParam($value);
        
                $this->placeholders[] = [":{$column}", $value];

                return "{$column} = :{$column}";

            }, array_keys($data), $data));

            $sql  = "UPDATE {$this->table} SET {$sets} {$this->where} {$this->groupBy} {$this->orderBy} {$this->limit}";
            $stmt = $this->conn->prepare($sql);

            $stmt = $this->setBindValues($stmt, $this->placeholders);

            return $stmt->execute() ? true : false;
        }

        /**
         * Realiza operações de agregação no banco de dados.
         * COUNT, SUM, MAX, MIN, AVG
         */
        private function aggregation(string $column, string $operation)
        {
            $column = self::doCleanParam($column);

            $sql  = "SELECT {$operation}({$column}) AS total FROM {$this->table} {$this->where}";
            $stmt = $this->conn->prepare($sql);

            $stmt = $this->setBindValues($stmt, $this->placeholders);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total ?? false;
        }

        public function count(string $column = "id")
        {
           return $this->aggregation($column, "COUNT");
        }

        public function sum(string $column)
        {
           return $this->aggregation($column, "SUM");
        }

        public function avg(string $column)
        {
           return $this->aggregation($column, "AVG");
        }

        public function min(string $column)
        {
           return $this->aggregation($column, "MIN");
        }

        public function max(string $column)
        {
           return $this->aggregation($column, "MAX");
        }

        public function describe()
        {
            $sql = "DESCRIBE {$this->table}";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN) ?? null;

            return $columns;
        } 

        public function createTable(string $tableName, array $columns)
        {
            foreach ($columns as $name => $value) {
                $clauses[] = "{$name} {$value}";
            }

            $clauses = implode(",", $clauses);

            $sql  = "CREATE TABLE {$tableName} ({$clauses})";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute() ? true : false;
        }

    }