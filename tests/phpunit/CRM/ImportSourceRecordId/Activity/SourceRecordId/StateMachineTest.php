<?php

/**
 * Testcases for the state machine class.
 *
 * @group headless
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachineTest extends CRM_ImportSourceRecordId_HeadlessBase
{
    /**
     * It tests the class constructor.
     * The pages has to be our custom one.
     */
    public function testConstructor(): void
    {
        $sm = new CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachine(new CRM_ImportSourceRecordId_Activity_SourceRecordId_Controller());
        self::assertSame(CRM_ImportSourceRecordId_Activity_SourceRecordId_StateMachine::PAGES, $sm->getPages());
    }
}
