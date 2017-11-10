<?php

/**
 * Plugin Name: CdP Coins
 * Description: Integração com a loja de Coins
 * Author: Lucas Formiga
 * Author URI: http://cdp-wow.com.br
 * Version: 1.4
 *
 * @package WooCommerce_CDP
 */

defined( 'ABSPATH' ) or die( 'SAI CAPIROTO xD' );

const DB_Driver = "";
const DB_Hostname = "";
const DB_Port = "";
const DB_Name = "";
const DB_Username = "";
const DB_Password = "";


function is_registered( $email ) {

  $consulta = "SELECT * FROM dw_balance WHERE email = :email";

  $sql = new PDO(DB_Driver . ':host=' . DB_Hostname . ';port=' . DB_Port .';dbname=' . DB_Name, DB_Username, DB_Password);

  $set = $sql->prepare($consulta);

  $set->bindValue(':email', $email, PDO::PARAM_STR);

  $set->execute();

  if ($set->rowCount() > 0):
    return true
  else:
    return false;
  endif;

}

function register_customer( $email ) {

  $consulta = "INSERT INTO `dw_balance` (`email`, `balance`) VALUES (:email, '0')";

  $sql = new PDO(DB_Driver . ':host=' . DB_Hostname . ';port=' . DB_Port .';dbname=' . DB_Name, DB_Username, DB_Password);

  $set = $sql->prepare($consulta);

  $set->bindValue(':email', $email, PDO::PARAM_STR);

  $set->execute();

}

function add_balance( $email ) {

  $consulta = "UPDATE ";

  $sql = new PDO(DB_Driver . ':host=' . DB_Hostname . ';port=' . DB_Port .';dbname=' . DB_Name, DB_Username, DB_Password);

  $set = $sql->prepare($consulta);

  $set->bindValue(':email', $email, PDO::PARAM_STR);

  $set->execute();

}

function execute_when_completed( $order_id ) {

  $get_order = new WC_Order( $order_id );

  $items = $get_order->get_items();

  $email = $get_order->billing_email;

  $coins = 0;

  foreach ( $items as $item ):
    switch ( $item['product_id'] ):
      case 157:
        $price = 1000 * $item['qty'];
        $coins += $price;
      break;
      case 133:
        $price = 500 * $item['qty'];
        $coins += $price;
      break;
    endswitch;
  endforeach;

  if ( $coins > 0 ):
    if (is_registered($email)):
      add_balance($email);
    else:
      register_customer($email);
      add_balance($email);
    endif;
  endif;

}

add_action( 'woocommerce_order_status_completed' , 'execute_when_completed' );
