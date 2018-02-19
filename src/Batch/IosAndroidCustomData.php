<?php
/**
 * @file   Batch.php
 * @brief  This file contains the Batch class.
 * @author Marwan El Boussarghini
 */

namespace Batch;

use Batch\Exception\BatchException;


/**
 * IosAndroidCustomData class.
 * @brief Implements the CustomData API for two apps (iOS and Android).
 */
class IosAndroidCustomData {

  /**
   * Version of the Batch API used.
   */
  const BATCH_API_VERSION = "1.0";

  /**
   * @var CustomData $iosCustomData Batch Custom Data client for iOS.
   */
  private $iosCustomData;

  /**
   * @var CustomData $androidCustomData Batch Custom Data client for Android.
   */
  private $androidCustomData;


  /**
   * @brief IosAndroidCustomData constructor.
   * @param string $iosApiKey API key of the iOS project.
   * @param string $androidApiKey Android key of
   * @param string $restKey key
   */
  public function __construct ($iosApiKey, $androidApiKey, $restKey) {
    // Get iOS Batch API Key and initialise the iOS BatchCustomData client.
    $this->iosCustomData = new CustomData(
      $iosApiKey,
      $restKey,
      self::BATCH_API_VERSION);

    // Get iOS Batch API Key and initialise the iOS BatchCustomData client.
    $this->androidCustomData = new CustomData(
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
    if (is_null($iosException) || is_null($androidException))
      return;

    if ($iosException->getCode() === $androidException->getCode())
      throw $iosException;

    $errorMessage = <<<MESSAGE
2 Exceptions occured.
[iOS-{$iosException->getCode()}]{$iosException->getMessage()}[/iOS]
[Android-{$androidException->getCode()}]{$androidException->getMessage()}[/Android]
MESSAGE;

    throw new BatchException($errorMessage);
  }


  /**
   * @brief Sends a Custom Data request to the iOS client.
   * @param integer $customId Batch's custom id.
   * @param array $values Custom data to send to Batch.
   * @param boolean $overwrite Tells if Batch should override the existing data or override it.
   */
  public function sendIOS($customId, array $values, $overwrite = FALSE) {
    $this->iosCustomData->send($customId, $values, $overwrite);
  }


  /**
   * @brief Sends a Custom Data request to the Android client.
   * @param integer $customId Batch's custom id.
   * @param array $values Custom data to send to Batch.
   * @param boolean $overwrite Tells if Batch should override the existing data or override it.
   */
  public function sendAndroid($customId, array $values, $overwrite = FALSE) {
    $this->iosCustomData->send($customId, $values, $overwrite);
  }


  /**
   * @brief Sends a Custom Data request to both the iOS and Android clients.
   * @param integer $customId Batch's custom id.
   * @param array $values Custom data to send to Batch.
   * @param boolean $overwrite Tells if Batch should override the existing data or override it.
   */
  public function send($customId, array $values, $overwrite = FALSE) {
    $iosException = NULL;
    try {
      $this->iosCustomData->send($customId, $values, $overwrite);
    }
    catch (BatchException $exception) {
      $iosException = $exception;
    }

    $androidException = NULL;
    try {
      $this->androidCustomData->send($customId, $values, $overwrite);
    }
    catch (BatchException $exception) {
      $androidException = $exception;
    }

    $this->handleClientsExceptions($iosException, $androidException);
  }

}