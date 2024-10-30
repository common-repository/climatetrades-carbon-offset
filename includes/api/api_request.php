<?php
class WCT_APIConfig {
  
  public $header;
  public function __construct( $token ){
    $this->header = array(
      'Authorization' => 'Token '. $token,
      'Content-Type' => 'application/json'
    );
  }

  public function postRequest( $URL, $body ){

    $args = array(
      'body'        => wp_json_encode($body),
      'timeout'     => '5',
      'redirection' => '5',
      'httpversion' => '2.0',
      'blocking'    => true,
      'headers'     => $this->header,
    );

    $response = wp_remote_post( $URL, $args );

    if ( is_wp_error( $response ) ) {
      $error_message = $response->get_error_message();
      return "Something went wrong: $error_message";
    } else {
      
      return json_decode($response['body'], true);
    }

  }
}
?>
