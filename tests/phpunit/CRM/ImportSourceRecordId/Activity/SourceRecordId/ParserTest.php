<?php

/**
 * Testcases for the parser application.
 *
 * @group headless
 */
class CRM_ImportSourceRecordId_Activity_SourceRecordId_ParserTest extends CRM_ImportSourceRecordId_HeadlessBase
{
    /**
     * init test case.
     */
    public function testInit()
    {
        $mapperKeys = [];
        $parser = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser($mapperKeys);
        self::assertEmpty($parser->init(), 'Init supposed to be empty.');
    }

    /**
     * import test case.
     */
    public function testImport()
    {
        $values = [
            '01@email.com',
            '2021-01-02 13:13',
            'Petition',
            'Subject example',
            '1',
        ];
        $mapperKeys = ['email', 'activity_date', 'activity_type_id', 'subject', 'source_record_id'];
        $parser = new CRM_ImportSourceRecordId_Activity_SourceRecordId_Parser($mapperKeys);
        self::assertEmpty($parser->init(), 'Init supposed to be empty.');
        $isImported = $parser->import(CRM_Import_Parser::DUPLICATE_SKIP, $values);
        self::assertSame(CRM_Import_Parser::ERROR, $isImported, 'import supposed to be errored due to the missing contact.');
    }
}
