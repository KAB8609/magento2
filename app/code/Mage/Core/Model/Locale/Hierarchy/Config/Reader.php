<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Locale_Hierarchy_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/locale' => 'code',
    );

    /**
     * @param Mage_Core_Model_Locale_Hierarchy_Config_FileResolver $fileResolver
     * @param Mage_Core_Model_Locale_Hierarchy_Config_Converter $converter
     * @param Mage_Core_Model_Locale_Hierarchy_Config_SchemaLocator $schemeLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Mage_Core_Model_Locale_Hierarchy_Config_FileResolver $fileResolver,
        Mage_Core_Model_Locale_Hierarchy_Config_Converter $converter,
        Mage_Core_Model_Locale_Hierarchy_Config_SchemaLocator $schemeLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'config.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemeLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
