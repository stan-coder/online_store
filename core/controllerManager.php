<?php

/**
 * Parent class
 *
 * Class controllerManager
 */
class controllerManager extends baseManager
{
    public static $variables = array(), $js = array(), $css = array(), $title = '', $isAjax = false, $noView = false, $layout = 'general', $matchUrl = null, $view = null, $formFieldsError = [], $formFields = [];
    protected $post = [];

    /**
     * Get prepared resources
     *
     * @return string
     */
    public static function getResources() {
        $resources = '';
        foreach (self::$css as $css) {
            $resources .= "\t<link rel=\"stylesheet\" href=\"".
                (strpos($css, '/') !== false ? $css : "/public/css/{$css}")
                ."\" />\n";
        }
        foreach (self::$js as $js) {
            $resources .= "\t<script type=\"text/javascript\" src=\"".
                (strpos($js, '/') !== false ? $js : "/public/js/{$js}")
                ."\"></script>\n";
        }
        return "\n".$resources;
    }

    /**
     * Get needed Db instance
     *
     * @param null $classType
     * @return mixed
     */
    public function db($classType = null) {
        return (is_null($classType) ? dbPrepare::getInstance() : dbCommon::getInstance());
    }

    /**
     * Set title of html-page
     *
     * @param $value
     */
    public function setTitle($value) {
        self::$title = $value;
    }

    /**
     * Add js-file in list
     *
     * @param $fileName
     */
    public function js($fileName) {
        if (is_array($fileName)) {
            self::$js = array_merge(controllerManager::$js, $fileName);
        } else {
            self::$js[] = $fileName;
        }
    }

    /**
     * Add css-file in list
     *
     * @param $fileName
     */
    public function css($fileName) {
        if (is_array($fileName)) {
            self::$css = array_merge(controllerManager::$css, $fileName);
        } else {
            self::$css[] = $fileName;
        }
    }

    /**
     * Set layout
     *
     * @param $name
     */
    public function setLayout($name) {
        controllerManager::$layout = $name;
    }

    /**
     * Set another view
     *
     * @param $controller
     * @param $name
     */
    public function setView($name, $controller = null) {
        if (empty($controller)) {
            $controller = strtolower(substr(get_called_class(), 0, -10));
        }
        controllerManager::$view = [$name, $controller];
    }

    /**
     * Get math url
     *
     * @return null
     */
    public function getMatchUrl() {
        return self::$matchUrl;
    }

    /**
     * Short access to session model
     *
     * @return mixed
     */
    public function session() {
        return $this->model('session');
    }

    protected static function getFieldByIndex($index) {
        return array_slice(self::$formFieldsError, $index, 1);
    }

    /**
     * Get message of field error
     *
     * @param $index
     * @return mixed
     */
    public static function getFormFieldsError($index) {
        $key = key(self::getFieldByIndex($index));
        return (isset(self::$formFieldsError[$key])?self::$formFieldsError[$key]['message']:'');
    }

    /**
     * Set message of field error
     *
     * @param $index
     * @param $message
     */
    public function setFieldError($index, $message) {
        self::$formFieldsError[$index] = ['message' => $message, 'valid' => false];
    }

    /**
     * Check form on valid all fields
     *
     * @return bool
     */
    public function formIsValid() {
        $checking = true;
        array_walk(self::$formFieldsError, function($item) use(&$checking){
            if ($item['valid'] === false) {
                $checking = false;
            }
        });
        return $checking;
    }

    /**
     * Get post field
     *
     * @param $key
     * @return mixed
     */
    public function post($key) {
        return $this->post[$key];
    }
}