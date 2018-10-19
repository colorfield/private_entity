<?php

namespace Drupal\private_entity\Access;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_tools\FieldTools;

/**
 * Access to a Term page based on the definition of a private entity field.
 */
class TermPageAccessCheck implements AccessInterface {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, RouteMatchInterface $route_match) {
    $access = NULL;
    $term = $route_match->getParameter('taxonomy_term');
    if(!empty($term)) {
      // @todo Code design: is private should be set to true by default
      // now that private_entity_field_config_insert takes care of setting
      // initial values on existing content.
      $isPrivate = FALSE;
      // @todo get from field definition
      $privateFieldName = FieldTools::getFieldNameFromType($term, 'private_entity');
      if (!is_null($privateFieldName) && $term->hasField($privateFieldName)) {
        $privateFieldValue = $term->get($privateFieldName)->getValue();
        if (isset($privateFieldValue[0]['value'])
          && (int) $privateFieldValue[0]['value'] === 1) {
          $isPrivate = TRUE;
        }
      }
      if($isPrivate === FALSE) {
        $access = AccessResult::allowed();
      }else {
        // @todo define dynamic per entity type access permission
        $access = AccessResult::allowedIfHasPermission($account, 'private entity view access');
      }
    }else {
      // @todo review access of other entities
      $access = AccessResult::allowed();
    }
    return $access;
  }

}
