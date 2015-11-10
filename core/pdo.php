<?php

class db
{
    private static $instance;

    /**
     * Get instance of PDO object
     * @return PDO
     */
    private static function get() {
        if (!self::$instance instanceof PDO) {
            try {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                    PDO::ATTR_AUTOCOMMIT => true,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];
                self::$instance = new PDO(Config::$dbDriver.':host='.Config::$dbHost.';dbname='.Config::$dbName.';charset='.Config::$dbCharset, Config::$dbUsername, Config::$dbPassword, $options);
            } catch (PDOException $pe) {
                Config::crash(self::getTextError($pe));
            }
        }
        return self::$instance;
    }

    /**
     * Execute any query and return appropriate result
     * @param $sql
     * @param null $args
     * @return array|int|mixed
     */
    public static function exec($sql, $args = null) {
        $stmt = self::get()->prepare($sql);

        if (!is_null($args)) {
            if (!is_array($args)) $args = [$args];
            if (ctype_digit(implode($keys = array_keys($args))) && reset($keys) === 0) {
                $args = array_combine(array_map(function($element){
                    return ++$element;
                }, array_keys($args)), array_values($args));
            }
            foreach ($args as $key => $arg) {
                $stmt->bindValue($key, $arg, self::getDataType($arg));
            }
        }
        $stmt->execute();
        if (preg_match('/^(select)|(call)/m', str_replace([' ', "\n", "\r", "\t"], '', $sql))) {
            if (($data = $stmt->fetchAll()) === false || !is_array($data) || ($stmt->closeCursor()) === false) {
                Config::crash('The data cannot be fetched. SQL: '.$sql);
            }
            return count($data)===1 ? reset($data) : $data;
        } else {
            return $stmt->rowCount();
        }
    }

    /**
     * Execute list of query within transaction
     * @param $handler
     */
    public static function executeWithinTransaction($handler) {
        self::setAttrConsideringTransaction();
        try {
            self::get()->beginTransaction();
            (new ReflectionFunction($handler))->invoke();
            self::get()->commit();
            self::setAttrConsideringTransaction(false);
        } catch (PDOException $pe) {
            self::get()->rollBack();
            Config::crash(self::getTextError($pe));
        }
    }

    /**
     * Set attribute particularly dealing with transaction
     * @param bool|true $start
     */
    private static function setAttrConsideringTransaction($start = true) {
        self::get()->setAttribute(PDO::ATTR_ERRMODE, $start ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_WARNING);
        self::get()->setAttribute(PDO::ATTR_AUTOCOMMIT, !$start);
    }

    /**
     * Get data type while preparing query
     * @param $variable
     * @return int
     */
    private static function getDataType($variable) {
        switch (gettype($variable)) {
            case 'integer':
                return PDO::PARAM_INT;
            case 'boolean':
                return PDO::PARAM_BOOL;
            case 'NULL':
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * Get text of error
     * @param $pe
     * @return string
     */
    private static function getTextError($pe) {
        return 'PDO error. Code: '.$pe->getCode().'; ' . $pe->getMessage() .  "\nFile: " . $pe->getFile() . "\nLine: " . $pe->getLine();
    }

    /**
     * Get lastInsertId value
     * @return string
     */
    public static function getInsertedId() {
        return db::get()->lastInsertId();
    }
}