<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory that produces layout file instances
 */
class Mage_Core_Model_Layout_File_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of a layout file
     *
     * @param string $filename
     * @param string $module
     * @param Mage_Core_Model_ThemeInterface $theme
     * @return Mage_Core_Model_Layout_File
     */
    public function create($filename, $module, Mage_Core_Model_ThemeInterface $theme = null)
    {
        return $this->_objectManager->create(
            'Mage_Core_Model_Layout_File',
            array('filename' => $filename, 'module' => $module, 'theme' => $theme)
        );
    }
}
