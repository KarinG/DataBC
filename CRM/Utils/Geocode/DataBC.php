<?php
/*
  +--------------------------------------------------------------------+
  | CiviCRM version 4.4                                                |
  +--------------------------------------------------------------------+
  | Copyright CiviCRM LLC (c) 2004-2013                                |
  +--------------------------------------------------------------------+
  | This file is a part of CiviCRM.                                    |
  |                                                                    |
  | CiviCRM is free software; you can copy, modify, and distribute it  |
  | under the terms of the GNU Affero General Public License           |
  | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
  |                                                                    |
  | CiviCRM is distributed in the hope that it will be useful, but     |
  | WITHOUT ANY WARRANTY; without even the implied warranty of         |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
  | See the GNU Affero General Public License for more details.        |
  |                                                                    |
  | You should have received a copy of the GNU Affero General Public   |
  | License and the CiviCRM Licensing Exception along                  |
  | with this program; if not, contact CiviCRM LLC                     |
  | at info[AT]civicrm[DOT]org. If you have questions about the        |
  | GNU Affero General Public License or the licensing of CiviCRM,     |
  | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
  +--------------------------------------------------------------------+
*/

/**
 *
 */

/**
 * Class that uses DataBC geocoder
 */
class CRM_Utils_Geocode_DataBC {

  /**
   * server to retrieve the lat/long
   *
   * @var string
   * @static
   */
  static protected $_server = 'geocoder.api.gov.bc.ca/';

  /**
   * uri of service
   *
   * @var string
   * @static
   */
  static protected $_uri = 'addresses.geojson';

  /**
   * function that takes an address object and gets the latitude / longitude for this
   * address. Note that at a later stage, we could make this function also clean up
   * the address into a more valid format
   *
   * @param object $address
   * @return boolean true if we modified the address, false otherwise
   * @static
   */
  static function format(&$values, $stateName = FALSE) {
    // we need a BC Province: 1101

    $config = CRM_Core_Config::singleton();

    if ((CRM_Utils_Array::value('state_province_id', $values) != '1101') && (CRM_Utils_Array::value('state_province', $values) != 'British Columbia'))
    {
      // if we get here we are NOT in British Columbia - try BackUp method!
      $backupgeoProvider = Civi::settings()->get('bcdata_backup_geoProvider');

      if ($backupgeoProvider == 'Google') {
        $class = new CRM_Utils_Geocode_Google();
        $class->format($values, $stateName = FALSE);
        return TRUE;
      }
      elseif ($backupgeoProvider == 'Yahoo') {
        $class = new CRM_Utils_Geocode_Yahoo();
        $class->format($values, $stateName = FALSE);
        return TRUE;
      }
      elseif (empty($backupgeoProvider)) {
        return FALSE;
      }
    }

    // require a street address and a city
    if (!CRM_Utils_Array::value('street_address', $values) || !CRM_Utils_Array::value('city', $values)) {
      return FALSE;
    }

    // The BCData geocoder does not look at postal code. Civic address only.
    $add = '';
    if (strpos(strtoupper(str_replace(' ', '', CRM_Utils_Array::value('street_address', $values))), 'POBOX') !== false) {
      return FALSE;
    }
    $add .= CRM_Utils_Array::value('street_address', $values);
    if (CRM_Utils_Array::value('supplemental_address_1', $values)) {
      if (strpos(strtoupper(str_replace(' ', '', CRM_Utils_Array::value('supplemental_address_1', $values))), 'POBOX') !== false) {
        return FALSE;
      }
      $add .= ', ' . CRM_Utils_Array::value('supplemental_address_1', $values);
    }
    $add .= ', ' . CRM_Utils_Array::value('city', $values);
    $add .= ', BC';
    $add = urlencode($add);

    $minScore = Civi::settings()->get('bcdata_match_threshold');
    $selectedPrecision = Civi::settings()->get('bcdata_match_precision');

    $precisions = array(
      0 => 'CIVIC_NUMBER',
      1 => 'BLOCK',
      2 => 'STREET',
      3 => 'LOCALITY',
      4 => 'PROVINCE',
    );

    foreach ($precisions as $mc) {
      $precisions_x[] = $mc;
      if ($mc == $precisions[$selectedPrecision]) {
        break;
      }
    }
    $matchPrecisions = urlencode(implode(',', $precisions_x));

    $query = 'https://' . self::$_server . self::$_uri . '?minScore=' . $minScore . '&addressString=' . $add;
    //$query = 'https://' . self::$_server . self::$_uri . '?minScore=' . $minScore . '&matchPrecision=' . $matchPrecisions . '&addressString=' . $add;

    $client = new GuzzleHttp\Client();
    try {
      $request = $client->request('GET', $query, [
        'timeout' =>  '3',
      ]);
    }
    catch (Exception $e) {
      CRM_Core_Error::debug_log_message('DataBC Geocoding failed', $out = false );
      return FALSE;
    }

    $string = $request->getBody();
    $result = json_decode($string, TRUE);

    if ($result === FALSE) {
      CRM_Core_Error::debug_log_message('DataBC Geocoding failed', $out = false );
      return FALSE;
    }

    if ($result && isset($result['features'][0])) {
      $first_match = array_shift($result['features']);
      if (isset($first_match['geometry']['coordinates'])) {
        $values['geo_code_1'] = $first_match['geometry']['coordinates'][1];
        $values['geo_code_2'] = $first_match['geometry']['coordinates'][0];
      }
      if (isset($first_match['properties'])) {
        $values['city'] = $first_match['properties']['localityName'];

        // Format the Postal Code: all UPPER case and SPACE
        $values['postal_code'] = strtoupper(str_replace(' ', '', $values['postal_code']));
        $values['postal_code'] = chunk_split($values['postal_code'], 3, ' ');

        // Paste together street_address
        // We are -not- returning the individual components to CiviCRM as it's not getting the parsing correct
        $values['street_address'] = $first_match['properties']['unitNumber'] . $first_match['properties']['unitNumberSuffix'] . ' ' . $first_match['properties']['civicNumber'] . ' ' . $first_match['properties']['streetName'] . ' ' . $first_match['properties']['streetType'] . ' ' . $first_match['properties']['streetDirection'];
      }
      return TRUE;
    }

    // reset the geo code values if we did not get any good values
    $values['geo_code_1'] = $values['geo_code_2'] = 'null';
    return FALSE;

    // TODO: should we limit the precision to avoid geocodes that are overly imprecise?
    //   see matchPrecision and locationPositionalAccuracy in specs/glossary
    //   https://github.com/bcgov/api-specs/blob/master/geocoder/glossary.md

    // TODO: is there a min acceptable score to ensure we're getting the same address?
    //   for example: 101 Anywhere Street, Kitchener, BC  geocodes to  101 Kitchener St, Ladysmith, BC;
    //            18 Wellington St N, Kitchener, BC geocodes to 18 Wellington St, New Westminster, BC

    // TODO: implement hook https://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_geocoderFormat
    //   as of 4.7.7+
    // This hook allows you to manipulate the Address object during geocoding, for instance to extract
    // additional fields from the geocoder's returned XML.
  }
}

