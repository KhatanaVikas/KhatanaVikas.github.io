randomTeamSelector
==================

A Symfony project created on June 2, 2019, 12:51 pm.
To Run this symfony project on your local environment
1.  install apache , php (also php-xml)
2.  add these to /etc/apache2/sites-available/randomTeamGenerator.conf

<VirtualHost *:80>
    DocumentRoot /var/www/randomTeamSelector/web
    ServerName local.randomteamselector.com
    ServerAlias www.randomteamselector.com
    

    ErrorLog ${APACHE_LOG_DIR}/random-team-selector.log
    CustomLog ${APACHE_LOG_DIR}/random-team-selector.log combined

    <Directory /var/www/randomTeamSelector/web>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

3. Add to etc/hosts
    127.0.1.1       local.randomteamselector.com

cd /etc/apache2/sites-available
4. sudo service a2ensite randomTeamGenerator.conf
5. sudo service apache2 restart
6. visit local.randomteamselector.com/app_dev.php

Refresh everytime to generate new combination of teams.
 

