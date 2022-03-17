<?php

/**
 * This class previews the uploaded file and returns summary statistics.
 * Based on the legacy solution: https://github.com/civicrm/civicrm-core/blob/master/CRM/Activity/Import/Form/Preview.php
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_Preview extends CRM_Activity_Import_Form_Preview
{
    /**
     * Process the mapped fields and map it into the uploaded file preview the file and extract some summary statistics.
     * The duplication was necessary due to the import parser is instantiated and executed here.
     */
    public function postProcess()
    {
        $fileName = $this->controller->exportValue('DataSource', 'uploadFile');
        $separator = $this->controller->exportValue('DataSource', 'fieldSeparator');
        $skipColumnHeader = $this->controller->exportValue('DataSource', 'skipColumnHeader');
        $invalidRowCount = $this->get('invalidRowCount');
        $conflictRowCount = $this->get('conflictRowCount');
        $onDuplicate = $this->get('onDuplicate');

        $mapper = $this->controller->exportValue('MapFields', 'mapper');
        $mapperKeys = [];

        foreach ($mapper as $key => $value) {
            $mapperKeys[$key] = $mapper[$key][0];
        }

        $parser = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser($mapperKeys);

        $mapFields = $this->get('fields');

        foreach ($mapper as $key => $value) {
            $header = [];
            if (isset($mapFields[$mapper[$key][0]])) {
                $header[] = $mapFields[$mapper[$key][0]];
            }
            $mapperFields[] = implode(' - ', $header);
        }
        $parser->run(
            $fileName,
            $separator,
            $mapperFields,
            $skipColumnHeader,
            CRM_Import_Parser::MODE_IMPORT,
            $onDuplicate,
            $this->get('statusID'),
            $this->get('totalRowCount')
        );

        // add all the necessary variables to the form
        $parser->set($this, CRM_Import_Parser::MODE_IMPORT);

        // check if there is any error occurred

        $errorStack = CRM_Core_Error::singleton();
        $errors = $errorStack->getErrors();
        $errorMessage = [];

        if (is_array($errors)) {
            foreach ($errors as $key => $value) {
                $errorMessage[] = $value['message'];
            }

            $errorFile = $fileName['name'] . '.error.log';

            if ($fd = fopen($errorFile, 'w')) {
                fwrite($fd, implode('\n', $errorMessage));
            }
            fclose($fd);

            $this->set('errorFile', $errorFile);
            $urlParams = 'type=' . CRM_Import_Parser::ERROR . '&parser=CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser';
            $this->set('downloadErrorRecordsUrl', CRM_Utils_System::url('civicrm/export', $urlParams));
            $urlParams = 'type=' . CRM_Import_Parser::CONFLICT . '&parser=CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser';
            $this->set('downloadConflictRecordsUrl', CRM_Utils_System::url('civicrm/export', $urlParams));
            $urlParams = 'type=' . CRM_Import_Parser::NO_MATCH . '&parser=CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser';
            $this->set('downloadMismatchRecordsUrl', CRM_Utils_System::url('civicrm/export', $urlParams));
        }
    }
}
