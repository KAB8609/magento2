<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Invitation
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Invitation_Block_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Invitation_Block_Form
     */
    protected $_block;

    /**
     * Remembered old value of store config
     * @var array
     */
    protected $_rememberedConfig;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $this->_block = Mage::app()->getLayout()->createBlock('Enterprise_Invitation_Block_Form');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_rememberedConfig = null;
    }

    /**
     * @param int $num
     * @param int $expected
     *
     * @dataProvider getMaxInvitationsPerSendDataProvider
     */
    public function testGetMaxInvitationsPerSend($num, $expected)
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->_changeConfig(Enterprise_Invitation_Model_Config::XML_PATH_MAX_INVITATION_AMOUNT_PER_SEND, $num);
        try {
            $this->assertEquals($expected, $this->_block->getMaxInvitationsPerSend());
        } catch (Exception $e) {
            $this->_restoreConfig();
            throw $e;
        }
        $this->_restoreConfig();
    }

    /**
     * @return array
     */
    public function getMaxInvitationsPerSendDataProvider()
    {
        return array(
            array(1, 1),
            array(3, 3),
            array(100, 100),
            array(0, 1)
        );
    }

    /**
     * Sets new value to store config path, remembers old value
     *
     * @param  $path
     * @param  $value
     * @return Enterprise_Invitation_Block_FormTest
     */
    protected function _changeConfig($path, $value)
    {
        $store = Mage::app()->getStore();
        $oldValue = $store->getConfig($path);
        $store->setConfig($path, $value);

        if (!$this->_rememberedConfig) {
            $this->_rememberedConfig = array(
                'path' => $path,
                'old_value' => $oldValue
            );
        }
        return $this;
    }

    /**
     * Restores previously remembered store config value
     *
     * @return Enterprise_Invitation_Block_FormTest
     */
    protected function _restoreConfig()
    {
        Mage::app()->getStore()
            ->setConfig($this->_rememberedConfig['path'], $this->_rememberedConfig['old_value']);
        $this->_rememberedConfig = null;
        return $this;
    }

    public function testIsInvitationMessageAllowed()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        try {
            $this->_changeConfig(Enterprise_Invitation_Model_Config::XML_PATH_USE_INVITATION_MESSAGE, 1);
            $this->assertEquals(true, $this->_block->isInvitationMessageAllowed());

            $this->_changeConfig(Enterprise_Invitation_Model_Config::XML_PATH_USE_INVITATION_MESSAGE, 0);
            $this->assertEquals(false, $this->_block->isInvitationMessageAllowed());

        } catch (Exception $e) {
            $this->_restoreConfig();
            throw $e;
        }
        $this->_restoreConfig();
    }
}
