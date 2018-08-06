# Yii2 extension to access Rancher

Enables very easy access to Rancher from your Yii2 application.

Supports Rancher V2 beta (Rancher 1.6 API) and a single Rancher environment.

API-Resurces supported (easily expandable, see ```Rancher.php```):

* Get a list of stacks
* Deactivate a stack


Accesses Rancher via the fantastic [Rancher API](https://rancher.com/docs/rancher/v1.6/en/api/v2-beta/)

## Installation

Add System-Info to the require section of your **composer.json** file:

```php
{
    "require": {
        "weesee/yii2-rancher": "~1.0.0"
    }
}
```

And run following command to download extension using **composer**:

```bash
$ php composer.phar update
```

To configure Rancher API access for your Environment:

* In the Rancher UI: Open API->Keys
* Open "Advanced Options"
* Add an Environment API Key
* Copy Access Key (Username) and Secret Key (Password)
* Copy Endpoint (v2-beta)

## Usage

Get Rancher stacks:
```php
use weesee\Rancher;

// Get Rancher stacks
$rancher = new RancherApi([
    'apiEndpointUrl' => '...',  // Rancher Endpoint (v2-beta)
    'apiUsername' => '...', // Rancher Access Key (Username)
    'apiPassword' => '...', // Rancher Secret Key (Password)
]);
// get system details as Yii2 model
$stacks = $rancher->getStacks();

```

## Contribution

Contributing instructions are located in [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Author & Credits

Author: weesee@web.de

(C) 2018 WeeSee
