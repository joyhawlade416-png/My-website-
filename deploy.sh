#!/bin/bash
# Usage: customize domain and run on server
DOMAIN=YOUR_DOMAIN_HERE
docker-compose up -d
echo 'To get SSL cert run (example):'
echo 'docker-compose run --rm certbot certonly --webroot --webroot-path=/var/www/certbot -d '"$DOMAIN"' --email you@domain.com --agree-tos --no-eff-email'
