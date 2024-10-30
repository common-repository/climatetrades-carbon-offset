<?php if ( $GLOBALS["C_API_KEY"] ): ?>

<style>
  <?php include( plugin_dir_path(__FILE__).'../css/loading.css' ); ?>
  <?php include( plugin_dir_path(__FILE__).'../css/globals.css' ); ?>
  <?php include( plugin_dir_path(__FILE__).'../css/checkbox.css' ); ?>
  <?php include( plugin_dir_path(__FILE__).'../css/climatetrade_widget.css' ); ?>
</style>

<?php
  require_once( plugin_dir_path(__FILE__).'../api/api_request.php' );
  
  $product_id           =  wc_get_product_id_by_sku( WCT_SKU );
  $checkout = false;
  
  $amount_without_carbon = 0;
  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    if ( $cart_item['product_id'] == $product_id ) {
      $checkout = true;
    }else{
      $amount_without_carbon += $cart_item['line_subtotal'];
      // if( $checkout ) return;
    }
  }
  $api                 = new WCT_APIConfig( esc_html($GLOBALS["C_API_KEY"]) );
  $body                = array( "amount" => floatval( $amount_without_carbon ) * ($GLOBALS['C_COMPENSATION'] / 100), "currency" => "EUR" );
  $response            = $api->postRequest( esc_html($GLOBALS["URL_CALCULATE_OFFSET"]), $body );

  if( $response && array_key_exists('details', $response) ) return; // if get message key (Results exceeds tCO2 balance) widget not display

  if ($response && isset($response['tCO2'])) {
    $ton                 = number_format($response['tCO2'] * 1000, 0); // revisar api
    $info                = '
    <div class="container__info">
      <img src="'. plugin_dir_url(__FILE__)."../../admin/images/info.png" .'" class="info" />
      <div class="info--description fs-10 sourcesans--regular">
        '. __("Instrucciones del proceso", "widgetclimatetrade") .'
        <ol>
          <li>'. __("Seleccionar si quiere compensar la huella de carbono.", "widgetclimatetrade") .'</li>
          <li>'. __("Se te cobrará un porcentaje adicional para compensar.", "widgetclimatetrade") .'</li>
          <li>'. __("Te llegará un correo con la certificación de la compensación.", "widgetclimatetrade") .'</li>
        </ol>
      </div>
    </div>
    ';

    $payment_calculation = $amount_without_carbon * ( floatval($GLOBALS["C_PAYMENT"]) / 100 );
    $checked             = $checkout ? "checked": "";
    $checked_class       = $checkout ? ""       : "default";
    $wrapper_class       = $checkout ? ""       : "wrapper__widget--default";
    $array_text          = array(
      "basic"            => esc_html(__('Básico (¡Sí!, Quiero compensar mi huella)', 'widgetclimatetrade')),
      "kg"               => esc_html(sprintf(__('Compensar los %s Kg de mi huella de carbono ', 'widgetclimatetrade'), $ton)),
      "kg_price"         => esc_html(sprintf(__('Compensar los %s Kg de mi huella de carbono por %s€ ', 'widgetclimatetrade'), $ton, $payment_calculation))
    );
?>

<div class="widget_display">

  <div class="d-flex align-items-center flex-wrap wrapper__widget <?php echo esc_html($wrapper_class); ?> mt-30" 
    style="border: 1px solid <?php echo esc_html($GLOBALS["C_BORDER_COLOR"]); ?>; 
          background: <?php echo esc_html($GLOBALS["C_BACKGROUND_COLOR"]); ?>;">
    <label class="checkbox__container m-0">
      <input type="checkbox" id="add_compensation" name="add_compensation" class="<?php echo esc_html($checked_class); ?>" <?php echo esc_html($checked); ?>
        data-product-price="<?php echo floatval($payment_calculation) ?>"
        style="border: 1px solid <?php echo esc_html($GLOBALS["C_BORDER_COLOR"]); ?>; 
          background-color: <?php echo esc_html($GLOBALS["C_BORDER_COLOR"]); ?>;" />
    </label>

    <div class="d-flex align-items-center ml-15 mr-auto custom--width">
      <?php if($GLOBALS["C_IMAGE"]): ?>
        <div class="wrapper__image">
          <img src="<?php echo esc_html($GLOBALS["C_IMAGE"]); ?>" alt="" class="img-responsive"/>
        </div>
      <?php endif; ?>
      
      <div class="d-flex flex-column wrapper__description ml-15">
        <?php if( $GLOBALS["C_TITLE"] ):?>
          <div class="fs-18 grey1 sourcesans--bold"><?php echo esc_html($GLOBALS["C_TITLE"]); ?></div>
        <?php endif; ?>
        
        <?php if( $GLOBALS["C_DESCRIPTION"] ):?>
          <div class="fs-14 grey3 sourcesans--regular"><?php echo esc_html($GLOBALS["C_DESCRIPTION"]); ?></div>
        <?php endif; ?>

        <div class="d-flex fs-16 grey1 sourcesans--bold">
          <?php echo esc_html($array_text[$GLOBALS["C_CTA"]]); ?>
          <?php echo wp_kses_post($info); ?>
        </div>
          
        <!-- <input type="text" placeholder="test@climatetrade.com" value="" class="input--email" /> -->
        
        <div class="legal">
          <div class="d-flex mt-15">
            <a href="https://market.climatetrade.com/files/terms_en.pdf" target="_blank" class="sourcesans--regular fs-10 grey2"><?php _e('T&Cs aplicables', 'widgetclimatetrade'); ?></a>
            <?php if (esc_html($GLOBALS["C_POWERED"]) != 'powered') { ?>
            <div class="sourcesans--regular fs-10 grey4 ml-15"><?php _e('Powered by', 'widgetclimatetrade'); ?> <a href="https://climatetrade.com/" target="_blank">climateTrade</a></div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="h-100 d-flex flex-column justify-content-end">
      <a href="https://market.climatetrade.com/files/terms_en.pdf" target="_blank" class="sourcesans--regular fs-10 grey2"><?php _e('T&Cs aplicables', 'widgetclimatetrade'); ?></a>
      <?php if (esc_html($GLOBALS["C_POWERED"]) != 'powered') { ?>
      <div class="sourcesans--regular fs-10 grey4"><?php _e('Powered by', 'widgetclimatetrade'); ?> <a href="https://climatetrade.com/" target="_blank">climateTrade</a></div>
      <?php } ?>
    </div>
  </div>

</div>

<?php } endif; ?>
