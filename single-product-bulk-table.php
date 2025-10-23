<?php
global $product;
$tiers = get_post_meta($product->get_id(), '_qd_bulk_tiers', true);
if(!$tiers) return;
?>
<div class="bulk-price-table">
  <h4>Wholesale Price</h4>
  <table>
    <tr><th>Quantity</th><th>Price (৳)</th></tr>
    <?php foreach($tiers as $t): ?>
      <tr><td><?php echo esc_html($t['qty']); ?>+</td><td><?php echo esc_html($t['price']); ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
