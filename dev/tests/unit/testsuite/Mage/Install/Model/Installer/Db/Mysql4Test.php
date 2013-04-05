<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_Model_Installer_Db_Mysql4Test extends PHPUnit_Framework_TestCase
{
    /**
     * Test possible ways of declaring InnoDB engine by MySQL
     *
     * @dataProvider possibleEngines
     * @param array $supportedEngines
     * @param $expectedResult
     * @return void
     */
    public function testSupportEngine(array $supportedEngines, $expectedResult)
    {
        $connectionMock = $this->getMock('Varien_Db_Adapter_Interface');
        $resourceMock = $this->getMock('Mage_Core_Model_Resource', array('createConnection'), array(), '', false);
        $resourceMock->expects($this->once())->method('createConnection')->will($this->returnValue($connectionMock));

        $connectionMock->expects($this->once())->method('fetchPairs')->will($this->returnValue($supportedEngines));

        $installer = new Mage_Install_Model_Installer_Db_Mysql4($resourceMock);
        $this->assertEquals($expectedResult, $installer->supportEngine());
    }

    /**
     * Data provider for returned engines from mysql and expectations.
     * @return array
     */
    public function possibleEngines()
    {
        return array(
            array(array('InnoDB' => 'DEFAULT'),  true),
            array(array('InnoDB' => 'YES'),      true),
            array(array('wrongEngine' => '123'), false)
        );
    }
}
