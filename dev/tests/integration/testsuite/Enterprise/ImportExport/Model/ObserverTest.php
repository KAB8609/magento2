<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_ImportExport_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store crontab/jobs/enterprise_import_export_log_clean/schedule/cron_expr 1
     */
    public function testScheduledLogClean()
    {
        $model = new Enterprise_ImportExport_Model_Observer;
        $model->scheduledLogClean('not_used', true);
        /** @var $dirs Mage_Core_Model_Dir */
        $dirs = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_Dir');
        $this->assertFileExists($dirs->getDir(Mage_Core_Model_Dir::LOG)
            . DIRECTORY_SEPARATOR
            . Enterprise_ImportExport_Model_Scheduled_Operation::LOG_DIRECTORY
        );
    }
}
