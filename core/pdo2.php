<?php

class db2
{
    private static $instance;

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

    public static function executeWithinTransaction($handler) {
        self::get()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::get()->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        try {
            self::get()->beginTransaction();
            (new ReflectionFunction($handler))->invoke();
            self::get()->commit();
        } catch (PDOException $pe) {
            self::get()->rollBack();
            Config::crash(self::getTextError($pe));
        }
    }

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

    public static function getTextError($pe) {
        return $pe->getCode() . ': ' . $pe->getMessage() . "\n" . 'File: ' . $pe->getFile() . ', line: ' . $pe->getLine();
    }
}