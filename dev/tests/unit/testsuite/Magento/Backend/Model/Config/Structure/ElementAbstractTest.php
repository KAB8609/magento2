<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_ElementAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_ElementAbstract
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    protected function setUp()
    {
        $this->_applicationMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);

        $this->_model = $this->getMockForAbstractClass(
            'Magento_Backend_Model_Config_Structure_ElementAbstract',
            array($this->_applicationMock)
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_applicationMock);
    }

    public function testGetId()
    {
        $this->assertEquals('', $this->_model->getId());
        $this->_model->setData(array('id' => 'someId'), 'someScope');
        $this->assertEquals('someId', $this->_model->getId());
    }

    public function testGetLabelTranslatesLabel()
    {
        $this->assertEquals('', $this->_model->getLabel());
        $this->_model->setData(array('label' => 'some_label'), 'someScope');
        $this->assertEquals(__('some_label'), $this->_model->getLabel());
    }

    public function testGetCommentTranslatesComment()
    {
        $this->assertEquals('', $this->_model->getComment());
        $this->_model->setData(array('comment' => 'some_comment'), 'someScope');
        $this->assertEquals(__('some_comment'), $this->_model->getComment());
    }

    public function testGetFrontEndModel()
    {
        $this->_model->setData(array('frontend_model' => 'frontend_model_name'), 'store');
        $this->assertEquals('frontend_model_name', $this->_model->getFrontendModel());
    }

    public function testGetAttribute()
    {
        $this->_model->setData(array(
            'id' => 'elementId',
            'label' => 'Element Label',
            'customAttribute' => 'Custom attribute value'
        ), 'someScope');
        $this->assertEquals('elementId', $this->_model->getAttribute('id'));
        $this->assertEquals('Element Label', $this->_model->getAttribute('label'));
        $this->assertEquals('Custom attribute value', $this->_model->getAttribute('customAttribute'));
        $this->assertNull($this->_model->getAttribute('nonexistingAttribute'));
    }


    public function testIsVisibleReturnsTrueInSingleStoreModeForNonHiddenElements()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(array('showInDefault' => 1, 'showInStore' => 0, 'showInWebsite' => 0),
            Magento_Backend_Model_Config_ScopeDefiner::SCOPE_DEFAULT);
        $this->assertTrue($this->_model->isVisible());
    }

    public function testIsVisibleReturnsFalseInSingleStoreModeForHiddenElements()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(
            array('hide_in_single_store_mode' => 1, 'showInDefault' => 1, 'showInStore' => 0, 'showInWebsite' => 0),
            Magento_Backend_Model_Config_ScopeDefiner::SCOPE_DEFAULT
        );
        $this->assertFalse($this->_model->isVisible());
    }

    /**
     * Invisible elements is contains showInDefault="0" showInWebsite="0" showInStore="0"
     */
    public function testIsVisibleReturnsFalseInSingleStoreModeForInvisibleElements()
    {
        $this->_applicationMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->_model->setData(array('showInDefault' => 0, 'showInStore' => 0, 'showInWebsite' => 0),
            Magento_Backend_Model_Config_ScopeDefiner::SCOPE_DEFAULT
        );
        $this->assertFalse($this->_model->isVisible());
    }

    /**
     * @param array $settings
     * @param string $scope
     * @dataProvider isVisibleReturnsTrueForProperScopesDataProvider
     */
    public function testIsVisibleReturnsTrueForProperScopes($settings, $scope)
    {
        $this->_model->setData($settings, $scope);
        $this->assertTrue($this->_model->isVisible());
    }

    public function isVisibleReturnsTrueForProperScopesDataProvider()
    {
        return array(
            array(
                array('showInDefault' => 1, 'showInStore' => 0, 'showInWebsite' => 0),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_DEFAULT
            ),
            array(
                array('showInDefault' => 0, 'showInStore' => 1, 'showInWebsite' => 0),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_STORE
            ),
            array(
                array('showInDefault' => 0, 'showInStore' => 0, 'showInWebsite' => 1),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_WEBSITE
            ),
        );
    }

    /**
     * @param array $settings
     * @param string $scope
     * @dataProvider isVisibleReturnsFalseForNonProperScopesDataProvider
     */
    public function testIsVisibleReturnsFalseForNonProperScopes($settings, $scope)
    {
        $this->_model->setData($settings, $scope);
        $this->assertFalse($this->_model->isVisible());
    }

    public function isVisibleReturnsFalseForNonProperScopesDataProvider()
    {
        return array(
            array(
                array('showInDefault' => 0, 'showInStore' => 1, 'showInWebsite' => 1),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_DEFAULT
            ),
            array(
                array('showInDefault' => 1, 'showInStore' => 0, 'showInWebsite' => 1),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_STORE
            ),
            array(
                array('showInDefault' => 1, 'showInStore' => 1, 'showInWebsite' => 0),
                Magento_Backend_Model_Config_ScopeDefiner::SCOPE_WEBSITE
            ),
        );
    }

    public function testGetClass()
    {
        $this->assertEquals('', $this->_model->getClass());
        $this->_model->setData(array('class' => 'some_class'), 'store');
        $this->assertEquals('some_class', $this->_model->getClass());
    }

    public function testGetPathBuildsFullPath()
    {
        $this->_model->setData(array('path' => 'section/group', 'id' => 'fieldId'), 'scope');
        $this->assertEquals('section/group/prefix_fieldId', $this->_model->getPath('prefix_'));
    }
}