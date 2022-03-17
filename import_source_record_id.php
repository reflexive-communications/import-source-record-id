<?php

require_once 'import_source_record_id.civix.php';

// phpcs:disable
use CRM_ImportSourceRecordId_ExtensionUtil as E;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function import_source_record_id_civicrm_config(&$config)
{
    _import_source_record_id_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function import_source_record_id_civicrm_xmlMenu(&$files)
{
    _import_source_record_id_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function import_source_record_id_civicrm_install()
{
    _import_source_record_id_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function import_source_record_id_civicrm_postInstall()
{
    _import_source_record_id_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function import_source_record_id_civicrm_uninstall()
{
    _import_source_record_id_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function import_source_record_id_civicrm_enable()
{
    _import_source_record_id_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function import_source_record_id_civicrm_disable()
{
    _import_source_record_id_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function import_source_record_id_civicrm_upgrade($op, CRM_Queue_Queue $queue = null)
{
    return _import_source_record_id_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function import_source_record_id_civicrm_managed(&$entities)
{
    _import_source_record_id_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Add CiviCase types provided by this extension.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function import_source_record_id_civicrm_caseTypes(&$caseTypes)
{
    _import_source_record_id_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Add Angular modules provided by this extension.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function import_source_record_id_civicrm_angularModules(&$angularModules)
{
    // Auto-add module files from ./ang/*.ang.php
    _import_source_record_id_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function import_source_record_id_civicrm_alterSettingsFolders(&$metaDataFolders = null)
{
    _import_source_record_id_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function import_source_record_id_civicrm_entityTypes(&$entityTypes)
{
    _import_source_record_id_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function import_source_record_id_civicrm_themes(&$themes)
{
    _import_source_record_id_civix_civicrm_themes($themes);
}

// The following hooks are implemented by us

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function import_source_record_id_civicrm_navigationMenu(&$menu)
{
    _import_source_record_id_civix_insert_navigation_menu($menu, 'Contacts', [
        'label' => E::ts('Import Activities (source record ID)'),
        'name' => 'import_activity_source_record_id',
        'url' => 'civicrm/activity/import/source-record-id',
        'permission' => 'import contacts,access CiviCRM',
        'operator' => 'and',
        'separator' => 0,
    ]);
    _import_source_record_id_civix_navigationMenu($menu);
}
