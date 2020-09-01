<?php

namespace Drupal\fmrest\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining FileMaker REST Server entities.
 */
interface FmrestServerInterface extends ConfigEntityInterface {

  /**
   * Get the key entity for this FileMaker Rest server.
   *
   * @return \Drupal\key\Entity\Key
   *   The key entity of the user_password type with login credentials.
   */
  public function getKey();

}
