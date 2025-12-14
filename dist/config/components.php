<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT'])); 
require_once('config/functions.php');

class Card {

    public function render($widget, $data) {
        include 'components/partials/cards/' . $widget . '.php';
    }

    public function get($widget, $data = []) {
        $this->render($widget, $data);
    }
}
class Table {

    public function render($table, $data) {
        include 'components/tables/' . $table . '.php';
    }

    public function get($type, $data) {

        $table = $type === 'general' || $type === 'authority' ? $type.'-tasks' : $type;

        $this->render($table, $data);
    }
}

class Modal {

    public function render($modal, $data) {
        include 'components/partials/modals/' . $modal . '.php';
    }

    public function get($section, $data) {

        $modal = $section === 'general' || $section === 'authority' ? $section.'-tasks' : $section;

        $this->render($modal, $data);
    }
}

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

    public function get($widget, $data = []) {
        $this->render($widget, $data);
    }
}

class Layout {

    public function render($layout, $data) {
        include 'components/layouts/sidebar/' . $layout . '.php';
    }

    public function get($layout, $data) {
        $this->render($layout, $data);
    }
}