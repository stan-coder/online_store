<?php

abstract class baseManager
{
    private $models = array();

    /**
     * Get model
     *
     * @param $name
     * @return mixed
     */
    public function model($name) {
        if (!in_array($name, $this->models)) {
            /**
             * Create single exemplar
             */
            require_once(WORK_SPACE_FOLDER_PATH . 'models' . DS . $name . 'Model.php');
            $refCl = new ReflectionClass(ucfirst($name).'Model');
            $this->models[$name] = $refCl->newInstance();
        }
        return $this->models[$name];
    }
}