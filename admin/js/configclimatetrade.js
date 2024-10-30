jQuery(document).ready(function($){
  $('#climatetrade_image').change( function() {
    $('.image-selected').text( this.files[0].name );
    $('#hidden_image').val( this.files[0].name );
  } )

  $('#climatetrade_description').keyup( function () {
    $('.counter--characters').text( `${this.value.length} / 120` );
  } )

  $('.trash--icon').click( function() {
    $('.image-selected').text( 'No hay ninguna imagen seleccionada' );
    $('#climatetrade_image').val('');
    $('#hidden_image').val('');
    $('.img-responsive').attr('src', '');
  } )

  $('.chevron--cta').click( function () {     
    let parent = $(this).parent('.header__cta');
    if( this.src.match(/chevronUp/) ){
      this.src = this.src.replace(/chevronUp/, "chevronDown");
      parent.next()
        .removeClass('d-block')
        .addClass('d-none');
    }else{
      this.src = this.src.replace(/chevronDown/, "chevronUp");
      parent.next()
        .removeClass('d-none')
        .addClass('d-block');
    }
  } )

  $('.chevron').click( function () {
    let parent = $(this).parent('.collapse__header');

    if( this.src.match(/chevronUp/) ){
      parent.next()
        .removeClass('d-block')
        .addClass('d-none');
    }else{
      parent.next()
        .removeClass('d-none')
        .addClass('d-block');
    }
  } )
});