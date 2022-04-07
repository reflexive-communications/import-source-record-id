<?php

/**
 * Testcases for the controller class.
 *
 * @group headless
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_ControllerTest extends CRM_ImportSourceRecordId_HeadlessBase
{
    /**
     * It tests the class constructor.
     * The state machine has to be our custom one.
     */
    public function testConstructor(): void
    {
        $controller = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Controller();
        $sm = $controller->getStateMachine();
        self::assertSame('CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachine', get_class($sm));
    }
}
