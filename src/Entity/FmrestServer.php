<?php

namespace Drupal\fmrest\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the FileMaker REST Server entity.
 *
 * @ConfigEntityType(
 *   id = "fmrest_server",
 *   label = @Translation("FileMaker REST server"),
 *   label_collection = @Translation("FileMaker REST servers"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\fmrest\FmrestServerListBuilder",
 *     "form" = {
 *       "add" = "Drupal\fmrest\Form\FmrestServerForm",
 *       "edit" = "Drupal\fmrest\Form\FmrestServerForm",
 *       "delete" = "Drupal\fmrest\Form\FmrestServerDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/fmrest_server/add",
 *     "edit-form" = "/admin/structure/fmrest_server/{fmrest_server}/edit",
 *     "delete-form" = "/admin/structure/fmrest_server/{fmrest_server}/delete",
 *     "collection" = "/admin/structure/fmrest_server"
 *   },
 *   config_prefix = "fmrest_server",
 *   config_export = {
 *     "id",
 *     "label",
 *     "url",
 *     "key",
 *   }
 * )
 */
class FmrestServer extends ConfigEntityBase implements FmrestServerInterface {

  /**
   * The FileMaker REST Server ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The FileMaker REST Server label.
   *
   * @var string
   */
  protected $label;

  /**
   * The FileMaker REST Server base URL.
   *
   * @var string
   */
  protected $url;

  /**
   * The FileMaker REST Server credentials key.
   *
   * @var string
   */
  protected $key;

}
