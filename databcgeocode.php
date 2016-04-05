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
 * Implements hook_civicrm_post().
 * Update coordinates when an address is created/updated.
 * 
 * TODO: DataBC request should be queued and processed asynchronously so as not to block an
 *   operation (such as a backoffice edit or worse: an online donation) if DataBC systems are
 *   slow/offline. However other Civi geocoders don't do this -- not good architecture!
 *   We might trust Google reliability more than DataBC, though.
 */
function databcgeocode_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {

  if ($objectName != 'Address' || ($op != 'create' && $op != 'edit')) {
    return;
  }

  // ignore addresses where geocoding has been overridden by the user
  if ($objectRef->manual_geo_code == 1) {
    return;
  }

  $address = databcgeocode_format_address($objectRef);

  if ($address) {
    $coordinates = databcgeocode_get_coordinates($address);
  }

  if (isset($coordinates['geo_code_1']) && isset($coordinates['geo_code_2'])) {
    if ($coordinates['geo_code_1'] != $objectRef->geo_code_1 ||
        $coordinates['geo_code_2'] != $objectRef->geo_code_2) {
      $objectRef->geo_code_1 = $coordinates['geo_code_1'];
      $objectRef->geo_code_2 = $coordinates['geo_code_2'];
      $objectRef->save();
    }
  }

}

function databcgeocode_format_address($address) {

  if (!is_a($address, 'CRM_Core_DAO_Address')) {
    return;
  }

  // only encode BC addresses - look at state/province, country, and postal code
  // (BC postal codes begin with 'V')
  if (($address->state_province_id != 'null' && $address->state_province_id != 1101)
      || ($address->country_id != 'null' && $address->country_id != 1039)) {
    return;
  }
  else if ($address->postal_code != 'null'
      && !preg_match('/^([vV]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/', $address->postal_code)) {
    return;
  }

  // require a street address and a city
  if (!$address->street_address || !$address->city) {
    return;
  }

  // The BCData geocoder does not look at postal code. Civic address only.
  // TODO: should the request consider supplemental_address_1 & 2? 
  $add = $address->street_address . ', ' . $address->city;

  if ($address->state_province_id != 'null') {
    $add .= ', BC';
  }

  return urlencode($add);

}

function databcgeocode_get_coordinates($address) {
  
  $query = "http://apps.gov.bc.ca/pub/geocoder/addresses.geojson?addressString={$address}";

  require_once 'HTTP/Request.php';
  $request = new HTTP_Request($query);
  $request->sendRequest();
  $string = $request->getResponseBody();
  $result = json_decode($string, TRUE);

  if ($result && isset($result['features'])) {
//drupal_set_message(print_r($result,TRUE));
    $first_match = array_shift($result['features']);
    if (isset($first_match['geometry']['coordinates'])) {
      return array(
        'geo_code_1' => $first_match['geometry']['coordinates'][1],
        'geo_code_2' => $first_match['geometry']['coordinates'][0],
      );
    }
  }

  // TODO: should we limit the precision to avoid geocodes that are overly imprecise?
  //   see matchPrecision and locationPositionalAccuracy in specs/glossary
  //   http://www.data.gov.bc.ca/dbc/geographic/locate/physical_address_geo/glossary_of_terms.page

  // TODO: is there a min acceptable score to ensure we're getting the same address?
  //   for example: 101 Anywhere Street, Kitchener, BC  geocodes to  101 Kitchener St, Ladysmith, BC;
  //            18 Wellington St N, Kitchener, BC geocodes to 18 Wellington St, New Westminster, BC

  return;

}
