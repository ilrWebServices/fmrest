<?php

namespace Drupal\fmrest;

/**
 * Properties and methods to communicate with the FileMaker Data API.
 */
interface RestClientInterface {

  /**
   * Set the active FileMaker server.
   *
   * @param string $name
   *   The machine id of an FmrestServer config entity.
   *
   * @return NULL
   */
  public function setServer($name);

  /**
   * Make an API call to the active FmrestServer.
   *
   * @param string $method
   *   HTTP method for the call, such as GET or POST.
   * @param string $url
   *   The path to the resource. The server base URL, as configured for the
   *   FmrestServer, will be prepended. A limited set of string substitutions
   *   are allowed:
   *   - {{version}} The API version configured for the FmrestServer.
   *   - {{database}} The database configured for the FmrestServer.
   * @param array $params
   *   Parameters to provide.
   *
   * @return mixed
   *   The response object.
   */
  public function apiCall($method, $url, array $params = []);

}
