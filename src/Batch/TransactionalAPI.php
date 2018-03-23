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

namespace Batch\Exception;


use Batch\BatchAbstract;

class TransactionalAPI extends BatchAbstract {

  /**
   * Path to send a push notification using transactional api.
   */
  const TRANSACTIONAL_PATH = "transactional/send";

  private $defaultOptionalValues = [
    'priority' => 'normal',
    'time_to_live' => 172800,
    'gcm_collapse_key' => ['enabled' => false, 'key' => 'default'],
    'media' => [],
    'deeplink' => '',
    'custom_payload' => '',
    'landing' => []
  ];

  protected $debug = true;

  public function __construct($apiKey, $restKey, $apiVersion = '1.1')
  {
    parent::__construct($apiKey, $restKey, $apiVersion);
    $this->baseURL = "{$this->baseURL}/" . self::TRANSACTIONAL_PATH;
  }

  /**
   * @brief https://batch.com/doc/api/transactional.html
   * @param $campaignName
   * @param $recipients
   * @param $message
   * @param array $media
   * @param string $deeplink
   * @param string $custom_payload
   * @param array $landing
   * @param string $priority
   */
  protected function send($requiredFields, $optionalFields){
    $curl = curl_init();
    $opts = [];
    $opts[CURLOPT_RETURNTRANSFER] = TRUE;
    $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

    // Method and URL.
    $opts[CURLOPT_POST] = TRUE;
    $opts[CURLOPT_URL] = $this->baseURL;

    // Body of the request.
    $opts[CURLOPT_POSTFIELDS] = json_encode([
      'group_id' => $requiredFields['pushIdentifier'],
      'priority' => $optionalFields['priority'],
      'time_to_live' => $optionalFields['time_to_live'],
      'gcm_collapse_key' => $optionalFields['gcm_collapse_key'],
      'recipients' => $requiredFields['recipients'],
      'message' => $requiredFields['message'],
      'media' => $optionalFields['media'],
      'deeplink' => $optionalFields['deeplink'],
      'custom_payload' => $optionalFields['custom_payload'],
      'landing' => $optionalFields['landing']
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

      if ($httpStatus == 201 && $this->debug)
        $this->log->addInfo('POST successful for ' . $requiredFields['pushIdentifier'] . 'with' . $requiredFields['recipients'] ,$result);

      if ($httpStatus >= 400)
        throw BatchException::createFromResponseBody(json_decode($result, TRUE));

    } else {
      $error = curl_error($curl);
      throw new \RuntimeException("Error in Batch cURL call: $error");
    }
  }


  /**
   * @brief Send push notification with only required params.
   * @param $pushIdentifier
   * @param $recipients
   * @param $title
   * @param $messageBody
   */
  public function sendPush($requiredFields, $optionalFields =[]) {

    $optionalFields = array_merge($this->defaultOptionalValues, $optionalFields);
    if (!array_key_exists('pushIdentifier', $requiredFields) ||
      !array_key_exists('message', $requiredFields) || !array_key_exists('recipients', $requiredFields)) {
      throw new BatchException('Missing required fields in body', 32);
    }
    $this->send($requiredFields, $optionalFields);
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