<style>
  <?php include( plugin_dir_path(__FILE__).'../../includes/css/globals.css' ); ?>
  <?php include( plugin_dir_path(__FILE__).'../css/configclimatetrade.css' ); ?>
  <?php include( plugin_dir_path(__FILE__).'../css/configurationform.css' ); ?>
</style>


<?php if( !is_plugin_active('woocommerce/woocommerce.php') ): ?>
<div class="overlay">
  <div class="disable__plugin--message">
    <div class="alert">
      <b class="fs-18"><?php _e("Información","widgetclimatetrade"); ?>:</b>
      <p class="fs-16"><?php _e("Este plugin necesita tener WooCommerce instalado y activado. Por favor, instala o activa WooCommerce y vuelve a esta página.","widgetclimatetrade"); ?></p>  
    </div>
  </div>
</div>
<?php endif; ?>

<div class="container-fluid config__container">
  <form method="post" action="options.php" enctype="multipart/form-data">
    
    <div class="d-flex justify-content-between">
      <!-- wrapper__configuration -->
      <div class="wrapper__configuration">
        <div class="custom__collapse">
          <div class="collapse__header grey1 fs-18 d-flex justify-content-between align-items-center">
            <div class="title__collapse mr-auto"><?php _e('Configuración General', 'widgetclimatetrade'); ?></div>
          </div>
          
          <!-- collapse__body -->
          <div class="collapse__body">
            <?php
              settings_fields( 'climatetrade_plugin_settings' );
              do_settings_sections( 'climatetrade' );
            ?>
          </div>
          <!-- collapse__body -->
        </div>
      </div>
      <!-- wrapper__configuration -->
      
      <!-- wrapper_publish -->
      <div class="wrapper__publish">
        <div class="collapse__header grey1 fs-18 d-flex justify-content-between align-items-center">
          <div class="title__collapse mr-auto"><?php _e('Publicar', 'widgetclimatetrade'); ?></div>
        </div>
        <div class="text-right">
          <input type="submit" name="submit" id="submit" class="btn--update mt-20" value="<?php _e('Guardar cambios', 'widgetclimatetrade'); ?>">
        </div>
      </div>
      <!-- /wrapper__publish -->    
    </div>

  </form>
</div>

<script>
  <?php include( plugin_dir_path(__FILE__).'../js/configclimatetrade.js' ); ?>
</script>
