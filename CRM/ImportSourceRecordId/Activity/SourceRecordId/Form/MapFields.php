<?php

/**
 * This class gets the name of the file to upload.
 * Data mapper class.
 * Based on the legacy solution: https://github.com/civicrm/civicrm-core/blob/master/CRM/Activity/Import/Form/MapField.php
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_MapFields extends CRM_Activity_Import_Form_MapField {

  /**
   * Set variables up before form is built.
   * It extends the mapper fields list with the source record id.
   */
  public function preProcess() {
    parent::preProcess();
    $this->_mapperFields = array_merge($this->_mapperFields, ['source_record_id' => 'Source Record Id']);
    asort($this->_mapperFields);
  }
}
