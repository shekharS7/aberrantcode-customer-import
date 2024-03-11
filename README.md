Module is still not in a packages repository such as packagist.org, or packages.magento.com, you need to specify your own VCS repository so that composer can find it. In the repositories section of the composer.json file of the Magento 2 project add the following:
```
"repositories": {
  "mycustommodule": {
    "type": "vcs",
    "url": "https://github.com/shekharS7/customer-import-command"
  }
}
``` 
package is in a development stage, you will need to add the minimum-stability as well to the composer.json file:
```
"minimum-stability": "dev",
```

# Install Magento 2 Customer Import module
    composer require wunderman-thompson/customer-import
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy

IMPORTANT : Keep CSV and JSON file inside pub/media folders for customer importing
So to import from the CSV and the JSON respectively the user would execute
either one of the following 

```
bin/magento customer:import sample-csv sample.csv
bin/magento customer:import sample-json sample.json
```
NOTE:I encountered some challenges with my laptop  as it is not compatible for Magento2 development , 
so I had to complete all tasks on a cloud server. Unfortunately, this meant that I was unable to perform the 
phpcodesniffer test and some unit tests due to issues such as "Config file allure/allure.config.php doesn't exist."
    Magento Version :  "2.4.6-p3"
	Php Version: 8.1.26
