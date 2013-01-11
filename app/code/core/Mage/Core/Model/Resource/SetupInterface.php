<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Resource_SetupInterface
{
    const DEFAULT_SETUP_CONNECTION  = 'core_setup';
    const VERSION_COMPARE_EQUAL     = 0;
    const VERSION_COMPARE_LOWER     = -1;
    const VERSION_COMPARE_GREATER   = 1;

    const TYPE_DB_INSTALL           = 'install';
    const TYPE_DB_UPGRADE           = 'upgrade';
    const TYPE_DB_ROLLBACK          = 'rollback';
    const TYPE_DB_UNINSTALL         = 'uninstall';
    const TYPE_DATA_INSTALL         = 'data-install';
    const TYPE_DATA_UPGRADE         = 'data-upgrade';

    /**
     * Apply module resource install, upgrade and data scripts
     *
     * @return Mage_Core_Model_Resource_SetupInterface
     */
    public function applyUpdates();

    /**
     * Check call afterApplyAllUpdates method for setup class
     *
     * @return boolean
     */
    public function getCallAfterApplyAllUpdates();

    /**
     * Run each time after applying of all updates,
     *
     * @return Mage_Core_Model_Resource_SetupInterface
     */
    public function afterApplyAllUpdates();

    /**
     *  Apply data updates to the system after upgrading
     */
    public function applyDataUpdates();
}
