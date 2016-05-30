<?php

require_once 'CRM/Core/Form.php';

class CRM_DataBCGeocode_Form_Settings extends CRM_Core_Form {

  const D_THRESHOLD = 80;
  const D_PRECISION = 'STREET';
  const PRECISIONS = array(
    'CIVIC_NUMBER',
    'BLOCK',
    'STREET',
    'LOCALITY',
    'PROVINCE',
  );

  function buildQuickForm() {

    // allow admin to specify scoring threshold for matches
    $this->addElement('text', 'match_threshold', ts('Match Threshold'));

    // allow admin to specify precision level
    $precisions = array();
    foreach (self::PRECISIONS as $mc) {
      $precisions[$mc] = ts($mc);
    }
    $this->addRadio('match_precision', ts('Match Precision'), $precisions, NULL, '<br />');

    // add Backup_GeoCoder Provider (for non BC addresses):
    $backup_geo = CRM_Core_SelectValues::geoProvider();
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
      'match_threshold' => CRM_Core_BAO_Setting::getItem('bcdatageocode', 'match_threshold', NULL, self::D_THRESHOLD),
      'match_precision' => CRM_Core_BAO_Setting::getItem('bcdatageocode', 'match_precision', NULL, self::D_PRECISION),
      'backup_geoProvider' => CRM_Core_BAO_Setting::getItem('bcdatageocode', 'backup_geoProvider', NULL, self::D_PRECISION),
    );

    return $defaults;
  }

  function postProcess() {

    $values = $this->exportValues();

    if (CRM_Utils_Array::value('match_threshold', $values)) {
      CRM_Core_BAO_Setting::setItem($values['match_threshold'], 'bcdatageocode', 'match_threshold');
      CRM_Core_BAO_Setting::setItem($values['match_precision'], 'bcdatageocode', 'match_precision');
    }
    CRM_Core_BAO_Setting::setItem($values['backup_geoProvider'], 'bcdatageocode', 'backup_geoProvider');

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
