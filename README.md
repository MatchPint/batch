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

### Regular usage


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

 ### When you have two projects (iOS and Android)

 In case you have two Batch applications (iOS and Android), you probably want to make sure to send a call to each applications.

 You will find for each client an version of this class implementing calls for two applications (iOS / Android).

 __Convention__: The client `{Client}` will have a `IosAndroid{Client}` equivalent that is implementing this.


## Functionality

### Custom Data API

Class: [```Batch\CustomData```](https://github.com/MatchPint/batch/blob/master/src/Batch/CustomData.php)

 - Update data: `send(customUserId: string, values: array, override: boolean)`

    + `customUserId`: Batch Custom Id described [here](https://batch.com/doc/ios/custom-data/customid.html) for iOS and [here](https://batch.com/doc/android/custom-data/customid.html) for Android.
    + `values`: Array containing the values that should be sent to the API as described [here](https://batch.com/doc/api/custom-data-api/set-update.html#_post-data).
    + `override`: Instead of merging the data we already have for a user, the existing data will be *deleted* and replaced by the incoming data (default to FALSE).

 - Update Bulk data: `sendBulk(body: array)`

    + `body`: Body of the request describe [here](https://batch.com/doc/api/custom-data-api/set-update.html#_bulk-post-data)

 - Delete member: __TODO__

Class [```Batch\IosAndroidCustomData```](https://github.com/MatchPint/batch/blob/master/src/Batch/IosAndroidCustomData.php)

 - Update data:

    + only on iOS: `sendIOS(customUserId: string, values: array, override: boolean)`;
    + only on Android: `sendAndroid(customUserId: string, values: array, override: boolean)`;
    + on both projects: `send(customUserId: string, values: array, override: boolean)`.

 - Update Bulk data:

     + only on iOS: `sendBulkIOS(body: array)`;
     + only on Android: `sendBulkAndroid(body: array)`;
     + on both projects: `sendBulk(body: array)`.

 - Delete member: __TODO__

### Transactional API

Class: [```Batch\TransactionalAPI```](https://github.com/MatchPint/batch/blob/master/src/Batch/TransactionalAPI.php)

 - Send a push notification: `sendPush(requiredFields, optionalFields)`

    + `requiredFields`: (ARRAY) Required fields to send a push notification through the transactional api. Must contain :
    
        + `pushIdentifier`: (STRING) Name given to a given kind of push notification. ex: _referral_
        + `recipients`: (ARRAY[STRING[]]) Set of recipients. ex: _["custom_ids" => [162446]]_
        + `message`: (STRING[]) Message to send to the user, must contain a title and a boy. ex: _["title" => "XXX", "body" => "XXXX"]_
        
    + `optionalFields`: (ARRAY) Any kind of optional field that can precise push notification parameters. For more detailed information see [here](https://batch.com/doc/api/transactional.html#_request-structure)

Class [```Batch\IosAndroidCustomData```](https://github.com/MatchPint/batch/blob/master/src/Batch/IosAndroidCustomData.php)

 - Send a push notification:

    + only on iOS: `sendPushNotificationIOS(requiredFields, optionalFields)`;
    + only on Android: `sendPushNotificationAndroid(requiredFields, optionalFields)`;
    + on both projects: `sendPushNotification(requiredFields, optionalFields)`.

### Campaigns API

__TODO:__
