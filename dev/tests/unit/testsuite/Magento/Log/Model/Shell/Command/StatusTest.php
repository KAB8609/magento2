<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Log_Model_Shell_Command_StatusTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shellMock;

    /**
     * @var \Magento\Log\Model\Shell\Command\Status
     */
    protected $_model;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento_Log_Model_Resource_ShellFactory',
            array('create'), array(), '', false
        );
        $this->_shellMock = $this->getMock('Magento\Log\Model\Resource\Shell', array(), array(), '', false);
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($this->_shellMock));
        $this->_model = new \Magento\Log\Model\Shell\Command\Status($this->_factoryMock);
    }

    public function testExecuteWithoutDataTotalAndHeadLinesFormatting()
    {
        $data = array();
        $this->_shellMock->expects($this->once())->method('getTablesInfo')->will($this->returnValue($data));
        $output = $this->_model->execute();
        $total = '/Total( )+\|( )+0( )+\|( )+0 b( )+\|( )+0 b( )+\|/';
        $this->assertRegExp($total, $output, 'Incorrect Total Line');

        $head = '/Table Name( )+\|( )+Rows( )+\|( )+Data Size( )+\|( )+Index Size( )+\|/';
        $this->assertRegExp($head, $output, 'Incorrect Head Line');
    }

    /**
     * @param array $tableData
     * @param string $expected
     * @dataProvider executeDataFormatDataProvider
     */
    public function testExecuteWithData($tableData, $expected)
    {
        $data = array($tableData);
        $this->_shellMock->expects($this->once())->method('getTablesInfo')->will($this->returnValue($data));
        $this->assertRegExp($expected, $this->_model->execute());
    }

    public function executeDataFormatDataProvider()
    {
        return array(
           array(
               array(
                   'name' => 'table_1',
                   'rows' => 1500,
                   'data_length' => 1000,
                   'index_length' => 1024 * 1024,
               ),
               '/table_1( )+\|( )+1\.50K( )+\|( )+1000 b( )+\|( )+1\.00Mb( )+\|/'
           ),

            array(
               array(
                   'name' => 'table_2',
                   'rows' => 1500000,
                   'data_length' => 1024 * 1024 * 1024,
                   'index_length' => 1024 * 1024 * 1024 * 500,
               ),
                '/table_2( )+\|( )+1\.50M( )+\|( )+1\.00Gb( )+\|( )+500\.00Gb( )+\|/'
           ),
        );
    }
}