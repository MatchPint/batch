<?php
/**
 * @file BatchCustomAPI.php
 * @brief This file contains the BatchCustomAPI class.
 * @author Marwan El Boussarghini
 */


namespace Batch\Client;


use Batch\Exception\BatchException;

/**
 * Class BatchCustomData
 * @brief Provides functions to communicate with Batch Custom Data API endpoints.
 */
class BatchCustomData extends BatchAbstract {

  /**
   * Path to use to access the Custom Data API.
   */
  const CUSTOM_DATA_PATH = "data/users/";


  /**
   * @brief Batch constructor.
   * @param string $apiKey API Key corresponding to the Batch account to send request to.
   * @param string $restKey REST Key that provides the authorisation to access to the Batch API.
   * @param string $apiVersion Version of the Batch API used.
   */
  public function __construct($apiKey, $restKey, $apiVersion = '1.1') {
    parent::__construct($apiKey, $restKey, $apiVersion);
    $this->baseURL = "{$this->baseURL}/{$this::CUSTOM_DATA_PATH}";
  }

  /**
   * @brief Sends a call to Batch Custom Data API.
   * @link  https://batch.com/doc/api/custom-data-api/set-update.html#_request-structure
   * @param integer $customId Batch's custom id.
   * @param array $customData Custom to send to Batch.
   * @param bool  $overwrite Tells if Batch should override the existing data or override it.
   * @throws BatchException
   */
  public function send($customId, $customData, $overwrite = FALSE) {
    $curl = curl_init();
    $opts = [];
    $opts[CURLOPT_RETURNTRANSFER] = TRUE;
    $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

    // Method and URL.
    $opts[CURLOPT_POST] = TRUE;
    $opts[CURLOPT_URL] = "{$this->baseURL}/$customId";

    // Body of the request.
    $opts[CURLOPT_POSTFIELDS] = json_encode([
      'overwrite' => $overwrite,
      'values' => $customData
    ]);

    // Authorization headers.
    $headers = [
      'Content-Type: application/json',
      "X-Authorization: {$this->restKey}"
    ];
    curl_setopt_array($curl, $opts);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    if ($result = curl_exec($curl)) {
      $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      if ($httpStatus >= 400)
        throw BatchException::createFromResponseBody($result);

    } else {
      $error = curl_error($curl);
      throw new \RuntimeException("Error in Batch cURL call: $error");
    }
  }
}
