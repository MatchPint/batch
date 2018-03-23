<?php
/**
 * @file BatchService.php
 * @brief This file contains the BatchService class.
 * @author Marwan El Boussarghini
 */


namespace Batch;


use Monolog\Logger;

/**
 * Class BatchService
 * @brief Abstract class to model the basic specifications of Batch API.
 */
abstract class BatchAbstract {

  /**
   * Domain URL of the Batch API (custom, transactional and campaigns).
   */
  const API_DOMAIN_URL = "https://api.batch.com";

  /**
   * @var string $apiKey Batch API Key. Identify to which account the Request should be sent.
   */
  protected $apiKey;

  /**
   * @var string $restKey Batch REST Key. Grants the access to the API.
   */
  protected $restKey;

  /**
   * @var string $baseURL Base URL
   */
  protected $baseURL;

  /**
   * @var Logger $log
   */
  protected $log;

  /**
   * @brief BatchService constructor.
   * @param string $apiKey API Key corresponding to the Batch account to send request to.
   * @param string $restKey REST Key that provides the authorisation to access to the Batch API.
   * @param string $apiVersion Version of the Batch API used.
   */
  public function __construct ($apiKey, $restKey, $apiVersion = '1.1')
  {
    if (empty($apiKey))
      throw new \InvalidArgumentException("You must provide a non-empty API Key");

    $this->apiKey = $apiKey;

    if (empty($restKey))
      throw new \InvalidArgumentException("You must provide a non-empty Rest Key");

    $this->restKey = $restKey;
    $this->baseURL = self::API_DOMAIN_URL . "/{$apiVersion}/{$this->apiKey}";
  }

}
