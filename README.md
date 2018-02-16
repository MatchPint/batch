# Batch

This library includes several classes and methods to implement the different Batch API calls. You will find the
documentation of the different API [here](https://batch.com/doc/api/prerequisites.html).

## Installation

### Composer installation

To install Google Analytics, you first need to install [Composer](http://getcomposer.org/), a Package Manager 
for PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


### Batch Installation


You can install this package using `composer` by running the command below.

```bash
php /usr/local/bin/composer/composer.phar require matchpint/batch
```

You can also add `matchpint\batch` to your the require of your `composer.json` as below

```json
{
    "name": "domain/your_project",
    "require": {
        "matchpint/batch": "~1"
    }
}
```

and run

```bash
php /usr/local/bin/composer/composer.phar update
```


## Prerequisites

This library implements the APIs provide by [batch.com](https://batch.com/doc/api/prerequisites.html).
You must get your API Key and Rest Key before using this project.

## Usage


 1. Initialisation

```php
use Batch\BatchCustomData;

$batchCustomData = new BatchCustomData($yourApiKey, $yourRestKey);
```


 2. Write as an array the body that you want to send to Batch (if needed)

```php
$body = [
  "u.field_name" => "newValue",
  "ut.tags" => [
    "$add" => ["newTag"]
  ]
];
```

 3. Send the request to Batch using the function corresponding to the endpoint you need.

 ```php
 $batchCustomData->send($customUserId, $body, FALSE);
 ```


## Functionality

### Custom Data API

Class: [```Batch\BatchCustomData```](https://github.com/MatchPint/batch/blob/feature/documentation/src/Batch/BatchCustomData.php)

 - Update data: `send(customUserId: string, values: array, override: boolean)`

    + `customUserId`: Batch Custom Id described [here](https://batch.com/doc/ios/custom-data/customid.html) for iOS and [here](https://batch.com/doc/android/custom-data/customid.html) for Android.
    + `values`: Array containing the values that should be sent to the API as described [here](https://batch.com/doc/api/custom-data-api/set-update.html#_post-data).
    + `override`: Instead of merging the data we already have for a user, the existing data will be *deleted* and replaced by the incoming data (default to FALSE).

 - Update Bulk data: __TODO__
 - Delete member: __TODO__

### Transactional API

__TODO__

### Campaigns API

__TODO:__
