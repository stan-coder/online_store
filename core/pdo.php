<?php

class db
{
    private static $dbh = null, $instance = array();

    /**
     * Return exemplar of PDO object
     *
     * @return null|PDO
     */
    public static function get() {
        if (is_null(self::$dbh)) {
            try {
                self::$dbh = new PDO(Config::$dbDriver.':host='.Config::$dbHost.';dbname='.Config::$dbName, Config::$dbUsername, Config::$dbPassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            } catch (PDOException $pe) {
                crash($pe->getMessage());
            }
        }
        return self::$dbh;
    }

    /**
     * Return instance of class
     *
     * @return mixed
     */
    public static function getInstance() {
        $class = get_called_class();

        if (!isset(self::$instance[$class])) {
            $reflect = new ReflectionClass($class);
            self::$instance[$class] = $reflect->newInstance();
        }

        return self::$instance[$class];
    }

    /**
     * Select one record and return only single value of first column
     *
     * @param $sql
     * @param $data
     * @param bool|false $throwError
     * @param int $fetch
     * @return array|bool|mixed
     */
    public function selectSingle($sql, $data, $throwError = false, $fetch = PDO::FETCH_ASSOC) {
        $result = $this->selectOne($sql, $data, $throwError, $fetch);
        return is_array($result) ? array_shift($result) : $result;
    }

    /**
     *  Closing unneeded functions
     */
    private function __clone() {}
    private function __sleep() {}
    private function __wakeup() {}
}

class dbCommon extends db
{
    /**
     * Execute sql-query and return count of affected rows
     *
     * @param $sql
     * @param bool $throwError , if needed, error will be thrown as trigger_error
     * @return int
     */
    public function exec($sql, $throwError = false){
        $result = null;

        try {
            $result = self::get()->exec($sql);
        } catch(PDOException $pe) {
            if ($throwError === true) {
                crash($pe->getMessage());
            }
            $result = false;
        }
        return $result;
    }

    /**
     * Select data and return whole result
     *
     * @param $sql
     * @param bool $throwError
     * @param int $fetch
     * @return array|bool|null
     */
    public function select($sql, $throwError = false, $fetch = PDO::FETCH_ASSOC) {
        $data = null;
        try {
            $stmt = self::get()->query($sql);
            if (!$stmt instanceof PDOStatement || $stmt === false || self::get()->errorCode() != PDO::ERR_NONE) {
                throw new PDOException('SQL query was not executed. Query: ' . $sql);
            }
            $data = $stmt->fetchAll($fetch);
            $stmt->closeCursor();
            if ((!is_array($data) || empty($data)) && $throwError === true) {
                throw new PDOException('SQL query returned empty result. Query: ' . $sql);
            }
        } catch(PDOException $pe) {
            if ($throwError === true) {
                crash($pe->getMessage());
            }
            $data = false;
        }
        return $data;
    }

    /**
     * Select data and return only one row
     *
     * @param $sql
     * @param bool $throwError
     * @param int $fetch
     * @return array|bool|null
     */
    public function selectOne($sql, $throwError = false, $fetch = PDO::FETCH_ASSOC) {
        if ((string)strtolower(substr($sql, -7)) !== 'limit 1') {
            $sql .= ' limit 1';
        }
        $data = $this->select($sql, $throwError, $fetch);
        return is_array($data) && isset($data[0]) ? $data[0] : $data;
    }
}

class dbPrepare extends db
{
    /**
     * Binding data type to PDO representation
     *
     * @param $variable
     * @return int
     */
    private function getDataType($variable) {
        switch (gettype($variable)) {
            case 'integer':
                return \PDO::PARAM_INT;
            case 'boolean':
                return \PDO::PARAM_BOOL;
            case 'NULL':
                return \PDO::PARAM_NULL;
            default:
                return \PDO::PARAM_STR;
        }
    }

    /**
     * Prepare and execute sql query and return object of PDOStatement
     *
     * @param $sql
     * @param $data
     * @param bool $throwError
     * @return bool|null|PDOStatement
     */
    public function prepareAndExecute($sql, $data, $throwError = false) {
        $dc = self::get();
        try {
            $stmt = $dc->prepare($sql);
            if (!$stmt instanceof PDOStatement || $stmt === false || $dc->errorCode() != PDO::ERR_NONE) {
                throw new PDOException('SQL query was not prepared. Query: ' . $sql);
            }
            /**
             * Binding data type
             */
            if (isset($data[0])) {
                $data = array_combine(array_map(function($element){
                    return ++$element;
                }, array_keys($data)), array_values($data));
            }
            foreach ($data as $key => $element) {
                $stmt->bindValue($key, $element, $this->getDataType($element));
            }
            if ($stmt->execute() === false) {
                throw new PDOException('SQL query was not executed. Query: ' . $sql);
            }
        } catch (PDOException $pe) {
            if ($throwError === true) {
                crash($pe->getMessage());
            }
            return false;
        }
        return $stmt;
    }

    /**
     * Execute sql query and return count of affected rows
     *
     * @param $sql
     * @param $data
     * @param bool $throwError
     * @return bool|int
     */
    public function exec($sql, $data, $throwError = false) {
        $rowCount = false;
        try {
            if (($stmt = $this->prepareAndExecute($sql, $data, $throwError)) === false) {
                throw new PDOException('SQL query was not executed: Query: ' . $sql);
            }
            $rowCount = $stmt->rowCount();
        } catch (PDOException $pe) {
            if ($throwError === true) {
                crash($pe->getMessage());
            }
        }
        return $rowCount;
    }

    /**
     * Select data and return whole result
     *
     * @param $sql
     * @param $data
     * @param bool $throwError
     * @param int $fetch
     * @return array|bool
     */
    public function select($sql, $data, $throwError = false, $fetch = PDO::FETCH_ASSOC) {
        $resultData = false;
        try {
            if (($stmt = $this->prepareAndExecute($sql, $data, $throwError)) === false) {
                throw new PDOException('SQL query was not executed: Query: ' . $sql);
            }
            $resultData = $stmt->fetchAll($fetch);
            $stmt->closeCursor();
            if ((!is_array($resultData) || empty($resultData)) && $throwError === true) {
                throw new PDOException('SQL query returned empty result. Query: ' . $sql);
            }
        } catch (PDOException $pe) {
            if ($throwError === true) {
                crash($pe->getMessage());
            }
        }
        return $resultData;
    }

    /**
     * Select data and return only one row
     *
     * @param $sql
     * @param $data
     * @param bool $throwError
     * @param int $fetch
     * @return array|bool
     */
    public function selectOne($sql, $data, $throwError = false, $fetch = PDO::FETCH_ASSOC) {
        if ((string)strtolower(substr($sql, -7)) !== 'limit 1') {
            $sql .= ' limit 1';
        }
        $data = $this->select($sql, $data, $throwError, $fetch);
        return is_array($data) && isset($data[0]) ? $data[0] : $data;
    }
}