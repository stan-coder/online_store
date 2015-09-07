<?php

/**
 * Emergency exit
 *
 * @param $message
 */
function crash($message) {
    trigger_error($message, E_USER_ERROR);
}

/**
 * Redirect to url
 *
 * @param $url
 */
function redirect($url) {
    header('Location: /'.$url);
}

/**
 * Set variables in order to render their in view
 *
 * @param $varInfo
 * @param null $value
 */
function set($varInfo, $value = null){
    if (is_array($varInfo)) {
        foreach ($varInfo as $name => $value) {
            addVariableToController($name, $value);
        }
    } else {
        addVariableToController($varInfo, $value);
    }
}

function addVariableToController($varName, $value){
    if (isset(controllerManager::$variables[$varName])) {
        crash("Controller already has variable '$varName'");
    }
    controllerManager::$variables[$varName] = $value;
}

/**
 * Include and render partial
 *
 * @param $name
 */
function renderPartial($name) {
    if (!is_file($partialPath = WORK_SPACE_FOLDER_PATH . 'partials' . DS . $name . '.php')) {
        crash('Partial does not exists: ' . $partialPath);
    }
    require_once($partialPath);
}

/**
 * Checking variable to integer context
 *
 * @param $value
 * @return bool
 */
function isNum($value) {
    return (!is_numeric($value)) ? false : ctype_digit(strval($value));
}