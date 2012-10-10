<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Source_DesignTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Source_Design
     */
    protected $_model = null;

    public static function setUpBeforeClass()
    {
        Mage::getConfig()->getOptions()->setDesignDir(__DIR__ . '/_files/design');
    }

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->_model = Mage::getModel('Mage_Core_Model_Design_Source_Design');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testGetAllOptionsSorting()
    {
        $fixture = array(
            array(
                'label' => 'Default / Default',
                'value' => array(
                    array(
                        'label' => 'default (incompatible version)',
                        'value' => 'default/default/default',
                    ),
                ),
            ),
            array(
                'label' => 'Default / Theme G',
                'value' => array(
                    array(
                        'label' => 'default (incompatible version)',
                        'value' => 'default/g/default',
                    ),
                ),
            ),
            array(
                'label' => 'Package A / Theme D',
                'value' => array(
                    array(
                        'label' => 'y (incompatible version)',
                        'value' => 'a/d/y',
                    ),
                ),
            ),
            array(
                'label' => 'Package B / Theme E',
                'value' => array(
                    array(
                        'label' => 'x (incompatible version)',
                        'value' => 'b/e/x',
                    ),
                ),
            ),
        );
        $this->assertSame($fixture, $this->_model->getAllOptions(false));
    }

    public function testGetThemeOptionsSorting()
    {
        $fixture = array(
            array(
                'label' => 'Default',
                'value' => array(
                    array(
                        'label' => 'Default (incompatible version)',
                        'value' => 'default/default',
                    ),
                    array(
                        'label' => 'Theme G (incompatible version)',
                        'value' => 'default/g',
                    ),
                ),
            ),
            array(
                'label' => 'Package A',
                'value' => array(
                    array(
                        'label' => 'Theme D (incompatible version)',
                        'value' => 'a/d',
                    ),
                ),
            ),
            array(
                'label' => 'Package B',
                'value' => array(
                    array(
                        'label' => 'Theme E (incompatible version)',
                        'value' => 'b/e',
                    ),
                ),
            ),
        );
        $this->assertSame($fixture, $this->_model->getThemeOptions());
    }

    public function testGetOptions()
    {
        $this->assertSame($this->_model->getAllOptions(false), $this->_model->getOptions());
    }
}
