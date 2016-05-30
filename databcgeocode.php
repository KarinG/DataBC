<?php

require_once 'databcgeocode.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function databcgeocode_civicrm_config(&$config) {
  _databcgeocode_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function databcgeocode_civicrm_xmlMenu(&$files) {
  _databcgeocode_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function databcgeocode_civicrm_install() {
  _databcgeocode_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function databcgeocode_civicrm_uninstall() {
  _databcgeocode_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function databcgeocode_civicrm_enable() {
  _databcgeocode_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function databcgeocode_civicrm_disable() {
  _databcgeocode_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function databcgeocode_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _databcgeocode_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function databcgeocode_civicrm_managed(&$entities) {
  _databcgeocode_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function databcgeocode_civicrm_caseTypes(&$caseTypes) {
  _databcgeocode_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function databcgeocode_civicrm_angularModules(&$angularModules) {
_databcgeocode_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function databcgeocode_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _databcgeocode_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_buildForm.
 * Update the form at settings page: civicrm/admin/setting/mapping?reset=1
 * Create a selection option: databc
 *
 */
function databcgeocode_civicrm_buildForm($formName, &$form) {

  if ($formName != 'CRM_Admin_Form_Setting_Mapping') {
    return;
  }

  // Ok we're on the right form
  $geo = CRM_Core_SelectValues::geoProvider();
  $geo['DataBC'] = 'DataBC';
  // add DataBC option to existing GeoCoder Providers:
  $form->addElement('select', 'geoProvider', ts('Geocoding Provider'), array('' => '- select -') + $geo);

  // add our template:
  CRM_Core_Region::instance('page-body')->add(array(
    'template' => 'CRM/DataBCGeocode/DataBCAdmin.tpl'
  ));
}

