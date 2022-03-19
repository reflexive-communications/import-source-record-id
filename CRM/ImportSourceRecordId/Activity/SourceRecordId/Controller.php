<?php

/**
 * State machine controller class.
 * Based on the legacy solution: https://github.com/civicrm/civicrm-core/blob/master/CRM/Activity/Import/Controller.php
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_Controller extends CRM_Core_Controller {

  /**
   * Class constructor.
   *
   * @param string $title
   * @param bool|int $action
   * @param bool $modal
   */
  public function __construct($title = NULL, $action = CRM_Core_Action::NONE, $modal = TRUE) {
    parent::__construct($title, $modal);

    set_time_limit(0);

    $this->_stateMachine = new CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachine($this, $action);

    // create and instantiate the pages
    $this->addPages($this->_stateMachine, $action);

    // add all the actions
    $config = CRM_Core_Config::singleton();
    $this->addActions($config->uploadDir, ['uploadFile']);
  }

}
