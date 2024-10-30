jQuery(document).ready(function($){
  $('#add_compensation').change( function() {
    let amount = $( this ).attr('data-product-price');
    // console.log( 'amount', amount )
    // let product_quantity = $( this ).attr('data-quantity');

    if( $( this ).is(':checked') ){
      $( this ).removeClass('default');
      $('.wrapper__widget').removeClass('wrapper__widget--default');
      ajax_request('add_to_cart', amount);
    } else {
      $( this ).addClass('default');
      $('.wrapper__widget').addClass('wrapper__widget--default');
      ajax_request('delete_item_cart', null);
    }
  } )

  function ajax_request( operation, amount ){
    $.ajax({
      url : climatetrade_vars.ajaxurl,
      type: 'post',
      data: {
        action: 'climatetrade_updatecart',
        operation: operation,
        amount: amount
      },
      beforeSend: function(){
        // $('.lds-ring').removeClass('d-none');
        // $('.widget_display').addClass('d-none');
        console.log('entro aqui');
      },
      success: function(){
        // $('.lds-ring').addClass('d-none');
        // $('.widget_display').removeClass('d-none');
        $('body').trigger('update_checkout');
      }
    });
  }
});
