<?php

class CSRFProtectionModel extends modelManager
{
    public function protection() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->checkProtection();
        }
        return $this->getProtection();
    }

    private function getProtection() {
        $hash = $this->model('customFunction')->getRandomString(50);
        $this->model('session')->set('CSRFHash', $hash);
        return '<input type="hidden" value="'.$hash.'" name="CSRFHash" />'."\n";
    }

    private function checkProtection() {
        if (!isset($_POST['CSRFHash']) || $_POST['CSRFHash'] !== $this->model('session')->get('CSRFHash')) {
            exit('Error, unexpected request!');
        }
    }
}