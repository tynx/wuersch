#Installation
Get the backend-code and place it somewhere like /opt/wuersch
```
git clone https://github.com/tynx/wuersch
```
Create config (adapt as needed) found in backend/config/
```
cp Config.class.php.example Config.class.php
```
Make sure the http-server has permission (www-data in my case)
```
chown -R www-data:www-data wuersch/backend
```
Make it visible within doc-root
```
ln -s wuersch/backend/ /var/www/wuersch_backend
```
For apache there is an .htacces for the rewrite (all request should _as they are_ forwarded to index.php. in Apache enable mod_rewrite and make sure AllowOverride is set appropriately.

Then we want a log file, accessible by the http-server:
```
touch /var/log/wuersch.log
chown www-data:www-data /var/log/wuersch.log
```
Prepeare Databse (with according user, adapt in your conf!)
```
CREATE USER 'wuersch'@'localhost' IDENTIFIED BY 'wuersch';
GRANT ALL PRIVILEGES ON `wuersch`.* TO 'wuersch'@'localhost';
mysql -u root -p < data/db.sql
```

DONE!
