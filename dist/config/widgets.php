<?php 
set_include_path(realpath($_SERVER['DOCUMENT_ROOT'])); 
require_once('config/functions.php');

class Widget {

    public function render($widget, $data) {
        include 'components/partials/widgets/' . $widget . '.php';
    }

    public function getJS($roleId) {
        $Role = new Roles();
        $sub = $Role->$roleId->sub_department;
        $department = $Role->$roleId->department;
        $JS = new stdClass;

        switch($department) {
            case 'management':
                switch ($sub) {
                    case 'finance':
                        $JS = (object) [
                            "$department/main.js",
                        ];
                    break;

                    case 'operation':
                        $JS = (object) [
                            "$department/main.js",

                        ];
                    break;
                }
            break;
            case 'finance' :
                $JS = (object) [
                    "$department/$sub.js",

                ];
                break;
            case 'geospatial':
                switch ($sub) {
                    case 'charting':
                        $JS = (object) [
                            "$department/$sub.js",

                        ];
                    break;
                    case 'translation':
                        $JS = (object) [
                            "$department/$sub.js",

                        ];
                    break;
                }
            break;
            default:
                break;
        }

        foreach ($JS as $script) {
            echo '<script src="assets/js/custom' . $script . '"></script>';
        }
    }

    public function get($widget, $data) {
        $this->render($widget, $data);
    }
}