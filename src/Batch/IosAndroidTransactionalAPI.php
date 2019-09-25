<?php
/**
 *    oOOOOOo
 *   ,|    oO
 *  //|     |
 *  \\|     |
 *   `|     |
 *    `-----`
 * MatchPint Ltd
 * @author Pierre Segonne
 * Date: 23/03/2018
 */

namespace Batch;

use Batch\Exception\BatchException;

/**
 * IosAndroidTransactionalAPI class.
 * @brief Implements the Transactional API for two apps (iOS and Android).
 */
class IosAndroidTransactionalAPI {

  /**
   * Version of the Batch API used.
   */
  const BATCH_API_VERSION = "1.1";

  /**
   * @var TransactionalAPI $transactionalApi Batch transactional api client for iOS.
   */
  private $iosTransactionalApi;

  /**
   * @var TransactionalAPI $transactionalApi Batch transactional api client for Android.
   */
  private $androidTransactionalApi;


  /**
   * @brief IosAndroidCustomData constructor.
   * @param string $iosApiKey API key of the iOS project.
   * @param string $androidApiKey API key of the Android project
   * @param string $restKey Access key to Batch.
   */
  public function __construct($iosApiKey, $androidApiKey, $restKey) {
    if (empty($iosApiKey)) {
      throw new \InvalidArgumentException('No batch.iosApiKey defined in config');
    }

    if (empty($androidApiKey)) {
      throw new \InvalidArgumentException('No batch.androidApiKey defined in config');
    }

    // Create the instance of the iOS transactional api client.
    $this->iosTransactionalApi = new TransactionalAPI(
      $iosApiKey,
      $restKey,
      self::BATCH_API_VERSION);

    // Create the instance of the android transactional api client.
    $this->androidTransactionalApi = new TransactionalAPI(
      $androidApiKey,
      $restKey,
      self::BATCH_API_VERSION);

  }


  /**
   * @brief Handles the exceptions coming from the iOS and Android calls.
   * @param BatchException|null $iosException Exception thrown by the iOS App request.
   * @param BatchException|null $androidException Exception thrown by the Android App request.
   */
  private function handleClientsExceptions($iosException, $androidException) {
    // If one of the apps returns no error, the call is a success.
    if (is_null($iosException) || is_null($androidException)) {
      return;
    }

    if ($iosException->getCode() === $androidException->getCode()) {
      throw $iosException;
    }

    $errorMessage = <<<MESSAGE
2 Exceptions occurred.
[iOS-{$iosException->getCode()}]{$iosException->getMessage()}[/iOS]
[Android-{$androidException->getCode()}]{$androidException->getMessage()}[/Android]
MESSAGE;

    throw new BatchException($errorMessage);
  }

  /**
   * @brief Send a push notification trough the transactional api client for iOS.
   * @param string $pushIdentifier Identifier of the push notification.
   * @param array $recipients Recipients of the notification.
   * @param string[] $message Message of the notification.
   * @param array $optionalFields Optional fields, overwriting default values.
   * @return array
   */
  public function sendPushNotificationIOS($pushIdentifier, array $recipients, array $message, array $optionalFields = []) {
    if (!empty($optionalFields['media']) && !empty($optionalFields['media']['icon'])) {
      // No icon supported on iOS
      unset($optionalFields['media']['icon']);

      // If all we had in media was an icon, remove media
      if (empty($optionalFields['media'])) {
        unset($optionalFields['media']);
      }
    }
    return $this->iosTransactionalApi->sendPush($pushIdentifier, $recipients, $message, $optionalFields);
  }


  /**
   * @brief Send a push notification trough the transactional api client for Android.
   * @param string $pushIdentifier Identifier of the push notification.
   * @param array $recipients Recipients of the notification.
   * @param string[] $message Message of the notification.
   * @param array $optionalFields Optional fields, overwriting default values.
   * @return array
   */
  public function sendPushNotificationAndroid($pushIdentifier, array $recipients, array $message, array $optionalFields = []) {
    return $this->androidTransactionalApi->sendPush($pushIdentifier, $recipients, $message, $optionalFields);
  }

  /**
   * @brief Send a push notification trough the transactional api client.
   * @param string $pushIdentifier Identifier of the push notification.
   * @param array $recipients Recipients of the notification.
   * @param string[] $message Message of the notification.
   * @param array $optionalFields Optional fields, overwriting default values.
   * @return array;
   */
  public function sendPushNotification($pushIdentifier, array $recipients, array $message, array $optionalFields = []) {
    $iosException = null;
    try {
      $iosResult = $this->sendPushNotificationIOS($pushIdentifier, $recipients, $message, $optionalFields);
    } catch (BatchException $exception) {
      $iosException = $exception;
    }

    $androidException = null;
    try {
      $androidResult = $this->sendPushNotificationAndroid($pushIdentifier, $recipients, $message, $optionalFields);
    } catch (BatchException $exception) {
      $androidException = $exception;
    }

    $this->handleClientsExceptions($iosException, $androidException);

    if (empty($iosResult)) {
      $iosResult = $iosException->getMessage();
    }

    if (empty($androidResult)) {
      $androidResult = $androidException->getMessage();
    }

    return [
      'ios'     => $iosResult,
      'android' => $androidResult,
    ];
  }
}
