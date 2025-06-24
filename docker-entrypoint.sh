#!/bin/sh
set -e

FLAG_FILE="/var/www/html/.installed"

wait_for_db() {
  php -r '
  $max = 30;
  while ($max--) {
      @$c = new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"));
      if (!$c->connect_errno) { echo "✅  MariaDB reachable\n"; exit(0); }
      sleep(2);
  }
  echo "❌  MariaDB not reachable\n"; exit(1);
  ' || exit 1
}

if [ true ]; then #! -f "$FLAG_FILE" TEMPORARILY REMOVING FLAG FILE 
  wait_for_db
  echo "🚀  Running initial database setup …"
  php /var/www/html/install.php "${INSTALL_ACTION:-complete}" # can be set to complete or basic
  touch "$FLAG_FILE"
  echo "🔄  Generating XML files for all datasets..."
  php /var/www/html/generate_xml_files.php
  echo "🏁  Database setup finished."
fi

exec apache2-foreground

# Clean up install files
rm -f /var/www/html/install.{php,html}