Module is still not in a packages repository such as packagist.org, or packages.magento.com, you need to specify your own VCS repository so that composer can find it. In the repositories section of the composer.json file of the Magento 2 project add the following:
    "repositories": {
					"mycustommodule": {
      				"type": "vcs",
      				"url": "https://github.com/shekharS7/customer-import-command"
    					}
 		 }

# Install Magento 2 Customer Import module
    composer require wunderman-thompson/customer-import
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy
