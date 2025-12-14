<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/functions/roles.php';
require_once 'config/functions/general.php';
require_once 'config/system.php';


class Controller {

    private $Role;
    private $System;

    protected $user;

    public function __construct($username) {
        $this->Role = new Roles();
        $this->System = new System;
        $this->user = $username;
    }

    public function dashboard() {

        $depart = $this->Role->{$this->user}->department;
        $subdepart  = $this->Role->{$this->user}->sub_department;

        switch($depart) {
            case 'management':
                $dashboard =  General::getDashboard($depart."-".$subdepart);
            break;
            default:
                $dashboard =  General::getDashboard($subdepart);
            break;

        }
        return $dashboard;
    }

    public function modals() {
        $modals = [General::getModal("pin-projects"), General::getModal("add-notes")];

        if($this->System->App->tenant === "KUP") {
            if($this->Role->{$this->user}->sub_department == "registration") {
                $modals[] = General::getModal("add-letter-in");
            }
        }
        return $modals;
    }

    public function buttons() {
        $buttons = [General::getButton("add-notes")];
        if($this->System->App->tenant === "KUP") {
            switch($this->Role->{$this->user}->sub_department){
                case 'registration':
                    $buttons[] = General::getButton("add-letters");
                break;
            }
        }
        return $buttons;
    }
}