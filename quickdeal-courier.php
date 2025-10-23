<?php
/*
Plugin Name: QuickDeal Courier (RedX & Steadfast)
Description: Integrations (skeleton) for RedX and Steadfast. Configure API keys in settings.
Version: 1.2
*/

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function(){
  add_submenu_page('woocommerce', 'QuickDeal Courier', 'QuickDeal Courier', 'manage_options', 'qd-courier', 'qd_courier_page');
});

function qd_courier_page(){
  if (isset($_POST['qd_save'])){
    update_option('qd_courier_redx_key', sanitize_text_field($_POST['qd_redx_key']));
    update_option('qd_courier_steadfast_key', sanitize_text_field($_POST['qd_steadfast_key']));
    update_option('qd_courier_provider', sanitize_text_field($_POST['qd_provider']));
    echo '<div class="updated"><p>Saved.</p></div>';
  }
  $redx = esc_attr(get_option('qd_courier_redx_key',''));
  $steady = esc_attr(get_option('qd_courier_steadfast_key',''));
  $provider = esc_attr(get_option('qd_courier_provider','redx'));
  ?>
  <div class="wrap"><h1>QuickDeal Courier Settings</h1>
  <form method="post">
    <table class="form-table">
      <tr><th>Provider</th><td>
        <select name="qd_provider">
          <option value="redx" <?php selected($provider,'redx'); ?>>RedX</option>
          <option value="steadfast" <?php selected($provider,'steadfast'); ?>>Steadfast</option>
        </select>
      </td></tr>
      <tr><th>RedX API Key</th><td><input name="qd_redx_key" value="<?php echo $redx; ?>" class="regular-text"></td></tr>
      <tr><th>Steadfast API Key</th><td><input name="qd_steadfast_key" value="<?php echo $steady; ?>" class="regular-text"></td></tr>
    </table>
    <p><input type="submit" name="qd_save" class="button-primary" value="Save"></p>
  </form></div>
  <?php
}

add_action('woocommerce_order_status_processing', function($order_id){
  $provider = get_option('qd_courier_provider','redx');
  $order = wc_get_order($order_id);
  if (!$order) return;
  $payload = array(
    'order_id' => $order->get_id(),
    'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
    'phone' => $order->get_billing_phone(),
    'address' => $order->get_billing_address_1(),
    'items' => array(),
  );
  foreach ($order->get_items() as $item){
    $payload['items'][] = array('product_id' => $item->get_product_id(), 'qty' => $item->get_quantity());
  }

  if ($provider === 'redx'){
    $apikey = get_option('qd_courier_redx_key');
    $endpoint = 'https://api.redx.example/create_shipment';
    $headers = array('Content-Type'=>'application/json','API-Key'=>$apikey);
  } else {
    $apikey = get_option('qd_courier_steadfast_key');
    $endpoint = 'https://api.steadfast.example/v1/shipments';
    $headers = array('Content-Type'=>'application/json','Authorization'=>'Bearer '.$apikey);
  }
  if (empty($apikey)) {
    error_log('QuickDeal Courier: API key not configured for ' . $provider);
    return;
  }
  $args = array('body'=>wp_json_encode($payload),'headers'=>$headers,'timeout'=>20);
  $resp = wp_remote_post($endpoint, $args);
  if (is_wp_error($resp)){
    error_log('Courier API error: '.$resp->get_error_message());
    return;
  }
  update_post_meta($order_id,'_qd_courier_last_response', wp_remote_retrieve_body($resp));
});
