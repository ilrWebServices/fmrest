<?php

namespace Drupal\fmrest\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FmrestServerForm.
 */
class FmrestServerForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#description' => $this->t('The administrative label for this FileMaker Server (e.g. FileMaker Server).'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\fmrest\Entity\FmrestServer::load',
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base URL'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->get('url'),
      '#description' => $this->t('The HTTP scheme and hostname of this FileMaker Server (e.g. https://filemaker.example.net). Include the leading <em>http/https</em> but do not use a trailing slash.'),
      '#required' => TRUE,
    ];

    $form['db'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->get('db'),
      '#description' => $this->t('The name of the database for this connection.'),
      '#required' => TRUE,
    ];

    $form['version'] = [
      '#type' => 'select',
      '#title' => $this->t('API version'),
      '#options' => [
        'v1' => 'v1',
        'v2' => 'v2',
        'vLatest' => 'vLatest',
      ],
      '#default_value' => $this->entity->get('version'),
      '#description' => $this->t('The FileMaker Data API version.'),
      '#required' => TRUE,
    ];

    $form['key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Credentials key'),
      '#key_filters' => [
        'type' => 'user_password',
      ],
      '#default_value' => $this->entity->get('key'),
      '#description' => $this->t('Ensure that the key is a <em>User/password</em> type.'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label FileMaker REST Server.', [
          '%label' => $this->entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label FileMaker REST Server.', [
          '%label' => $this->entity->label(),
        ]));
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }

}
