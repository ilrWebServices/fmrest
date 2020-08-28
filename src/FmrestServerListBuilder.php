<?php

namespace Drupal\fmrest;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of FileMaker REST Server entities.
 */
class FmrestServerListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['url'] = $this->t('Base URL');
    $header['key'] = $this->t('Credentials Key');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['url'] = $entity->get('url');
    $row['key'] = $entity->get('key');
    return $row + parent::buildRow($entity);
  }

}
