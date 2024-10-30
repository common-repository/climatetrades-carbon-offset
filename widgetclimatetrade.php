<?php
  /*
   Plugin Name: ClimateTrade's Carbon Offset
   Plugin URI: https://climatetrade.com
   description: ClimateTrade’s easy to integrate widget allows your customers to offset the carbon footprint of their purchases in just a few clicks
   Version: 1.0.0
   Author: ClimateTrade
   Author URI: https://developers.climatetrade.com/es/products/widget/
   Text Domain: widgetclimatetrade
   Domain Path: /languages/
   License: GPLv2 or later
   
  */
  
  /*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <https://www.gnu.org/licenses/>.
  */
  
  define( 'WCT_SKU', 'CARBONFOOTPRINT_CLIMATETRADE' );
  define( 'WCT_URL_API', 'https://api.climatetrade.com/market' ); // PRE: https://pre.backend.climatetrade.net/api ,  AWS: https://pre.api.climatetrade.com/market , PROD: https://api.climatetrade.com/market
  
  register_activation_hook(__FILE__, array('WidgetClimateTrade', 'wct_activate'));
  
  register_deactivation_hook(__FILE__, array('WidgetClimateTrade', 'wct_deactivate'));

  /**
   * Load variables and init translations
   */
  add_action('init', array('WidgetClimateTrade', 'wct_init'));
  
  if (is_admin()) {
    add_action('init', array('WidgetClimateTrade', 'wct_admin_init')); 
  }

  $C_API_KEY = get_option("climatetrade_api_key");
  $C_COMPENSATION = get_option("climatetrade_compensation", "5");
  $C_PAYMENT = get_option("climatetrade_payment", "1");
  $C_IMAGE = get_option("climatetrade_image");
  $C_TITLE = get_option("climatetrade_title");
  $C_DESCRIPTION = get_option("climatetrade_description");
  $C_CTA = get_option("climatetrade_cta");
  $C_BORDER_COLOR = get_option("climatetrade_principal_color");
  $C_BACKGROUND_COLOR = get_option("climatetrade_background_color");
  $C_POWERED = get_option("climatetrade_powered");
  $URL_CALCULATE_OFFSET = WCT_URL_API . "/v1/widget/calculate_offset_given_amount/";
  $URL_OFFSET = WCT_URL_API . "/v1/offsets/";


  class WidgetClimateTrade {

    public static function wct_init() {

      load_plugin_textdomain(
        'widgetclimatetrade', 
        false, 
        basename( dirname( __FILE__ ) ) . '/languages' 
      );

    }

    public static function wct_admin_init() {
      add_action('admin_init', array('WidgetClimateTrade', 'wct_form_settings'));
      add_action('admin_menu', array('WidgetClimateTrade', 'wct_menu_widget_configuration'));
    }

    /**
     * Add elementos to menu sidebar
     */
    
    public static function wct_menu_widget_configuration(){
      add_menu_page(
        'Climatetrade', //Titulo de la pagina
        'Climatetrade', //Titulo del menu
        'manage_options', //Capability
        'climatetrade', // slug
        // plugin_dir_path(__FILE__).'admin/includes/configurations.php',
        array('WidgetClimateTrade', 'wct_widget_form') // function callback
      );
    }

    public static function wct_widget_form(){
      include( plugin_dir_path(__FILE__).'admin/includes/configurations.php' );
    }

    /**
     * Add settings configuration
     */    
    public static function wct_form_settings(){
      
      // register_setting( 'climatetrade_plugin_settings', 'climatetrade_plugin_settings' );

      add_settings_section(
        'climatetrade_plugin_settings', //id
        '', //title
        null, //callback
        'climatetrade' //page
      );

      add_settings_field( 
        'wct_settings_form', //id
        null, //title
        array('WidgetClimateTrade', 'wct_settings_form'), // callback
        'climatetrade', //page
        'climatetrade_plugin_settings' //section 
      );
      
      register_setting("climatetrade_plugin_settings", "climatetrade_api_key", array("sanitize_callback" => "sanitize_text_field"));
      register_setting("climatetrade_plugin_settings", "climatetrade_compensation", array("type" => "number"));
      register_setting("climatetrade_plugin_settings", "climatetrade_payment", array("type" => "number"));
      register_setting("climatetrade_plugin_settings", "climatetrade_image", array('WidgetClimateTrade', 'wct_settings_form'));
      register_setting("climatetrade_plugin_settings", "climatetrade_title", array("sanitize_callback" => "sanitize_text_field"));
      register_setting("climatetrade_plugin_settings", "climatetrade_description", array("sanitize_callback" => "sanitize_text_field"));
      register_setting("climatetrade_plugin_settings", "climatetrade_cta");
      register_setting("climatetrade_plugin_settings", "climatetrade_principal_color");
      register_setting("climatetrade_plugin_settings", "climatetrade_background_color");
      register_setting("climatetrade_plugin_settings", "climatetrade_powered");

    }

    public static function wct_settings_form() {

      if( $GLOBALS["C_CTA"] ){
        $basic    = $GLOBALS["C_CTA"] == "basic" ? "checked"   : "";
        $kg       = $GLOBALS["C_CTA"] == "kg" ? "checked"      : "";
        $kg_price = $GLOBALS["C_CTA"] == "kg_price" ? "checked": "";
      }else{
        $basic    = "checked";
        $kg       = "";
        $kg_price = "";
      }
  
      $powered    = isset($GLOBALS["C_POWERED"]) && $GLOBALS["C_POWERED"] == "powered" ? "checked" : "";
      
  
      $border_color     = $GLOBALS["C_BORDER_COLOR"] ? $GLOBALS["C_BORDER_COLOR"]: "#003A6C";
      $background_color = $GLOBALS["C_BACKGROUND_COLOR"] ? $GLOBALS["C_BACKGROUND_COLOR"] : "#F1F8FF";
      ?>
      
        <!-- API Key -->
        <label class="roboto--bold grey1 fs-14 mt-30"><?php _e('APIKey', 'widgetclimatetrade'); ?></label>
        <input type="text" id="climatetrade_api_key" name="climatetrade_api_key" value="<?php echo esc_html($GLOBALS["C_API_KEY"]); ?>" placeholder="ej. 112de8c505d7dde213cd1236ea6bd2e7123124" class="custom__input mt-10 mb-10" />
        <a href="https://developers.climatetrade.com/es/products/widget/" target="_blank" class="blue1 fs-12 sourcesans--regular"><?php _e('¿Cómo obtener el APIKey?', 'widgetclimatetrade'); ?></a>
        <div class="custom__line mt-20 mb-20"></div>
        <!-- /API Key -->
  
        <!-- Compensation -->
        <div class="d-flex justify-content-start">
          <div class="wrapper__percent mr-40">
            <label class="roboto--bold grey1 fs-14">
              <?php _e('% Compensación', 'widgetclimatetrade'); ?>
            </label>
            <div class="d-flex">
              <input type="number" id="climatetrade_compensation" name="climatetrade_compensation" value="<?php echo floatval($GLOBALS["C_COMPENSATION"]); ?>" placeholder="10" class="custom__input custom__input--percent mt-10" />
              <div class="percent d-flex align-items-center mt-10">%</div>
            </div>
          </div>
          <div class="wrapper__percent mr-40">
            <label class="roboto--bold grey1 fs-14">
              <?php _e('% Cobro','widgetclimatetrade'); ?>
            </label>
            <div class="d-flex">
              <input type="number" id="climatetrade_payment" name="climatetrade_payment" value="<?php echo floatval($GLOBALS["C_PAYMENT"]); ?>" placeholder="15" class="custom__input custom__input--percent mt-10" />
              <div class="percent d-flex align-items-center mt-10">%</div>
            </div>
          </div>
        </div>
        <div class="custom__line mt-20 mb-20"></div>
        <!-- /Compensation -->
  
        <!-- Image -->
        <label class="roboto--bold grey1 fs-14"><?php _e('Imagen','widgetclimatetrade'); ?></label>
        <div class="wrapper__fileupload">
          <input type="file" name="climatetrade_image" id="climatetrade_image" value="<?php echo esc_html($GLOBALS["C_IMAGE"]); ?>" hidden/>
          <input type="text" id="hidden_image" name="hidden_image" value="<?php echo esc_html($GLOBALS["C_IMAGE"]); ?>" hidden />
          <div class="d-flex align-items-center">
            <div class="roboto--bold fs-12 grey1 mr-10 image-selected"><?php _e('No hay ninguna imagen seleccionada', 'widgetclimatetrade'); ?></div>
            <label for="climatetrade_image" class="file--upload"><?php _e('Añadir imagen', 'widgetclimatetrade'); ?></label>
            <img src="<?php echo plugin_dir_url(__FILE__).'admin/images/trash.png' ?>" class="ml-10 trash--icon" />
          </div>
          <img src="<?php echo esc_html($GLOBALS["C_IMAGE"]); ?>" alt="" class="mt-10 img-responsive"/>
        </div>
        <div class="custom__line mt-20 mb-20"></div>
        <!-- /Image -->
  
        <!-- Title description -->
        <div class="wrapper__input">
          <label class="roboto--bold grey1 fs-14"><?php _e("Título", "widgetclimatetrade"); ?></label>
          <input type="text" id="climatetrade_title" name="climatetrade_title" value="<?php echo esc_html($GLOBALS["C_TITLE"]); ?>" placeholder="<?php _e('Compensa tu huella de carbono', 'widgetclimatetrade'); ?>" class="custom__input mt-10">
        </div>
        
        <div class="wrapper__input mt-30">
          <label class="roboto--bold grey1 fs-14"><?php _e('Descripción', 'widgetclimatetrade'); ?></label>
          <input type="text" id="climatetrade_description" name="climatetrade_description" value="<?php echo esc_html($GLOBALS["C_DESCRIPTION"]); ?>" placeholder="<?php _e('Al compensar la huella de carbono ayudaras al planeta', 'widgetclimatetrade'); ?>" class="custom__input mt-10">
          <span class="sourcesans--regular fs-8 counter--characters">0/120</span>
          <div class="custom__line mt-20 mb-20"></div>
        </div>
        <!-- /Title description -->
  
        <!-- CTA -->
        <label class="roboto--bold grey1 fs-14">CTA</label>
        <div class="wrapper__cta mt-10">
          <div class="header__cta d-flex justify-content-between align-items-center">
            <div class="sourcesans--regular fs-12"><?php _e('Seleciona el CTA', 'widgetclimatetrade'); ?></div>
          </div>
          <div class="body__cta mt-30">
            <div class="d-flex align-items-center mb-10">
              <input type="radio" id="basic" name="climatetrade_cta" value="basic" <?php echo esc_html($basic); ?> />
              <div class="sourcesans--regular grey3 fs-12"><?php _e('Básico (¡Sí!, Quiero compensar mi huella)', 'widgetclimatetrade'); ?></div>
            </div>
            <div class="d-flex align-items-center mb-10">
              <input type="radio" id="kg" name="climatetrade_cta" value="kg" <?php echo esc_html($kg); ?> />
              <div class="sourcesans--regular grey3 fs-12"><?php _e('Añadir Kg (Compensar los ___ Kg de mi huella de carbono)', 'widgetclimatetrade'); ?></div>
            </div>
            <div class="d-flex align-items-center">
              <input type="radio" id="kg_price" name="climatetrade_cta" value="kg_price" <?php echo esc_html($kg_price); ?> />
              <div class="sourcesans--regular grey3 fs-12"><?php _e('Añadir Kg y € (Compensar los ___ Kg de mi huella de carbono por ___ €)', 'widgetclimatetrade'); ?></div>
            </div>
          </div>
        </div>
        <div class="custom__line mt-20 mb-20"></div>
        <!-- /CTA -->
  
        <!-- Color picker -->
        <div class="wrapper__cta mt-10">
          <div class="d-flex align-items-center">
            <label class="roboto--bold grey1 fs-14 m-0"><?php _e('Color principal', 'widgetclimatetrade'); ?></label>
            <input type="color" id="climatetrade_principal_color" name="climatetrade_principal_color" value="<?php echo esc_html($border_color); ?>" class="ml-10 input--color">
          </div>
          <div class="d-flex align-items-center mt-20">
            <label class="roboto--bold grey1 fs-14 m-0"><?php _e('Color de fondo', 'widgetclimatetrade'); ?></label>
            <input type="color" id="climatetrade_background_color" name="climatetrade_background_color" value="<?php echo esc_html($background_color); ?>" class="ml-10 input--color">
          </div>
        </div>
        <!-- /Color picker -->
        <div class="custom__line mt-20 mb-20"></div>
  
        <!-- Powered by -->
        <label class="roboto--bold grey1 fs-14"><?php _e('Powered by', 'widgetclimatetrade'); ?></label>
        <div class="wrapper__cta mt-10">
          <div class="header__cta d-flex justify-content-between align-items-center">
            <div class="sourcesans--regular fs-12"><?php _e('Selecciona si mostrar Powered by', 'widgetclimatetrade'); ?></div>
          </div>
          <div class="body__cta mt-30">
            <div class="d-flex align-items-center mb-10">
              <input type="checkbox" id="powered" name="climatetrade_powered" value="powered" <?php echo esc_html($powered); ?> />
              <div class="sourcesans--regular grey3 fs-12"><?php _e('No mostrar Powered By', 'widgetclimatetrade'); ?></div>
            </div>
          </div>
        </div>
        <div class="custom__line mt-20 mb-20"></div>
        <!-- /Powered by -->
  
      <?php
    }

    public static function wct_handle_file_upload($options)
    {
      //check if user had uploaded a file and clicked save changes button
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      if(!empty($_FILES["climatetrade_image"]["tmp_name"]) || strlen($_POST["hidden_image"]) == 0 )
      {
        $urls = wp_handle_upload($_FILES["climatetrade_image"], array('test_form' => FALSE));
        $temp = $urls["url"];
        return $temp;
      }

      //no upload. old file url is the new value.
      return $GLOBALS["C_IMAGE"];
    }

    public static function wct_activate(){
      if ( 
        in_array( 
          'woocommerce/woocommerce.php', 
          apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
        ) 
      ) {
        $product_id   =  wc_get_product_id_by_sku( WCT_SKU );
    
        if($product_id == 0){
          $product = new WC_Product();
          $product->set_name(__('Compensación de huella de carbono', 'widgetclimatetrade'));
          $product->set_status('publish');
          $product->set_featured(false);
          $product->set_catalog_visibility('hidden');
          //$product->set_description('Descripción larga de la compensación de huella de carbono');
          //$product->set_short_description('Descripción corta de la compensación de la huella de carbono');
    
          //Set SKU
          $product->set_sku( WCT_SKU );
          $product->set_price( 0 );
          $product->set_regular_price( 0 );
          $product->save();
        }
      } else {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( __( 'Por favor instala y activa WooCommerce', 'widgetclimatetrade' ), __('Revisar la dependencia del plugin', 'widgetclimatetrade'), array( 'back_link' => true ) );
      }
      
    }
    public static function wct_deactivate(){
      if ( 
        in_array( 
          'woocommerce/woocommerce.php', 
          apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
        ) 
      ) {
        $product_id   =  wc_get_product_id_by_sku( WCT_SKU );
        if( $product_id > 0 ){
          wp_delete_post( $product_id );
        }
      }
      
    }
  }

  /**
   * Add elements in woocommerce cart
   */
  add_action( 'woocommerce_checkout_before_customer_details', 'wct_insert_widget_in_cart' );
  function wct_insert_widget_in_cart() { 
    include( plugin_dir_path(__FILE__).'includes/widget/widget_view.php' );
  }

  /**
   * Add ajax requests
   */
  add_action('wp_enqueue_scripts', 'wct_climatetrade_ajax_js');
  function wct_climatetrade_ajax_js(){
    if ( 
      in_array( 
        'woocommerce/woocommerce.php', 
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
      ) 
    ) {
      if (!is_checkout()) return;
  
      wp_register_script('climatetrade_script', plugins_url('includes/js/widget_climatetrade.js', __FILE__), array('jquery'), '1', true );
      wp_enqueue_script('climatetrade_script');
  
      wp_localize_script('climatetrade_script','climatetrade_vars',['ajaxurl'=>admin_url('admin-ajax.php')]);
    }
  }

  add_action('wp_ajax_nopriv_climatetrade_updatecart','wct_update_cart');
  add_action('wp_ajax_climatetrade_updatecart','wct_update_cart');
  function wct_update_cart(){
    $product_id = wc_get_product_id_by_sku( WCT_SKU );
    $operation  = sanitize_text_field($_POST['operation']);
    $amount     = floatval($_POST['amount']);
    $quantity   = 1;
  

    if ( $operation === 'add_to_cart' ) {
      $cart_item_data = array('price' => $amount);
      WC()->cart->add_to_cart( $product_id, $quantity, null, null, $cart_item_data );
      WC()->cart->calculate_totals();
      WC()->cart->set_session();

      //var_dump($product_id);
      // echo 'Elementos agregados al carrito';
    } elseif ( $operation === 'delete_item_cart' ) {
      foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( $cart_item['product_id'] == $product_id ) {
          WC()->cart->remove_cart_item( $cart_item_key );
        }
      }
      // echo 'Elementos eliminados en el carrito';
    }

    wp_die();
  }

  /**
   * Update custom price before calculate_totals
   */
  add_action( 'woocommerce_before_calculate_totals', 'wct_add_custom_price' );
  function wct_add_custom_price( $cart_object ) {
      $product_id   =  wc_get_product_id_by_sku( WCT_SKU ); 
      foreach ( $cart_object->cart_contents as $key => $value ) {
        if ( $value['product_id'] === $product_id ) {
          if(WC()->version < "3.0.0")
            $value['data']->price = $value['price'];
          else
            $value['data']->set_price($value['price']);
        }
      }
  }

  /**
   * Detect when status order change to processing
   */
  add_action( 'woocommerce_order_status_processing', 'wct_action_woocommerce_order_status_processing', 10, 1 ); 
  function wct_action_woocommerce_order_status_processing( $order_id ) { 
    include( plugin_dir_path(__FILE__).'includes/api/api_request.php' );
    
    $order = wc_get_order( $order_id );
    $original_amount = 0;
    foreach( $order->get_items() as $item_id => $item ){
      if( $item->get_product()->get_sku() != WCT_SKU )
        $original_amount += $item->get_subtotal();
    }

    $amount_compensation = $original_amount * ( $GLOBALS["C_COMPENSATION"] / 100 );

    // Calculate offset given amount
    $api       = new WCT_APIConfig( esc_html($GLOBALS["C_API_KEY"]) );
    $body      = array( "amount" => floatval( $amount_compensation ), "currency" => "EUR" );
    $offset [] = $api->postRequest( esc_html($GLOBALS["URL_CALCULATE_OFFSET"]), $body );
    
    $body_fields = array(
      "offsets" => array(
        array(
          "project" => $offset[0]["project_id"],
          "co2_amount" => $offset[0]["tCO2"] 
        )
      ),
      "ticket_data" => array(
        "first_name" => $order->get_billing_first_name(),
        "last_name" => $order->get_billing_last_name(),
        "email" => $order->get_billing_email()
      )
    );

    $response = $api->postRequest( esc_html($GLOBALS["URL_OFFSET"]), $body_fields );

  };

?>
