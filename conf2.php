RewriteEngine on

RewriteBase /

RewriteRule ^site/(.+)/(.+)$ /index.php?site=$1
RewriteRule ^view/(.+)/(.+)/(.+)$ /index.php?site=$1&add=$2
RewriteRule ^costing/(.+)/(.+)/$ /index.php?site=600&url=$1&did=$2
RewriteRule ^project/(.+)/(.+)/$ /index.php?site=$1&plist=$2
RewriteRule ^galery/(.+)/(.+)/$ /index.php?site=$1&pid=$2
RewriteRule ^order/(.+)/$ /index.php?site=13&oid=$1
RewriteRule ^detail/(.+)/$ /index.php?site=14&did=$1
RewriteRule ^program/(.+)/$ /index.php?site=16&pid=$1