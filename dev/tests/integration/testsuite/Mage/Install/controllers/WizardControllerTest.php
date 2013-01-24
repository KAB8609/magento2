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

class Mage_Install_WizardControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var string
     */
    protected static $_mediaDir;

    /**
     * @var string
     */
    protected static $_themeDir;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$_mediaDir = Mage::getBaseDir(Mage_Core_Model_Dir::MEDIA);
        self::$_themeDir = self::$_mediaDir . DIRECTORY_SEPARATOR . 'theme';
    }

    public function setUp()
    {
        // emulate non-installed application
        Magento_Test_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        ));
        parent::setUp();
    }

    public function tearDown()
    {
        if (is_dir(self::$_mediaDir)) {
            chmod(self::$_mediaDir, 0777);
        }
        if (is_dir(self::$_themeDir)) {
            chmod(self::$_themeDir, 0777);
        }
        parent::tearDown();
    }

    public function testPreDispatch()
    {
        $this->dispatch('install/wizard');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }

    public function testPreDispatchNonWritableMedia()
    {
        $this->_testInstallProhibitedWhenNonWritable(self::$_mediaDir);
    }

    public function testPreDispatchNonWritableTheme()
    {
        $this->_testInstallProhibitedWhenNonWritable(self::$_themeDir);
    }

    /**
     * Tests that when $nonWritableDir folder is read-only, the installation controller prohibits continuing
     * installation and points to fix issue with theme directory.
     *
     * @param string $nonWritableDir
     */
    protected function _testInstallProhibitedWhenNonWritable($nonWritableDir)
    {
        if (file_exists($nonWritableDir) && !is_dir($nonWritableDir)) {
            $this->markTestSkipped("Incorrect file structure. $nonWritableDir should be a directory");
        }

        if (is_dir($nonWritableDir)) {
            chmod($nonWritableDir, 0444);
        } else {
            mkdir($nonWritableDir, 0444);
        }

        if (is_writable($nonWritableDir)) {
            $this->markTestSkipped("Current OS doesn't support setting write-access for folders via mode flags");
        }

        $this->dispatch('install/wizard');

        $this->assertEquals(503, $this->getResponse()->getHttpResponseCode());
        $this->assertContains(self::$_themeDir, $this->getResponse()->getBody());
    }
}
