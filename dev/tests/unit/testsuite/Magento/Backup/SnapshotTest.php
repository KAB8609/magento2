<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backup_SnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     * @return \Magento\Backup\Snapshot
     */
    public function testGetDbBackupFilename()
    {
        $manager = $this->getMock(
            'Magento\Backup\Snapshot',
            array('getBackupFilename')
        );

        $file = 'var/backup/2.gz';
        $manager->expects($this->once())
            ->method('getBackupFilename')
            ->will($this->returnValue($file));

        $model = new \Magento\Backup\Snapshot();
        $model->setDbBackupManager($manager);
        $this->assertEquals($file, $model->getDbBackupFilename());
    }
}
