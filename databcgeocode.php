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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function databcgeocode_civicrm_install() {
  _databcgeocode_civix_civicrm_install();
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

// /**
//  * Implements hook_civicrm_postInstall().
//  *
//  * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
//  */
// function databcgeocode_civicrm_postInstall() {
//   _databcgeocode_civix_civicrm_postInstall();
// }

// /**
//  * Implements hook_civicrm_entityTypes().
//  *
//  * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
//  */
// function databcgeocode_civicrm_entityTypes(&$entityTypes) {
//   _databcgeocode_civix_civicrm_entityTypes($entityTypes);
// }
