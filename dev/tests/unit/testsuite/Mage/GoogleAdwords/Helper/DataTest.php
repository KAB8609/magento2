<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_GoogleAdwords_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array('asArray', 'getNode'), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_ConfigInterface', array(), array(), '', false);
        $this->_registryMock = $this->getMock('Mage_Core_Model_Registry', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManager->getObject('Mage_GoogleAdwords_Helper_Data', array(
            'config' => $this->_configMock,
            'storeConfig' => $this->_storeConfigMock,
            'registry' => $this->_registryMock
        ));
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsActive()
    {
        return array(
            array(true, 1234, true),
            array(true, 'conversionId', false),
            array(true, '', false),
            array(false, '', false),
        );
    }

    /**
     * @param bool $isActive
     * @param string $returnConfigValue
     * @param bool $returnValue
     * @dataProvider dataProviderForTestIsActive
     */
    public function testIsGoogleAdwordsActive($isActive, $returnConfigValue, $returnValue)
    {
        $this->_storeConfigMock->expects($this->any())->method('getConfigFlag')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_ACTIVE)
            ->will($this->returnValue($isActive));
        $this->_storeConfigMock->expects($this->any())->method('getConfig')
            ->with($this->isType('string'))
            ->will($this->returnCallback(function () use ($returnConfigValue) {
                return $returnConfigValue;
            }));

        $this->assertEquals($returnValue, $this->_helper->isGoogleAdwordsActive());
    }

    public function testGetLanguageCodes()
    {
        $languages = array('en', 'ru', 'uk');
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_LANGUAGES)
            ->will($this->returnSelf());
        $this->_configMock->expects($this->once())->method('asArray')
            ->will($this->returnValue($languages));

        $this->assertEquals($languages, $this->_helper->getLanguageCodes());
    }

    public function testGetConversionImgSrc()
    {
        $conversionId = 123;
        $label = 'LabEl';
        $imgSrc = sprintf('https://www.googleadservices.com/pagead/conversion/%s/?label=%s&amp;guid=ON&amp;script=0',
            $conversionId, $label);
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_IMG_SRC)->will($this->returnValue($imgSrc));
        $this->assertEquals($imgSrc, $this->_helper->getConversionImgSrc());
    }

    /**
     * @return array
     */
    public function dataProviderForTestStoreConfig()
    {
        return array(
            array('getConversionId', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_ID, 123),
            array('getConversionLanguage', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_LANGUAGE, 'en'),
            array('getConversionFormat', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_FORMAT, '2'),
            array('getConversionColor', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_COLOR, 'ffffff'),
            array('getConversionLabel', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_LABEL, 'Label'),
            array('getConversionValueType', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE, '1'),
            array('getConversionValueConstant', Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE, '0'),
        );
    }

    /**
     * @param string $method
     * @param string $xmlPath
     * @param string $returnValue
     * @dataProvider dataProviderForTestStoreConfig
     */
    public function testGetStoreConfigValue($method, $xmlPath, $returnValue)
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')->with($xmlPath)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->_helper->$method());
    }

    public function testGetConversionValueDynamic()
    {
        $returnValue = 4.1;
        $this->_storeConfigMock->expects($this->any())->method('getConfig')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE)
            ->will($this->returnValue(Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_DYNAMIC));
        $this->_registryMock->expects($this->once())->method('registry')
            ->with(Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_REGISTRY_NAME)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->_helper->getConversionValue());
    }

    /**
     * @return array
     */
    public function dataProviderForTestConversionValueConstant()
    {
        return array(
            array(1.4, 1.4),
            array('', Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_DEFAULT),
        );
    }

    /**
     * @param string $conversionValueConst
     * @param string $returnValue
     * @dataProvider dataProviderForTestConversionValueConstant
     */
    public function testGetConversionValueConstant($conversionValueConst, $returnValue)
    {
        $this->_storeConfigMock->expects($this->at(0))->method('getConfig')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE_TYPE)
            ->will($this->returnValue(Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_CONSTANT));
        $this->_registryMock->expects($this->never())->method('registry');
        $this->_storeConfigMock->expects($this->at(1))->method('getConfig')
            ->with(Mage_GoogleAdwords_Helper_Data::XML_PATH_CONVERSION_VALUE)
            ->will($this->returnValue($conversionValueConst));

        $this->assertEquals($returnValue, $this->_helper->getConversionValue());
    }
}
