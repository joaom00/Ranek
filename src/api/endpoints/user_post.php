<?php

function api_user_post($request) {

  $name = sanitize_text_field($request['name']);
  $email = sanitize_email($request['email']);
  $password = ($request['password']);
  $cep = sanitize_text_field($request['cep']);
  $street = sanitize_text_field($request['street']);
  $number = sanitize_text_field($request['number']);
  $neighborhood = sanitize_text_field($request['neighborhood']);
  $city = sanitize_text_field($request['city']);
  $uf = sanitize_text_field($request['uf']);

  $user_exists = username_exists($email);
  $email_exists = email_exists($email);

  if(!$user_exists && !$email_exists && $email && $password) {
    $user_id = wp_create_user($email, $password, $email);

    $response = array(
      'ID' => $user_id,
      'display_name' => $name,
      'first_name' => $name,
      'role' => 'subscriber'
    );

    wp_update_user($response);

    update_user_meta($user_id, 'cep', $cep);
    update_user_meta($user_id, 'street', $street);
    update_user_meta($user_id, 'number', $number);
    update_user_meta($user_id, 'neighborhood', $neighborhood);
    update_user_meta($user_id, 'city', $city);
    update_user_meta($user_id, 'uf', $uf);

  } else {
    $response = new WP_Error('email', 'Email já cadastrado.', array('status' => 403));
  }

  return rest_ensure_response($response);
}

function register_api_user_post() {
  register_rest_route('api', '/user', array(
    array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_user_post',
    ),
  ));
}

add_action('rest_api_init', 'register_api_user_post');

?>