
To test out the default website layout, perform the folling steps:

echo "127.0.0.1 flexible-orm-demo-website.local" | sudo tee -a /etc/hosts

Ensure this repository is checked out at /sites/flexible-orm-demo-website/

# On Ubuntu:
sudo ln -s /sites/flexible-orm-demo-website/deployment/apache-conf/sites-available/flexible-orm-demo-website  /etc/apache2/sites-available/
sudo a2ensite flexible-orm-demo-website
sudo service apache2 restart

# On gentoo
sudo ln -s /sites/flexible-orm-demo-website/deployment/apache-conf/sites-available/flexible-orm-demo-website  /etc/apache2/vhost.d/flexible-orm-demo-website.conf

