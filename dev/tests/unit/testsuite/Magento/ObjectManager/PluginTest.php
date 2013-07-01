<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once __DIR__ . '/../_files/Interface.php';
require_once __DIR__ . '/../_files/Parent.php';
require_once __DIR__ . '/../_files/Child.php';
require_once __DIR__ . '/../_files/Child/A.php';
require_once __DIR__ . '/../_files/Child/Circular.php';
require_once __DIR__ . '/../_files/Child/Interceptor/A.php';
require_once __DIR__ . '/../_files/Child/Interceptor/B.php';
require_once __DIR__ . '/../_files/Aggregate/Interface.php';
require_once __DIR__ . '/../_files/Aggregate/Parent.php';
require_once __DIR__ . '/../_files/Aggregate/Child.php';
require_once __DIR__ . '/../_files/Aggregate/WithOptional.php';
require_once __DIR__ . '/../_files/Child/Interceptor.php';
require_once __DIR__ . '/../_files/Child/Interceptor/A.php';
require_once __DIR__ . '/../_files/Child/Interceptor/B.php';

class Magento_ObjectManager_PluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_locator;

    protected function setUp()
    {
        $this->_locator = new Magento_ObjectManager_ObjectManager(new Magento_ObjectManager_Definition_Runtime());
    }

    public function testPluginsAreCalled()
    {
        $this->_locator->configure(
            array('Magento_Test_Di_Child' => array(
                'plugins' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                )
            ))
        );

        $child = $this->_locator->create('Magento_Test_Di_Child');
        $this->assertEquals('_A__B_|BAtestStringAB|_B__A_', $child->wrap('testString'));
    }

    public function testPluginsAreOrdered()
    {
        $this->_locator->configure(
            array('Magento_Test_Di_Child' => array(
                'plugins' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B', 'sortOrder' => '0')
                )
            ))
        );

        $child = $this->_locator->create('Magento_Test_Di_Child');
        $this->assertEquals('_B__A_|ABtestStringBA|_A__B_', $child->wrap('testString'));
    }

    public function testPluginsAreAddedToInstances()
    {
        $this->_locator->configure(
            array('customChild' => array(
                'type' => 'Magento_Test_Di_Child',
                'plugins' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                ),
                'parameters' => array(
                    'wrapperSymbol' => '/'
                )
            ))
        );

        $child = $this->_locator->create('customChild');
        $this->assertEquals('_A__B_/BAtestStringAB/_B__A_', $child->wrap('testString'));
    }

    public function testInstanceIsUsedAsPlugin()
    {
        $this->_locator->configure(
            array(
                'Magento_Test_Di_Child' => array(
                    'plugins' => array(
                        'first' => array('instance' => 'customAInterceptor'),
                        'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                    )
                ),
                'customAInterceptor' => array(
                    'type' => 'Magento_Test_Di_Child_Interceptor_A',
                    'parameters' => array(
                        'wrapperSym' => 'AAA'
                    )
                )
            )
        );

        $child = $this->_locator->create('Magento_Test_Di_Child');
        $this->assertEquals('_AAA__B_|BAAAtestStringAAAB|_B__AAA_', $child->wrap('testString'));
    }

    public function testCreateReturnsNewInterceptorEveryTime()
    {
        $this->_locator->configure(array(
            'Magento_Test_Di_Child' => array(
                'plugins' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                )
            )
        ));

        $childOne = $this->_locator->create('Magento_Test_Di_Child');
        $childTwo = $this->_locator->create('Magento_Test_Di_Child');
        $this->assertEquals($childOne->wrap('testString'), $childTwo->wrap('testString'));
        $this->assertNotSame($childOne, $childTwo);
    }

    public function testGetReturnsSameInterceptorEveryTime()
    {
        $this->_locator->configure(array(
            'Magento_Test_Di_Child' => array(
                'plugins' => array(
                    'first' => array('instance' => 'Magento_Test_Di_Child_Interceptor_A'),
                    'second' => array('instance' => 'Magento_Test_Di_Child_Interceptor_B')
                )
            )
        ));

        $childOne = $this->_locator->get('Magento_Test_Di_Child');
        $childTwo = $this->_locator->get('Magento_Test_Di_Child');
        $this->assertEquals($childOne->wrap('testString'), $childTwo->wrap('testString'));
        $this->assertSame($childOne, $childTwo);
    }
}
