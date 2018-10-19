<?php

namespace Drupal\private_entity\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'private_entity.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('private_entity.settings');
    $form['messages'] = [
      '#type' => 'fieldset',
      '#title' => t('Confirmation message'),
      '#description' => t('Confirmation of the entity access status after saving an entity.'),
      '#collapsible' => TRUE,
    ];
    $form['messages']['confirm_public'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('When the entity is <strong>public</strong>'),
      '#default_value' => $config->get('confirm_public'),
    ];
    $form['messages']['confirm_private'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('When the entity is <strong>private</strong>'),
      '#default_value' => $config->get('confirm_private'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('private_entity.settings')
      ->set('confirm_public', $form_state->getValue('confirm_public'))
      ->set('confirm_private', $form_state->getValue('confirm_private'))
      ->save();
  }

}
