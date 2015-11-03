<?php

class Render
{
    private $routingInfo, $controllerName, $controllerPath;

    /**
     * Execute method
     *
     * @param array $routingInfo
     * @param $controllerName
     * @param $controllerPath
     */
    public function execute($routingInfo, $controllerName, $controllerPath){
        $reflect = new ReflectionClass(ucfirst($controllerName) . 'Controller');
        $controllerExemplar = $reflect->newInstance($routingInfo);

        if (!method_exists($controllerExemplar, $routingInfo['function'])) {
            crash("Controller '$controllerName' does not contained method '$routingInfo[function]' controller. Path: $controllerPath");
        }
        foreach ($this as $varName => $value) {
            $this->$varName = $$varName;
        }
        $this->checkNoViewAndIsAjax($controllerExemplar);
        /**
         * If established isAjax
         */
        if (controllerManager::$isAjax === true) {
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest' || strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
                if (Config::$debug === true) {
                    crash('This page may be rendered only within Ajax');
                }
                self::generate404Error();
            }
            Config::$ajaxMode = true;
            header('Content-Type: application/json');
        } else {
            header('Content-Type: text/html; charset=utf-8');
        }
        if (method_exists($controllerExemplar, 'preController')) {
            $controllerExemplar->preController($routingInfo);
        }
        /**
         * Begin gradually execute and response
         */
        call_user_func(array($controllerExemplar, $routingInfo['function']));
        if (controllerManager::$noView === true || controllerManager::$isAjax === true) {
            exit();
        }
        $this->workingWithView();
    }

    /**
     * Checking properly before view execute
     *
     * @param $contEx
     * @return string
     */
    private function checkNoViewAndIsAjax($contEx) {
        if (
            (!empty($this->routingInfo['ajax']))
            ||
            (property_exists($contEx, 'options') && is_array($contEx::$options) && isset($contEx::$options['wholeControllerIsAjax']))
        ) {
            controllerManager::$isAjax = true;

        } elseif (
            (property_exists($contEx, 'noView') && is_array($contEx::$noView) && !empty($contEx::$noView) && in_array($this->routingInfo['function'], $contEx::$noView))
            ||
            (property_exists($contEx, 'options') && !empty($contEx::$options['noView']))
        ) {
            controllerManager::$noView = true;
        }
    }

    /**
     * Working with view
     */
    private function workingWithView() {

        $pathToViewFile = WORK_SPACE_FOLDER_PATH . 'views' . DS .
           (!is_null(controllerManager::$view) ? controllerManager::$view[1] . DS . controllerManager::$view[0]
               : $this->controllerName . DS . $this->routingInfo['function']) . '.php';
        if (!is_file($pathToViewFile) || ($content = file_get_contents($pathToViewFile)) === false) {
            crash('Unable to open view-file: '.$pathToViewFile);
        }
        /**
         * Exclusively for CSRF protection actions in order to obtain more convenience while using
         */
        if (mb_strpos($content, '{CSRFProtection}') !== false) {
            require_once(WORK_SPACE_FOLDER_PATH.'models'.DS.'CSRFProtectionModel.php');
            $content = str_replace('{CSRFProtection}', (new CSRFProtectionModel())->protection(), $content);
        }
        /**
         * Show fields error
         */
        if (mb_strpos($content, '{_err}') !== false) {
            $content = explode('{_err}', $content);
            array_walk($content, function (&$item, $key, $count) {
                $item = $item.($key<$count?"<?php echo controllerManager::getFormFieldsError({$key}); ?>":'');
            }, count($content)-1);
            $content = implode('', $content);
        }
        /**
         * Replace variables {} in template
         */
        foreach (controllerManager::$variables as $varName => $varValue) {
            if (mb_strpos($content, '{' . $varName . '}') !== false) {
                $content = str_replace('{' . $varName . '}', $varValue, $content);
            }
        }
        if (empty(controllerManager::$title) && isset($this->routingInfo['title'])) {
            controllerManager::$title = $this->routingInfo['title'];
        }
        $this->setResourcesFromControllerProperty();
        extract(controllerManager::$variables);
        require_once(WORK_SPACE_FOLDER_PATH . 'layouts' . DS . $this->getLayout() . '.php');
        exit();
    }

    /**
     * Get need template
     */
    private function getLayout() {
        $controller = ucfirst($this->controllerName.'Controller');
        /**
         * Set layout for whole controller
         */
        if (property_exists($controller, 'options') && !empty($controller::$options['layout'])) {
            controllerManager::$layout = $controller::$options['layout'];
        }
        elseif (isset($this->routingInfo['layout'])) {
            /**
             * Only single action
             */
            return $this->routingInfo['layout'];
        }
        return controllerManager::$layout;
    }

    /**
     * Set js & css given from controller properties
     */
    private function setResourcesFromControllerProperty(){
        if (isset($this->routingInfo['css'])) {
            if (is_array($this->routingInfo['css'])) {
                controllerManager::$css = array_merge(controllerManager::$css, $this->routingInfo['css']);
            } else {
                controllerManager::$css[] = $this->routingInfo['css'];
            }
        }
        if (isset($this->routingInfo['js'])) {
            if (is_array($this->routingInfo['js'])) {
                controllerManager::$js = array_merge(controllerManager::$js, $this->routingInfo['js']);
            } else {
                controllerManager::$js[] = $this->routingInfo['js'];
            }
        }
    }

    /**
     * Rendering 404 error
     */
    public static function generate404Error() {
        header("HTTP/1.0 404 Not Found");
        require_once(CORE . 'basicTemplates' . DS . '404Error.php');
        exit();
    }
}