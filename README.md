Payment Gateway Placeholders
----------------------------
This folder contains placeholders and integration instructions for:
- bKash (checkout & disbursement)
- Nagad
- Rocket/MFS
- SSLCommerz
- AamarPay (card gateway)

Steps to integrate:
1. Obtain merchant sandbox credentials from each provider.
2. Install the official WordPress/WooCommerce plugin (or use provided plugin if available).
3. Configure webhook URLs to point to https://YOUR_DOMAIN/wc-api/{gateway}
4. Test transactions in sandbox mode, then swap to production keys.
