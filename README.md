## SIMPLE REST API WITH:
###| SILEX | DOCTRINE 2 | OAUTH  | IFTTT | ELASTICSEARCH | PHP 5.6 |

VHOST CONFIGURATION :
---------------

    <VirtualHost *:80>
            DocumentRoot "/var/www/html/simpleApiSilex/public"
            ServerName api.simple.com
            SetEnv APPLICATION_ENV "development"
            <Directory "/var/www/html/simpleApiSilex/public">
                    Options Indexes Multiviews FollowSymLinks
                    AllowOverride All
                    Order allow,deny
                    Allow from all
                    Require all granted
            </Directory>
    </VirtualHost>


