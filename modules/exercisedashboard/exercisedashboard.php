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

    public function getContent() {
        $output = '';

        if (Tools::isSubmit('submit_' . $this->name)) {
            Configuration::updateValue('EXD_API_KEY', Tools::getValue('EXD_API_KEY'));
            Configuration::updateValue('EXD_UPDATE_FREQUENCY', Tools::getValue('EXD_UPDATE_FREQUENCY'));
            Configuration::updateValue('EXD_ENABLED', Tools::getValue('EXD_ENABLED'));

            $output .= $this->displayConfirmation($this->l('Configuration sauvegardée.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm() {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Paramètres du module'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Clé API'),
                        'name' => 'EXD_API_KEY',
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Fréquence de mise à jour'),
                        'name' => 'EXD_UPDATE_FREQUENCY',
                        'options' => [
                            'query' => [
                                ['id' => 'manual', 'name' => $this->l('Manuelle')],
                                ['id' => '24h', 'name' => $this->l('Toutes les 24h')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activer le module'),
                        'name' => 'EXD_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Oui'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Non'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->fields_value = [
            'EXD_API_KEY' => Configuration::get('EXD_API_KEY'),
            'EXD_UPDATE_FREQUENCY' => Configuration::get('EXD_UPDATE_FREQUENCY'),
            'EXD_ENABLED' => Configuration::get('EXD_ENABLED'),
        ];

        return $helper->generateForm([$fields_form]);
    }
}
