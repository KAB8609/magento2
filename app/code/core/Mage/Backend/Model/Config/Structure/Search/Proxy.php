<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Search_Proxy implements Mage_Backend_Model_Config_Structure_SearchInterface
{
    /**
     * Object manager
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Backend_Model_Config_Structure
     */
    protected $_subject;

    /**
     * @param Magento_ObjectManager $objectManger
     */
    public function __construct(Magento_ObjectManager $objectManger)
    {
        $this->_objectManager = $objectManger;
    }

    /**
     * Retrieve subject
     *
     * @return Mage_Backend_Model_Config_Structure_SearchInterface
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get('Mage_Backend_Model_Config_Structure_SearchInterface');
        }
        return $this->_subject;
    }

    /**
     * Find element by path
     *
     * @param string $path
     * @return Mage_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElement($path)
    {
        return $this->_getSubject()->getElement($path);
    }
}
