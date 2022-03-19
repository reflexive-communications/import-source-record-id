<?php

/**
 * State machine for managing different states of the Import process.
 * Based on the legacy solution: https://github.com/civicrm/civicrm-core/blob/409ffdf5d67e22566a7e9f6086900cc00b45a08d/CRM/Import/StateMachine.php
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachine extends CRM_Core_StateMachine {

  public const PAGES = [
    'CRM_Activity_Import_Form_DataSource' => null,
    'CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_MapFields' => null,
    'CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_Preview' => null,
    'CRM_Activity_Import_Form_Summary' => null,
  ];

  /**
   * Class constructor.
   *
   * @param object $controller
   * @param \const|int $action
   */
  public function __construct($controller, $action = CRM_Core_Action::NONE) {
    parent::__construct($controller, $action);

    $this->_pages = self::PAGES;

    $this->addSequentialPages($this->_pages, $action);
  }

}
