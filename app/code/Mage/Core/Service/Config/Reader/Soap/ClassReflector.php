<?php
/**
 * SOAP API specific class reflector.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Config_Reader_Soap_ClassReflector
    extends Mage_Core_Service_Config_Reader_ClassReflectorAbstract
{
    /**
     * Set types data into reader after reflecting all files.
     *
     * @return array
     */
    public function getPostReflectionData()
    {
        return array(
            'types' => $this->_typeProcessor->getTypesData(),
            'type_to_class_map' => $this->_typeProcessor->getTypeToClassMap(),
        );
    }
}
