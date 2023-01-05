<?php

class CRM_DataBCGeocode_Form_Settings extends CRM_Core_Form {

  const D_THRESHOLD = 75;
  const D_PRECISION = 'STREET';

  function buildQuickForm() {

    // allow admin to specify scoring threshold for matches
    $this->addElement('text', 'match_threshold', ts('Match Threshold'));

    // allow admin to specify precision level
    $precisions = array(
      0 => 'CIVIC_NUMBER',
      1 => 'BLOCK',
      2 => 'STREET',
      3 => 'LOCALITY',
      4 => 'PROVINCE',
    );

    $this->addRadio('match_precision', ts('Match Precision'), $precisions, NULL, '<br />');

    // add Backup_GeoCoder Provider (for non BC addresses):
    $all_geo = CRM_Core_SelectValues::geoProvider();
    // check for DataBC and remove it from the array:
    $backup_geo = array_diff($all_geo, array('DataBC'));

    $this->addElement('select', 'backup_geoProvider', ts('Backup Geocoding Provider'), array('' => '- select -') + $backup_geo);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();

  }

  function setDefaultValues() {

    $defaults = array(
      'match_threshold' => Civi::settings()->get('bcdata_match_threshold') ?? self::D_THRESHOLD,
      'match_precision' => Civi::settings()->get('bcdata_match_precision') ?? self::D_PRECISION,
      'backup_geoProvider' => Civi::settings()->get('bcdata_backup_geoProvider'),
    );

    return $defaults;
  }

  function postProcess() {

    $values = $this->exportValues();

    if (CRM_Utils_Array::value('match_threshold', $values)) {
      Civi::settings()->set('bcdata_match_threshold', $values['match_threshold']);
      Civi::settings()->set('bcdata_match_precision', $values['match_precision']);
      Civi::settings()->set('bcdata_backup_geoProvider', $values['backup_geoProvider']);
    }

    parent::postProcess();

  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels. We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
