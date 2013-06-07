# Oggetto Facebook extension for Magento

To install set up your root composer.json in your project like this

    {
        "require": {
            "oggetto/facebook-extension": "1.*"
        }
        "repositories": [
            {
                "type": "composer",
                "url": "http://packages.firegento.com"
            }
        ],
        "extra":{
            "magento-root-dir": "./",
            "magento-deploystrategy": "copy"
        }
    }
