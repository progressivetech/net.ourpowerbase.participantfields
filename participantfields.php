<?php

require_once 'participantfields.civix.php';
use CRM_Participantfields_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function participantfields_civicrm_config(&$config) {
  _participantfields_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function participantfields_civicrm_xmlMenu(&$files) {
  _participantfields_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function participantfields_civicrm_install() {
  _participantfields_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function participantfields_civicrm_postInstall() {
  _participantfields_civix_civicrm_postInstall();

  // Bugfix. It seems that managed entities do not properly set our
  // custom data group to be based on participants by event so updated it here.
  $params = array(
    'name' => 'participantfields_participant_info',
    'return' => 'id'
  );
  $id = civicrm_api3('CustomGroup', 'getvalue', $params);
  $sql = 'UPDATE civicrm_custom_group SET extends_entity_column_id = 2 WHERE id = %0';
  CRM_Core_DAO::executeQuery($sql, array(0 => array($id, 'Integer')));

  // We add some special dynamic code to the managed hook call. So, we
  // have to trigger a fresh reconciliation at the end of installation
  // to ensure everything is properly created.
 
  // Also, the participant custom fields are, for some reason, heavily cached. 
  // So we have to clear that cache to ensure they are created properly.  
  CRM_Event_BAO_Participant::$_importableFields = NULL;
  $force = TRUE;
  CRM_Core_BAO_UFField::getAvailableFieldsFlat($force);

  CRM_Core_ManagedEntities::singleton(TRUE)->reconcile();

}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function participantfields_civicrm_uninstall() {
  _participantfields_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function participantfields_civicrm_enable() {
  _participantfields_civix_civicrm_enable();
  participantfields_transfer_civicrm_engage_entities();
}

/**
 * Transfer civicrm_engage profiles.
 *
 * Before enabling this module, ensure that all profiles/fields handled by
 * civicrm_engage will now be taken over by this extension.
 **/
function participantfields_transfer_civicrm_engage_entities() {
  $custom_groups = array(
    'Participant_Info' => 'participantfields_participant_info',
  );
  foreach ($custom_groups as $old_name => $new_name) {
    $results = civicrm_api3('CustomGroup', 'get', array('name' => $old_name));
    if ($results['count'] > 0) {
      $id = $results['id'];
      $sql = "UPDATE civicrm_custom_group SET name = %0 WHERE id = %1";
      $params = array(0 => array($new_name, 'String'), 1 => array($id, 'Integer'));
      CRM_Core_DAO::executeQuery($sql, $params);

      $sql = "INSERT INTO civicrm_managed SET module = 'net.ourpowerbase.participantfields',
        name = %0, entity_type = 'CustomGroup', entity_id = %1";
      CRM_Core_DAO::executeQuery($sql, $params);
    }
  }

  $profiles = array(
    'update_event_invite_responses' => 'participantfields_update_event_invite_response'
  );
  foreach ($profiles as $old_name => $new_name) {
    $results = civicrm_api3('UFGroup', 'get', array('name' => $old_name));
    if ($results['count'] > 0) {
      // This means the profile already exists. We are going to rename it so
      // we have consistent naming of this extensions entities. 
      $uf_group_id = $results['id'];
      $params = array_pop($results['values']);
      $params['name'] = $new_name;
      CRM_Core_Error::debug_log_message("update event invite profile already exists, renaming.");
      civicrm_api3('UFGroup', 'create', $params);
    }
  }
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function participantfields_civicrm_disable() {
  _participantfields_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function participantfields_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _participantfields_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function participantfields_civicrm_managed(&$entities) {
  _participantfields_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function participantfields_civicrm_caseTypes(&$caseTypes) {
  _participantfields_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function participantfields_civicrm_angularModules(&$angularModules) {
  _participantfields_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function participantfields_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _participantfields_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function participantfields_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function participantfields_civicrm_navigationMenu(&$menu) {
  _participantfields_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _participantfields_civix_navigationMenu($menu);
} // */
