#!/usr/bin/env bash
# Nginx ve la IP real del visitante cuando Cloudflare está en modo proxy (nube naranja).
# Uso: sudo bash deploy/hostinger/install-cloudflare-realip.sh
set -euo pipefail

if [[ "$(id -u)" -ne 0 ]]; then
  echo "Ejecuta como root: sudo bash deploy/hostinger/install-cloudflare-realip.sh"
  exit 1
fi

OUT=/etc/nginx/conf.d/cloudflare-realip.conf
TMP="$(mktemp)"

{
  echo "# Generado $(date -u +%Y-%m-%dT%H:%MZ). Fuente: https://www.cloudflare.com/ips/"
  curl -fsSL https://www.cloudflare.com/ips-v4 | sed 's/^/set_real_ip_from /; s/$/;/'
  curl -fsSL https://www.cloudflare.com/ips-v6 | sed 's/^/set_real_ip_from /; s/$/;/'
  echo "real_ip_header CF-Connecting-IP;"
} > "$TMP"

install -m 644 "$TMP" "$OUT"
rm -f "$TMP"

nginx -t
systemctl reload nginx
echo "Listo: $OUT"
