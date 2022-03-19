<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */


/**
 * Class to parse activity csv files.
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser extends CRM_Activity_Import_Parser_Activity {

  /**
   * The initializer code, called before the processing.
   * Based on the legacy solution: https://github.com/civicrm/civicrm-core/blob/master/CRM/Activity/Import/Parser/Activity.php
   * The customization is necessary due to the fields are set here.
   */
  public function init() {
    $activityContact = CRM_Activity_BAO_ActivityContact::import();
    $activityTarget['target_contact_id'] = $activityContact['contact_id'];
    $fields = array_merge(CRM_Activity_BAO_Activity::importableFields(),
      $activityTarget
    );

    $fields = array_merge($fields, [
      'source_contact_id' => [
        'title' => ts('Source Contact'),
        'headerPattern' => '/Source.Contact?/i',
      ],
      'activity_label' => [
        'title' => ts('Activity Type Label'),
        'headerPattern' => '/(activity.)?type label?/i',
      ],
      'source_record_id' => [
        'title' => ts('Source Record Id'),
        'headerPattern' => '/.ource..ecord..d/i',
      ],
    ]);

    foreach ($fields as $name => $field) {
      $field['type'] = CRM_Utils_Array::value('type', $field, CRM_Utils_Type::T_INT);
      $field['dataPattern'] = CRM_Utils_Array::value('dataPattern', $field, '//');
      $field['headerPattern'] = CRM_Utils_Array::value('headerPattern', $field, '//');
      if (!empty($field['custom_group_id'])) {
        $field['title'] = $field["groupTitle"] . ' :: ' . $field["title"];
      }
      $this->addField($name, $field['title'], $field['type'], $field['headerPattern'], $field['dataPattern']);
    }

    $this->_newActivity = [];

    $this->setActiveFields($this->_mapperKeys);

    // FIXME: we should do this in one place together with Form/MapField.php
    $this->_contactIdIndex = -1;

    $index = 0;
    foreach ($this->_mapperKeys as $key) {
      switch ($key) {
        case 'target_contact_id':
        case 'external_identifier':
          $this->_contactIdIndex = $index;
          break;
      }
      $index++;
    }
  }

  /**
   * Handle the values in import mode.
   * The legacy solution has been extended
   * with the source record id related data handling.
   *
   * @param int $onDuplicate
   *   The code for what action to take on duplicates.
   * @param array $values
   *   The array of values belonging to this line.
   *
   * @return bool
   *   the result of this processing
   * @throws \CRM_Core_Exception
   */
  public function import($onDuplicate, &$values) {
    // First make sure this is a valid line
    try {
      $this->validateValues($values);
    }
    catch (CRM_Core_Exception $e) {
      return $this->addError($values, [$e->getMessage()]);
    }
    $params = $this->getApiReadyParams($values);
    // For date-Formats.
    $session = CRM_Core_Session::singleton();
    $dateType = $session->get('dateTypes');

    $customFields = CRM_Core_BAO_CustomField::getFields('Activity');

    foreach ($params as $key => $val) {
      if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
        if (!empty($customFields[$customFieldID]) && $customFields[$customFieldID]['data_type'] == 'Date') {
          CRM_Contact_Import_Parser_Contact::formatCustomDate($params, $params, $dateType, $key);
        }
        elseif (!empty($customFields[$customFieldID]) && $customFields[$customFieldID]['data_type'] == 'Boolean') {
          $params[$key] = CRM_Utils_String::strtoboolstr($val);
        }
      }
      elseif ($key === 'activity_date_time') {
        $params[$key] = CRM_Utils_Date::formatDate($val, $dateType);
      }
      elseif ($key === 'activity_subject') {
        $params['subject'] = $val;
      }
    }

    if ($this->_contactIdIndex < 0) {

      // Retrieve contact id using contact dedupe rule.
      // Since we are supporting only individual's activity import.
      $params['contact_type'] = 'Individual';
      $params['version'] = 3;
      $error = _civicrm_api3_deprecated_duplicate_formatted_contact($params);

      if (CRM_Core_Error::isAPIError($error, CRM_Core_ERROR::DUPLICATE_CONTACT)) {
        $matchedIDs = explode(',', $error['error_message']['params'][0]);
        if (count($matchedIDs) > 1) {
          array_unshift($values, 'Multiple matching contact records detected for this row. The activity was not imported');
          return CRM_Import_Parser::ERROR;
        }
        $cid = $matchedIDs[0];
        $params['target_contact_id'] = $cid;
        $params['version'] = 3;
        if (isset($params['source_record_id'])) {
          $survey = civicrm_api('survey', 'get', ['source_record_id' => $params['source_record_id'], 'version' => 3]);
          if (!empty($survey['is_error'])) {
            array_unshift($values, $survey['error_message']);
            return CRM_Import_Parser::ERROR;
          }
          if (count($survey['values']) === 0) {
            array_unshift($values, 'No matching survey for identifier: '.$params['source_record_id']);
            return CRM_Import_Parser::ERROR;
          }
          $activityTypeKey = isset($params['activity_type_id']) ? 'activity_type_id' : 'activity_label';
          $signatures = civicrm_api('activity', 'get', [
            'source_record_id' => $params['source_record_id'],
            'version' => 3,
            'source_contact_id' => $cid,
            $activityTypeKey => $params[$activityTypeKey],
          ]);
          if (!empty($signatures['is_error'])) {
            array_unshift($values, $signatures['error_message']);
            return CRM_Import_Parser::ERROR;
          }
          if (count($signatures['values']) !== 0) {
            array_unshift($values, 'Survey already filled by contact: '.$cid);
            return CRM_Import_Parser::ERROR;
          }
          $params['source_contact_id'] = $cid;
        }
        $newActivity = civicrm_api('activity', 'create', $params);
        if (!empty($newActivity['is_error'])) {
          array_unshift($values, $newActivity['error_message']);
          return CRM_Import_Parser::ERROR;
        }

        $this->_newActivity[] = $newActivity['id'];
        return CRM_Import_Parser::VALID;

      }
      // Using new Dedupe rule.
      $ruleParams = [
        'contact_type' => 'Individual',
        'used' => 'Unsupervised',
      ];
      $fieldsArray = CRM_Dedupe_BAO_DedupeRule::dedupeRuleFields($ruleParams);

      $disp = NULL;
      foreach ($fieldsArray as $value) {
        if (array_key_exists(trim($value), $params)) {
          $paramValue = $params[trim($value)];
          if (is_array($paramValue)) {
            $disp .= $params[trim($value)][0][trim($value)] . " ";
          }
          else {
            $disp .= $params[trim($value)] . " ";
          }
        }
      }

      if (!empty($params['external_identifier'])) {
        if ($disp) {
          $disp .= "AND {$params['external_identifier']}";
        }
        else {
          $disp = $params['external_identifier'];
        }
      }

      array_unshift($values, 'No matching Contact found for (' . $disp . ')');
      return CRM_Import_Parser::ERROR;
    }
    if (!empty($params['external_identifier'])) {
      $targetContactId = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact',
        $params['external_identifier'], 'id', 'external_identifier'
      );

      if (!empty($params['target_contact_id']) &&
        $params['target_contact_id'] != $targetContactId
      ) {
        array_unshift($values, 'Mismatch of External ID:' . $params['external_identifier'] . ' and Contact Id:' . $params['target_contact_id']);
        return CRM_Import_Parser::ERROR;
      }
      if ($targetContactId) {
        $params['target_contact_id'] = $targetContactId;
      }
      else {
        array_unshift($values, 'No Matching Contact for External ID:' . $params['external_identifier']);
        return CRM_Import_Parser::ERROR;
      }
    }

    $params['version'] = 3;
    if (isset($params['source_record_id'])) {
      $survey = civicrm_api('survey', 'get', ['source_record_id' => $params['source_record_id'], 'version' => 3]);
      if (!empty($survey['is_error'])) {
        array_unshift($values, $survey['error_message']);
        return CRM_Import_Parser::ERROR;
      }
      if (count($survey['values']) === 0) {
        array_unshift($values, 'No matching survey for identifier: '.$params['source_record_id']);
        return CRM_Import_Parser::ERROR;
      }
      $activityTypeKey = isset($params['activity_type_id']) ? 'activity_type_id' : 'activity_label';
      $signatures = civicrm_api('activity', 'get', [
        'source_record_id' => $params['source_record_id'],
        'version' => 3,
        'source_contact_id' => $cid,
        $activityTypeKey => $params[$activityTypeKey],
      ]);
      if (!empty($signatures['is_error'])) {
        array_unshift($values, $signatures['error_message']);
        return CRM_Import_Parser::ERROR;
      }
      if (count($signatures['values']) !== 0) {
        array_unshift($values, 'Survey already filled by contact: '.$cid);
        return CRM_Import_Parser::ERROR;
      }
      $params['source_contact_id'] = $cid;
    }
    $newActivity = civicrm_api('activity', 'create', $params);
    if (!empty($newActivity['is_error'])) {
      array_unshift($values, $newActivity['error_message']);
      return CRM_Import_Parser::ERROR;
    }

    $this->_newActivity[] = $newActivity['id'];
    return CRM_Import_Parser::VALID;
  }
}
