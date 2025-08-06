#!/bin/sh
set -e

FLAG_FILE="/var/www/html/.installed"

# give www-data ownership of the xml folder every start
chown -R www-data:www-data /var/www/html/xml

wait_for_db() {
  php -r '
  while (true) {
      try {
          new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME"));
          echo "✅  MariaDB reachable\n";
          exit(0);
      } catch (mysqli_sql_exception $e) {
          echo "⏳  Waiting for MariaDB: {$e->getMessage()}\n";
          sleep(2);
      }
  }
  ' || exit 1
}

if [ true ]; then
  wait_for_db
  echo "🚀  Running initial database setup …"
  php /var/www/html/install.php "${INSTALL_ACTION:-complete}" # can be set to complete or basic
  touch "$FLAG_FILE"
  echo "🏁  Database setup finished."
fi

exec apache2-foreground

# Clean up install files
rm -f /var/www/html/install.{php,html}