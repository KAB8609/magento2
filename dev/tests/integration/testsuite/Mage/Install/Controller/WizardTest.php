<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_Controller_WizardTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var string
     */
    protected static $_tmpDir;

    /**
     * @var array
     */
    protected static $_params = array();

    public static function setUpBeforeClass()
    {
        $tmpDir = Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir() . DIRECTORY_SEPARATOR . __CLASS__;
        if (is_file($tmpDir)) {
            unlink($tmpDir);
        } elseif (is_dir($tmpDir)) {
            Magento_Io_File::rmdirRecursive($tmpDir);
        }
        // deliberately create a file instead of directory to emulate broken access to static directory
        touch($tmpDir);
        self::$_tmpDir = $tmpDir;

        // emulate invalid installation date, so that application will think it is not installed
        self::$_params = array(Mage::PARAM_CUSTOM_LOCAL_CONFIG
            => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        );
    }

    public function testPreDispatch()
    {
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize(self::$_params);
        Magento_Test_Helper_Bootstrap::getObjectManager()->configure(array(
            'preferences' => array(
                'Mage_Core_Controller_Request_Http' => 'Magento_Test_Request',
                'Mage_Core_Controller_Response_Http' => 'Magento_Test_Response'
            )
        ));
        $this->dispatch('install/wizard');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * @param string $action
     * @dataProvider actionsDataProvider
     * @expectedException Magento_BootstrapException
     */
    public function testPreDispatchImpossibleToRenderPage($action)
    {
        $params = self::$_params;
        $params[Mage::PARAM_APP_DIRS][Mage_Core_Model_Dir::STATIC_VIEW] = self::$_tmpDir;
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize($params);
        Magento_Test_Helper_Bootstrap::getObjectManager()->configure(array(
            'preferences' => array(
                'Mage_Core_Controller_Request_Http' => 'Magento_Test_Request',
                'Mage_Core_Controller_Response_Http' => 'Magento_Test_Response'
            )
        ));
        $this->dispatch("install/wizard/{$action}");
    }

    /**
     * @return array
     */
    public function actionsDataProvider()
    {
        return array(
            array('index'),
            array('begin'),
            array('beginPost'),
            array('locale'),
            array('localeChange'),
            array('localePost'),
            array('download'),
            array('downloadPost'),
            array('downloadAuto'),
            array('install'),
            array('downloadManual'),
            array('config'),
            array('configPost'),
            array('installDb'),
            array('administrator'),
            array('administratorPost'),
            array('end'),
        );
    }
}
