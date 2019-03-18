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

  // Via managed entities, we create a group of custom fields. Some of the fields
  // are radio fields that have options, so we ask managed entities to create
  // those options. 
  //
  // However, managed entities cannot assign each custom field to the
  // appropriate option group so we do that manually here.

  $pairs = array(
    'participantfields_reminder_response' => 'participantfields_invite_response_values',
    'participantfields_invitation_response' => 'participantfields_invite_response_values',
    'participantfields_second_call_response' => 'participantfields_invite_response_values',
  );

  foreach($pairs as $field_name => $option_group_name) {
    participantfields_assign_option_group_to_custom_field($field_name, $option_group_name); 
  }

  participantfields_create_profiles();
}

/**
 * Assign option groups to fields
 *
 * @param string $field_name 
 *   string name of the field
 * @param string $option_group_name
 *   string name of option group
 *
 **/
function participantfields_assign_option_group_to_custom_field($field_name, $option_group_name) {
  $params = array('name' => $option_group_name);
  $option_group = civicrm_api3('option_group', 'getsingle', $params);

  // Get the custom field.
  $params = array('name' => $field_name);

  try {
    $field = civicrm_api3('custom_field', 'getsingle', $params); 
    // Update the custom field.
    $field['option_group_id'] = $option_group['id'];
    civicrm_api3('custom_field', 'create', $field);
  }
  catch(CiviCRM_API3_Exception $e) {
    if ($e->getMessage() == 'Expected one CustomField but found 0') {
      // If we can't locate the custom field, it might mean they have disabled
      // it, deleted it or it never existed in the first place. That's ok.
      return;
    }
  }
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
}

/**
 * Create profiles using our custom fields.
 *
 * Unfortunatley, api3 can't create profiles with custom fields
 * because the api depends on the id of the custom field. We
 * can't predict what that id is, so instead using the api and
 * managed entities, we create our profile here.
 */
function participantfields_create_profiles() {
  $old_name ='update_event_invite_responses';
  $new_name = 'participantfields_update_event_invite_response';
  $results = civicrm_api3('UFGroup', 'get', array('name' => $old_name));
  if ($results['count'] > 0) {
    // This means the profile already exists. We are going to rename it so
    // we have consistent naming of this extensions entities. 
    $uf_group_id = $results['id'];
    $params = array_pop($results['values']);
    $params['name'] = $new_name;
    civicrm_api3('UFGroup', 'create', $params);
  }
  else {
    // This profile does not already exist. Let's create it.
    $params = array(
      'name' => $new_name,
      'title' => 'Update Event Invite Response',
      'description' => 'Powerbase profile for updating responses to invitations',
      'is_active' => 1,
      'is_update_dupe' => '1',
    );
    $result = civicrm_api3('UFGroup', 'create', $params);
    $uf_group_id = $result['id'];
    $template_params = array(
      'uf_group_id' => $uf_group_id, 
      'is_active' => '1',
      'is_view' => '0',
      'is_required' => '0',
      'weight' => '10',
      'visibility' => 'User and User Admin Only',
      'field_type' => 'Participant',
    );
    
    $fields = array(
      'participantfields_child_care_needed' => array(),
      'participantfields_ride_to' => array(),
      'participantfields_ride_from' => array(),
      'participantfields_invitation_date' => array(),
      'participantfields_invitation_response' => array(),
      'participantfields_second_call_date' => array(),
      'participantfields_second_call_response' => array(),
      'participantfields_reminder_date' => array(),
      'participantfields_reminder_response' => array(),
    );
    // Go to ridiculous lengths to rewarm the cache so the function
    // that builds the profiles will recognize the custom fields we just
    // created as existing.
    CRM_Event_BAO_Participant::$_importableFields = NULL;
    $force = TRUE;
    $fields = CRM_Core_BAO_UFField::getAvailableFieldsFlat($force);
    foreach ($fields as $field_name => $props) {
      // Get the custom id of the field we want.
      $result = civicrm_api3('CustomField', 'get', array('name' => $field_name));
      if ($result['count'] > 0) {
        $id = $result['id'];
        $params = $template_params;
        $params['field_name'] = 'custom_' . $id;
        $params['label'] = $result['values'][$id]['label'];
        try {
          civicrm_api3('UFField', 'create', $params);
        }
        catch (CiviCRM_API3_Exception $e) {
          CRM_Core_Error::debug_log_message("Failed to create profile field for '$field_name'.");
        }

      }
    }
  }

  // Lastly insert into managed entities so we can disable and remove
  // if this extension is disabled and removed.
  $sql = "INSERT INTO civicrm_managed SET module = 'net.ourpowerbase.participantfields',
      name = %0, entity_type = 'UFGroup', entity_id = %1";
  CRM_Core_DAO::executeQuery($sql, array(0 => array($new_name, 'String'), array($uf_group_id, 'Integer')));
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
