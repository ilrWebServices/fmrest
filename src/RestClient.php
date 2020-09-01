<?php

namespace Drupal\fmrest;

use GuzzleHttp\ClientInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Properties and methods to communicate with the FileMaker Data API.
 */
class RestClient implements RestClientInterface {

  /**
   * GuzzleHttp client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The cache service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The JSON serializer service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $json;

  /**
   * The entity_type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorage
   */
  protected $fmrestServerStorage;

  /**
   * The active FileMaker REST server.
   *
   * @var \Drupal\fmrest\Entity\FmrestServer
   */
  protected $server;

  /**
   * Constructor for a RestClient.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The GuzzleHttp Client.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache service.
   * @param \Drupal\Component\Serialization\Json $json
   *   The JSON serializer service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ClientInterface $http_client, StateInterface $state, CacheBackendInterface $cache, Json $json, EntityTypeManagerInterface $entity_type_manager) {
    $this->httpClient = $http_client;
    $this->state = $state;
    $this->cache = $cache;
    $this->json = $json;
    $this->entityTypeManager = $entity_type_manager;
    $this->fmrestServerStorage = $entity_type_manager->getStorage('fmrest_server');
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setServer($name) {
    $this->server = $this->fmrestServerStorage->load($name);
  }

  /**
   * {@inheritdoc}
   */
  public function apiCall($method, $url, array $params = []) {
    if (!$this->verifyAuthentication()) {
      $this->authenticate();
    }

    $auth_token = $this->state->get('fmrest.auth_token.' . $this->server->id());
    $url = $this->formatUrl($url);
    return $this->apiHttpRequest($method, $url, 'Bearer ' . $auth_token, $params);
  }

  /**
   * {@inheritdoc}
   */
  protected function apiHttpRequest($method, $url, $authorization, array $params) {
    $headers = [
      'Authorization' => $authorization,
      'Content-type' => 'application/json',
    ];

    $data = NULL;
    if (!empty($params)) {
      $data = $this->json->encode($params);
    }

    $args = ['headers' => $headers, 'body' => $data];
    return $this->httpClient->$method($url, $args);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatUrl($url) {
    if (!$this->server) {
      return $this->server->get('url') . $url;
    }

    return $this->server->get('url') . strtr($url, [
      '{{version}}' => $this->server->get('version'),
      '{{database}}' => $this->server->get('db'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function verifyAuthentication() {
    if (!$this->server) {
      throw new \Exception('Server is not set');
    }

    $existing_auth_token = $this->state->get('fmrest.auth_token.' . $this->server->id());

    if (!$existing_auth_token) {
      return FALSE;
    }

    $url = $this->formatUrl('/fmi/data/{{version}}/validateSession');

    try {
      $response = $this->apiHttpRequest('GET', $url, 'Bearer ' . $existing_auth_token, []);
      return TRUE;
    }
    catch (RequestException $e) {
      return FALSE;
      // TODO Deal with this? Or just let it fail silently?
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function authenticate() {
    if (!$this->server) {
      throw new \Exception('Server is not set');
    }

    $key = $this->server->getKey();
    $credentials = $key->getKeyValues();
    $url = $this->formatUrl('/fmi/data/{{version}}/databases/{{database}}/sessions');
    $auth_string = 'Basic ' . base64_encode($credentials['username'] . ':' . $credentials['password']);

    try {
      $response = $this->apiHttpRequest('POST', $url, $auth_string, []);
    }
    catch (RequestException $e) {
      // throw new RestException($this->response, $e->getMessage(), $e->getCode(), $e);
      dump($e);
    }

    if ($response->getStatusCode() !== 200) {
      // Throw error? Log?
      return FALSE;
    }

    $response_body = $this->json->decode($response->getBody());
    $auth_token = $response_body['response']['token'];

    // Save the auth_token for this server into the system state for later use.
    $this->state->set('fmrest.auth_token.' . $this->server->id(), $auth_token);

    return $auth_token;
  }

}
