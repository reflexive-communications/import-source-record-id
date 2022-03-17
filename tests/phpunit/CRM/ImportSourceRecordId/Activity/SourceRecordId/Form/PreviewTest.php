<?php

use CRM_CustomImports_ExtensionUtil as E;

/**
 * Testcases for the preview form.
 *
 * @group headless
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_PreviewTest extends CRM_ImportSourceRecordId_HeadlessBase
{
    /**
     * postProcess test case.
     */
    public function testPostProcess()
    {
        // Create a petition
        $petition = civicrm_api3('Survey', 'create', [
            'title' => "Example Survey Title",
            'activity_type_id' => "Petition",
            'is_active' => 1,
        ]);
        // Create contacts. gather the number of activityes for them.
        $contactNotImported = civicrm_api3('Contact', 'create', [
            'sequential' => 1,
            'contact_type' => 'Individual',
            'email' => '01@email.com',
        ]);
        $contactImportedA = civicrm_api3('Contact', 'create', [
            'sequential' => 1,
            'contact_type' => 'Individual',
            'email' => '02@email.com',
        ]);
        $contactImportedB = civicrm_api3('Contact', 'create', [
            'sequential' => 1,
            'contact_type' => 'Individual',
            'email' => '03@email.com',
        ]);
        $originalNumberOfActivityContactNotImported = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactNotImported['values'][0]['id'],
        ]);
        $originalNumberOfActivityContactImportedA = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactImportedA['values'][0]['id'],
        ]);
        $originalNumberOfActivityContactImportedB = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactImportedB['values'][0]['id'],
        ]);
        $originalNumberOfActivities = civicrm_api3('Activity', 'getcount', [
            'activity_type_id' => 'Petition',
        ]);
        // Run import, gather new activity numbers.
        $form = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Form_Preview();
        $form->controller = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Controller();
        $container =& $form->controller->container();
        $container['values']['MapFields']['mapper'] = [
            0 => [0 => 'email'],
            1 => [0 => 'activity_date_time'],
            2 => [0 => 'activity_type_id'],
            3 => [0 => 'doNotImport'],
            4 => [0 => 'subject'],
            5 => [0 => 'source_record_id'],
        ];
        $container['values']['DataSource']['uploadFile'] = ['name' => __DIR__.'/test.csv'];
        $container['values']['DataSource']['fieldSeparator'] = ',';
        $container['values']['DataSource']['skipColumnHeader'] = '1';
        $form->set('contactType', CRM_Import_Parser::CONTACT_INDIVIDUAL);
        self::assertEmpty($form->preProcess(), 'PreProcess supposed to be empty.');
        CRM_Core_Session::singleton()->set('dateTypes', 1);
        self::assertEmpty($form->postProcess(), 'PostProcess supposed to be empty.');
        $newNumberOfActivityContactNotImported = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactNotImported['values'][0]['id'],
        ]);
        $newNumberOfActivityContactImportedA = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactImportedA['values'][0]['id'],
        ]);
        $newNumberOfActivityContactImportedB = civicrm_api3('ActivityContact', 'getcount', [
            'contact_id' => $contactImportedB['values'][0]['id'],
        ]);
        // Compare the numbers.
        self::assertSame($originalNumberOfActivityContactNotImported, $newNumberOfActivityContactNotImported);
        self::assertSame($originalNumberOfActivityContactImportedA + 2, $newNumberOfActivityContactImportedA);
        self::assertSame($originalNumberOfActivityContactImportedB + 2, $newNumberOfActivityContactImportedB);
        // Count the signers with the search.
        $newNumberOfActivities = civicrm_api3('Activity', 'getcount', [
            'activity_type_id' => 'Petition',
        ]);
        self::assertSame($originalNumberOfActivities + 2, $newNumberOfActivities);
    }
}
