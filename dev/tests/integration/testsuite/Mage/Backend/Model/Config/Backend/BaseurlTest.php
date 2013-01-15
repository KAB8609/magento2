<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Backend_BaseurlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $path
     * @param string $value
     * @magentoDbIsolation enabled
     * @dataProvider validationDataProvider
     */
    public function testValidation($path, $value)
    {
        /** @var $model Mage_Backend_Model_Config_Backend_Baseurl */
        $model = Mage::getModel('Mage_Backend_Model_Config_Backend_Baseurl');
        $model->setPath($path)->setValue($value)->save();
        $this->assertNotEmpty((int)$model->getId());
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        $basePlaceholder = '{{base_url}}';
        $unsecurePlaceholder = '{{unsecure_base_url}}';
        $unsecureSuffix = '{{unsecure_base_url}}test/';
        $securePlaceholder = '{{secure_base_url}}';
        $secureSuffix = '{{secure_base_url}}test/';

        return array(
            // any fully qualified URLs regardless of path
            array('', 'http://example.com/'),
            array('', 'http://example.com/uri/'),

            // unsecure base URLs
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $basePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL, $unsecureSuffix),

            // secure base URLs
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $basePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $securePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $secureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, $securePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, $secureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, $securePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, $secureSuffix),

            // secure base URLs - in addition can use unsecure
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $unsecureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, $unsecureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, $unsecurePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, $unsecureSuffix),
        );
    }

    /**
     * @param string $path
     * @param string $value
     * @magentoDbIsolation enabled
     * @expectedException Mage_Core_Exception
     * @dataProvider validationExceptionDataProvider
     */
    public function testValidationException($path, $value)
    {
        /** @var $model Mage_Backend_Model_Config_Backend_Baseurl */
        $model = Mage::getModel('Mage_Backend_Model_Config_Backend_Baseurl');
        $model->setPath($path)->setValue($value)->save();
    }

    /**
     * @return array
     */
    public function validationExceptionDataProvider()
    {
        $baseSuffix = '{{base_url}}test/';
        $unsecurePlaceholder = '{{unsecure_base_url}}';
        $unsecureSuffix = '{{unsecure_base_url}}test/';
        $unsecureWrongSuffix = '{{unsecure_base_url}}test';
        $securePlaceholder = '{{secure_base_url}}';
        $secureSuffix = '{{secure_base_url}}test/';
        $secureWrongSuffix = '{{secure_base_url}}test';

        return array(
            // not a fully qualified URLs regardless path
            array('', 'not a valid URL'),
            array('', 'example.com'),
            array('', 'http://example.com'),
            array('', 'http://example.com/uri'),

            // unsecure base URLs
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, ''), // breaks cache
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $baseSuffix), // creates redirect loops
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $unsecureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, $unsecurePlaceholder), // creates endless recursion
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $baseSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $unsecureWrongSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL, $unsecureWrongSuffix),
            array(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LIB_URL, $unsecureWrongSuffix),

            // secure base URLs
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $baseSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $secureSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL, $securePlaceholder),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, ''),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $baseSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL, $secureWrongSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_MEDIA_URL, $secureWrongSuffix),
            array(Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LIB_URL, $secureWrongSuffix),
        );
    }
}
