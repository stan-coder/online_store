<?php

class Routing {

    /**
     * Start routing
     */
    public static function run() {
        if (!is_file(ROUTER_CACHE_FILE)) {
            crash('The cache-route-file is not exists');
        }
        require_once(ROUTER_CACHE_FILE);
        if (!function_exists('getRoute')) {
            crash('Route function "getRoute" does not exists');
        }
        $route = getRoute();
        if (Cache::e_array($route)) {
            crash('Route value is not correct in cache-route-file');
        }

        /**
         * Start finding
         */
        try {
            if (isset($route[URI])) {
                $data = $route[URI];
                throw new Exception();
            }
            $onlyRegexp = array_filter(
                array_keys($route), function($element){
                    if (substr($element, 0, 1) === '~') {
                        return true;
                    }
                    return false;
                }
            );
            foreach ($onlyRegexp as $pattern) {
                if (preg_match($pattern, URI, $match)) {
                    controllerManager::$matchUrl = $match;
                    $data = $route[$pattern];
                    throw new Exception();
                }
            }

        } catch(Exception $e) {
            require_once($controllerPath = WORK_SPACE_FOLDER_PATH . 'controllers' . DS . $data['controller'] . 'Controller.php');

            $render = new Render();
            $render->execute($data, $data['controller'], $controllerPath);
        }
        Render::generate404Error();
    }
}