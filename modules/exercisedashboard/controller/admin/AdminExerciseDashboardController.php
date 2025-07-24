<?php

class AdminExerciseDashboardController extends ModuleAdminController {
    public function __construct() {
        parent::__construct();
        $this->meta_title = $this->l('Exercice Dashboard');
        $this->bootstrap = true;
    }

    public function initContent() {
        parent::initContent();
        $this->context->smarty->assign([
            'message' => 'Bienvenue sur le Dashboard !'
        ]);
        $this->setTemplate('module:exercisedashboard/views/templates/admin/dashboard.tpl');
    }
}