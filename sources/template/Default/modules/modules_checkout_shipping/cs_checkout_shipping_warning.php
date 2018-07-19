<?php
/**
 * cs_checkout_shipping_warning.php
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licence

*/

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class cs_checkout_shipping_warning {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_shipping_warning_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_shipping_warning_description');

      if (defined('MODULE_CHECKOUT_SHIPPING_WARNING_STATUS')) {
        $this->sort_order = MODULE_CHECKOUT_SHIPPING_WARNING_SORT_ORDER;
        $this->enabled = (MODULE_CHECKOUT_SHIPPING_WARNING_STATUS == 'True');
      }
     }

    public function  execute() {

      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_GET['Checkout']) && isset($_GET['Shipping'])) {

        $content_width = (int)MODULE_CHECKOUT_SHIPPING_WARNING_CONTENT_WIDTH;

        $warning = '<!-- start cs_checkout_shipping_warning -->'. "\n";

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/checkout_shipping_warning'));

        $warning .= ob_get_clean();

        $warning .= '<!--  end cs_checkout_shipping_warning -->' . "\n";

        $CLICSHOPPING_Template->addBlock($warning, $this->group);
      }
    } // public function  execute

    public function  isEnabled() {
      return $this->enabled;
    }

    public function  check() {
      return defined('MODULE_CHECKOUT_SHIPPING_WARNING_STATUS');
    }

    public function  install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_WARNING_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_WARNING_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_WARNING_SORT_ORDER',
          'configuration_value' => '140',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_CHECKOUT_SHIPPING_WARNING_STATUS',
        'MODULE_CHECKOUT_SHIPPING_WARNING_CONTENT_WIDTH',
        'MODULE_CHECKOUT_SHIPPING_WARNING_SORT_ORDER'
      );
    }
  }