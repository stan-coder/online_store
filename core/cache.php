<?php

class Cache
{
    private $collectingOfUrl = array();

    /**
     * General scanning function
     */
    public function startScan() {
        $controllers = array_filter(scandir(WORK_SPACE_FOLDER_PATH . 'controllers'), function($element) {
            return strstr($element, 'Controller.php', true);
        });

        foreach ($controllers as $controller) {
            require_once(WORK_SPACE_FOLDER_PATH . 'controllers' . DS . $controller);
            if (!class_exists(ucfirst($classController = ucfirst(substr($controller, 0, -4))))) {
                crash('The controller "'.$controller.'" does not have necessary class');
            }
            if (!property_exists($classController, 'url')) {
                crash('The property "$url" does not exists in "' . $classController . '" controller');
            }
            /**
             * Loop across whole actions
             */
            foreach ($classController::$url as $method => $paramsOfAction) {

                $options = array('function' => $method, 'controller' => strtolower(strstr($classController, 'Controller', true)));
                /**
                 * Preparing route record
                 */
                if (is_array($paramsOfAction)) {
                    if (!isset($paramsOfAction['url']) && !isset($paramsOfAction['patternUrl'])) {
                        crash('The route "'.$method.'" of controller\'s "'.$classController.'" does not have element "url" or "patternUrl"');
                    }
                    $exactlyUrl = isset($paramsOfAction['url']) ? 'url' : 'patternUrl';
                    if (isset( $this->collectingOfUrl[ $paramsOfAction[$exactlyUrl] ] ) ) {
                        crash('Double url or patternUrl "'.$exactlyUrl.'" in controller "'.$classController.'"');
                    }
                    if (isset($paramsOfAction['controller']) || isset($paramsOfAction['function'])) {
                        crash('The route array has invalid key of element: "function", "controller"');
                    }
                    $fUrl = $paramsOfAction[$exactlyUrl];
                    unset($paramsOfAction[$exactlyUrl]);

                    $this->collectingOfUrl[$fUrl] = array_merge($paramsOfAction, $options);
                } else {
                    if (isset($this->collectingOfUrl[$paramsOfAction])) {
                        crash('Double route url "'.$paramsOfAction.'", controller "'.$classController);
                    }
                    $this->collectingOfUrl[$paramsOfAction] = $options;
                }
            }
        }
        Logging::checkRecordingFolderOnWritable();
        $convertedArrayUrlToString = $this->convertingArrayUrlToString();

        /**
         * First saving
         */
        if (!is_file(ROUTER_CACHE_FILE)) {
            $this->saveRouteUrlInFile($convertedArrayUrlToString, true);
        } else {
            /**
             * Compare and if there is need than save
             */
            if (($routeFileContent = file_get_contents(ROUTER_CACHE_FILE)) === false) {
                crash('File "route" can not open to read');
            }
            try {
                if (($functionContext = mb_strstr($routeFileContent, 'getRoute(')) === false) {
                    throw new Exception();
                }
                eval('function '.str_replace('getRoute', 'getRouteTemp', $functionContext));
                if (!function_exists('getRouteTemp') || getRouteTemp() !== $this->collectingOfUrl) {
                    throw new Exception();
                }
            } catch (Exception $e) {
                $this->saveRouteUrlInFile($convertedArrayUrlToString);
            }
        }
    }

    /**
     * Prepare list url for saving
     */
    private function convertingArrayUrlToString() {
        $result = "array(\n";
        foreach ($this->collectingOfUrl as $key => $value) {
            $result .= "'{$key}'=>array(";

            foreach ($value as $keyChild => $valueChild) {
                $result .= "'{$keyChild}'=>". $this->typeToString($valueChild).',';
            }
            $result = substr($result, 0, -1);
            $result .= "),\n";
        }
        $result = substr($result, 0, -2);
        $result .= ');';
        return $result;
    }

    /**
     * Converting type to string imagine
     *
     * @param $value
     * @return string
     */
    private function typeToString($value) {
        switch (gettype($value)) {
            case 'string' : {
                return "'{$value}'";
            }
            case 'boolean' : {
                return (!$value ? 'false' : 'true');
            }
            case 'integer' : {
                return $value;
            }
            case 'array' : {
                $result = 'array(';
                foreach ($value as $key => $content) {
                    $result .= "'{$key}'=>'{$content}',";
                }
                $result = substr($result, 0, -1);
                $result .= ')';
                return $result;
            }
            default : {
                crash('Gave value is not belonged any allowed types');
            }
        }
        return '';
    }

    /**
     * Check value on empty
     *
     * @param $value
     * @return bool
     */
    public static function e_array($value) {
        return empty($value) || !is_array($value);
    }

    /**
     * Saving route file
     *
     * @param $string
     * @param bool $firstSaving
     */
    private function saveRouteUrlInFile($string, $firstSaving = false) {
        $resultSaving = @file_put_contents(ROUTER_CACHE_FILE, '<?php function getRoute(){return '.$string.'}');
        if ($resultSaving === false || $resultSaving < 1) {
            crash('The cache-route-file was not saved');
        }
        if ($firstSaving && chmod(ROUTER_CACHE_FILE, 0777) === false) {
            crash('Not able to set permission 0777 for cache-route-file');
        }
    }
}