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

class TransactionalAPI extends BatchAbstract {

  /**
   * Path to send a push notification using transactional api.
   */
  const TRANSACTIONAL_PATH = "transactional/send";

  private static $DEFAULT_OPTIONAL_VALUES = [
    'priority'         => 'normal',
    'time_to_live'     => 172800,
    'gcm_collapse_key' => ['enabled' => false, 'key' => 'default'],
  ];

  protected $debug = true;

  public function __construct($apiKey, $restKey, $apiVersion = '1.1')
  {
    parent::__construct($apiKey, $restKey, $apiVersion);
    $this->baseURL = "{$this->baseURL}/" . self::TRANSACTIONAL_PATH;
  }


  /**
   * @brief Send information to batch to create a push notification.
   * @link https://batch.com/doc/api/transactional.html
   * @param string $pushIdentifier Identifier of the push notification.
   * @param array $recipients Recipients of the notification.
   * @param string[] $message Message of the notification.
   * @param array $optionalFields Optional fields, overwriting default values.
   * @return array;
   */
  protected function sendVerified($pushIdentifier, $recipients, $message, $optionalFields){
    $curl = curl_init();
    $opts = [];
    $opts[CURLOPT_RETURNTRANSFER] = TRUE;
    $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

    // Method and URL.
    $opts[CURLOPT_POST] = TRUE;
    $opts[CURLOPT_URL] = $this->baseURL;

    // Build the content of the POST
    $post = [
      'group_id'   => $pushIdentifier,
      'recipients' => $recipients,
      'message'    => $message,
    ];
    foreach ($optionalFields as $name => $value) {
      $post[$name] = $value;
    }
    // Body of the request.
    $opts[CURLOPT_POSTFIELDS] = json_encode($post);

    // Authorization headers.
    $headers = [
      'Content-Type: application/json',
      "X-Authorization: {$this->restKey}"
    ];
    curl_setopt_array($curl, $opts);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    if ($result = curl_exec($curl)) {
      $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      if ($httpStatus >= 400) {
        throw BatchException::createFromResponseBody(json_decode($result, TRUE));
      }
    } else {
      $error = curl_error($curl);
      throw new \RuntimeException("Error in Batch cURL call: $error");
    }

    return [
      'body' => $result,
      'code' => $httpStatus
    ];
  }


  /**
   * @brief Verify the required params and send the notification.
   * @param string $pushIdentifier Identifier of the push notification.
   * @param array $recipients Recipients of the notification.
   * @param string[] $message Message of the notification.
   * @param array $optionalFields Optional fields, overwriting default values.
   * @return array
   */
  public function sendPush($pushIdentifier, $recipients, $message, $optionalFields =[]) {

    $optionalFields = array_merge(self::$DEFAULT_OPTIONAL_VALUES, $optionalFields);

    // Check pushIdentifier.
    if (!is_string($pushIdentifier) || empty($pushIdentifier)) {
      throw new BatchException('Incorrect push identifier field', 32);
    }
    // Check recipients.
    if (!is_array($recipients) || empty($recipients) ||
      !(array_key_exists('custom_ids', $recipients) || array_key_exists('tokens', $recipients) || array_key_exists('install_ids', $recipients))) {
      throw new BatchException('Incorrect recipients field', 32);
    }
    // Check message.
    if (!is_array($message) || empty($message) || !(array_key_exists('title', $message) && array_key_exists('body', $message))) {
      throw new BatchException('Incorrect message field', 32);
    }

    // Casting recipients to string.
    array_walk_recursive($recipients, function (&$value) { $value = (string)$value; });

    return $this->sendVerified($pushIdentifier, $recipients, $message, $optionalFields);
  }


  /**
   * @return bool
   */
  public function isDebug()
  {
    return $this->debug;
  }

  /**
   * @param bool $debug
   */
  public function setDebug($debug)
  {
    $this->debug = $debug;
  }

}