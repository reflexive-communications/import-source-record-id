<?php

use CRM_CustomImports_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Civi\Test\CiviEnvBuilder;
use Civi\Api4\CustomGroup;
use Civi\Api4\CustomField;

/**
 * Base testclass to eliminate the code duplication.
 *
 * @group headless
 */
class CRM_ImportSourceRecordId_HeadlessBase extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface
{
    protected static $index = 1;
    /**
     * Setup used when HeadlessInterface is implemented.
     *
     * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
     *
     * @link https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
     *
     * @return \Civi\Test\CiviEnvBuilder
     *
     * @throws \CRM_Extension_Exception_ParseException
     */
    public function setUpHeadless(): CiviEnvBuilder
    {
        return \Civi\Test::headless()
            ->installMe(__DIR__)
            ->apply();
    }

    public function setUp():void
    {
        parent::setUp();
    }

    public function tearDown():void
    {
        parent::tearDown();
    }
    /**
     * Apply a forced rebuild of DB, thus
     * create a clean DB before running tests
     *
     * @throws \CRM_Extension_Exception_ParseException
     */
    public static function setUpBeforeClass(): void
    {
        // Resets DB and install depended extension
        \Civi\Test::headless()
            ->installMe(__DIR__)
            ->apply(true);
    }

    /**
     * Create a clean DB after running tests
     *
     * @throws CRM_Extension_Exception_ParseException
     */
    public static function tearDownAfterClass(): void
    {
        \Civi\Test::headless()
            ->uninstallMe(__DIR__)
            ->apply(true);
    }
}
