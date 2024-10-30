<?php
  if( !defined('WP_UNINSTALL_PLUGIN') ){
    die();
  }

  delete_option('climatetrade_api_key');
  delete_option('climatetrade_compensation');
  delete_option('climatetrade_payment');
  delete_option('climatetrade_image');
  delete_option('climatetrade_title');
  delete_option('climatetrade_description');
  delete_option('climatetrade_cta');
  delete_option('climatetrade_principal_color');
  delete_option('climatetrade_background_color');
?>