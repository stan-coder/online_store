<?php

/**
 * Parent class
 *
 * Class controllerManager
 */
class controllerManager
{
    public static $variables = array(), $js = array(), $css = array(), $title = '', $isAjax = false, $noView = false, $layout = 'general', $matchUrl = null, $view = null;
    private $models = array();

    /**
     * Get prepared resources
     *
     * @return string
     */
    public static function getResources(){
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
    public function js($fileName){
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
    public function css($fileName){
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
    public function getMatchUrl()
    {
        return self::$matchUrl;
    }
}