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

Note : Keep CSV and JSON file inside pub/media folders for customer importing
So to import from the CSV and the JSON respectively the user would execute
either one of the following 

```
bin/magento customer:import sample-csv sample.csv
bin/magento customer:import sample-json sample.json
```
