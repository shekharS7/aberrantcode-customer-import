# Install Magento 2 Customer Import module only
    composer require wunderman-thompson/customer-import
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy
