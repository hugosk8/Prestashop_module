<?php

// Vérifie qu'on est bien dans le contexte Prestashop
if (!defined('_PS_VERSION_')) {
    exit;
}

class Exercisedashboard extends Module {
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
        return parent::install() && $this->installDb();
    }

    public function installDb() {
        $sql = "
            CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "exd_btc_price` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `price` DECIMAL(20,6) NOT NULL,
                `fetched_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;
        ";

        return Db::getInstance()->Execute($sql);
    }
    
    public function uninstall() {
        return parent::uninstall() && $this->uninstallDb();
    }

    public function uninstallDb() {
        $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "exd_btc_price`";
        return Db::getInstance()->Execute($sql);
    }

    public function getContent() {
        $output = '';

        if (Tools::isSubmit('submit_' . $this->name)) {
            Configuration::updateValue('EXD_API_KEY', Tools::getValue('EXD_API_KEY'));
            Configuration::updateValue('EXD_UPDATE_FREQUENCY', Tools::getValue('EXD_UPDATE_FREQUENCY'));
            Configuration::updateValue('EXD_ENABLED', Tools::getValue('EXD_ENABLED'));

            $output .= $this->displayConfirmation($this->l('Configuration sauvegardée.'));
        }

        $btc = $this->fetchBitcoinPrice();
        if ($btc !== null) {
            $this->saveBitcoinPrice($btc);
            $output .= $this->displayConfirmation($this->l('Prix BTC sauvegardé : ' . $btc . '€'));
        } else {
            $output .= $this->displayError($this->l('Erreur lors de la récupération du prix BTC.'));
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
        $helper->submit_action = 'submit_' . $this->name;

        return $helper->generateForm([$fields_form]);
    }

    public function fetchBitcoinprice() {
        $url = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=eur';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);

        $data = json_decode($response, true);

        if (isset($data['bitcoin']['eur'])) {
            return $data['bitcoin']['eur'];
        }
        return null;
    }

    public function saveBitcoinPrice($price) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'exd_btc_price` (`price`, `fetched_at`) VALUES (' . (float)$price . ', NOW())';
        return Db::getInstance()->execute($sql);
    }
}
