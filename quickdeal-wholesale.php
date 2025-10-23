<?php
/*
Plugin Name: QuickDeal Wholesale (Pro-ready)
Description: Tiered wholesale pricing per product (JSON storage).
Version: 1.1
*/

if (!defined('ABSPATH')) exit;

add_action('woocommerce_product_options_pricing', function(){
  global $post;
  $tiers = get_post_meta($post->ID, '_qd_bulk_tiers', true) ?: [];
  echo '<div class="options_group">';
  woocommerce_wp_textarea_input(array(
    'id' => '_qd_bulk_tiers_json',
    'label' => __('Bulk price tiers (JSON)', 'quickdeal'),
    'description' => __('Example: [{"qty":1,"price":120},{"qty":10,"price":110},{"qty":50,"price":100}]'),
    'value' => esc_textarea(json_encode($tiers))
  ));
  echo '</div>';
});

add_action('woocommerce_process_product_meta', function($post_id){
  if (isset($_POST['_qd_bulk_tiers_json'])){
    $raw = wp_kses_post($_POST['_qd_bulk_tiers_json']);
    $parsed = json_decode($raw, true);
    if (is_array($parsed)) update_post_meta($post_id, '_qd_bulk_tiers', $parsed);
  }
});

add_action('woocommerce_before_calculate_totals', function($cart){
  if (is_admin() && !defined('DOING_AJAX')) return;
  $product_totals = [];
  foreach ($cart->get_cart() as $item){
    $pid = $item['product_id'];
    if (!isset($product_totals[$pid])) $product_totals[$pid] = 0;
    $product_totals[$pid] += $item['quantity'];
  }
  foreach ($cart->get_cart() as $cart_item_key => $cart_item){
    $pid = $cart_item['product_id'];
    $tiers = get_post_meta($pid, '_qd_bulk_tiers', true);
    if (empty($tiers)) continue;
    usort($tiers, function($a,$b){ return $b['qty'] <=> $a['qty']; });
    $total_qty = $product_totals[$pid];
    foreach ($tiers as $t){
      if ($total_qty >= intval($t['qty'])){
        $cart_item['data']->set_price(floatval($t['price']));
        break;
      }
    }
  }
}, 20, 1);
