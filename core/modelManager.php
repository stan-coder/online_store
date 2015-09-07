<?php

/**
 * Parent model class
 *
 * Class Model
 */
class Model
{
    /**
     * Get needed Db instance
     *
     * @param null $classType
     * @return mixed
     */
    public static function db($classType = null) {
        return (is_null($classType) ? dbPrepare::getInstance() : dbCommon::getInstance());
    }
}