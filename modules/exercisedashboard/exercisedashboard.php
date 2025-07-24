<?php

// Vérifie qu'on est bien dans le contexte Prestashop
if (!defined('_PS_VERSION_')) {
    exit;
}

class ExerciseDashboard extends Module {
    public function __construct() {
        $this->name = 'exercisedashboard';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Hugo Sauvage';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Exercice Dashboard');
        $this->description = $this->l('Module d\'exercice pour afficher un dashboard.');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }
    
    public function install() {
        return parent::install();
    }
    
    public function uninstall() {
        return parent::uninstall();
    }
}