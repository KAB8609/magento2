<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Log
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Log
 */
class Mage_Log_Model_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * Returns prepared model
     *
     * @param string $entryPoint
     * @return Mage_Log_Model_Shell
     */
    protected function _getModel($entryPoint = 'fake.php')
    {
        return new Mage_Log_Model_Shell($entryPoint);
    }

    /**
     * Returns result of running model - can be real model or mocked one
     *
     * @param Mage_Log_Model_Shell $model Can be mock
     * @return string
     */
    protected function _run($model)
    {
        ob_start();
        $model->run();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function testGetUsageHelp()
    {
        $model = $this->_getModel('testme.php');
        $this->assertContains('testme.php', $model->getUsageHelp());
    }

    public function testRunWithoutParams()
    {
        $model = $this->_getModel('testme.php');
        $result = $this->_run($model);
        $this->assertContains('testme.php', $result);
        $this->assertContains('log', $result); // Something about logs
    }

    public function testRunLogStatus()
    {
        $model = $this->_getModel('testme.php');
        $model->setRawArgs(array('testme.php', 'status'));
        $result = $this->_run($model);

        $this->assertNotContains('testme.php', $result);
        $this->assertNotContains('Usage:', $result);
        $this->assertContains('Table', $result);
        $this->assertContains('Total', $result);
        $this->assertContains('Rows', $result);
    }
}