<?php
/**
 * Google Optimizer Scripts Helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Helper_Code
{
    /**
     * @var Mage_GoogleOptimizer_Model_Code
     */
    protected $_codeModel;

    /**
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * @param Mage_GoogleOptimizer_Model_Code $code
     */
    public function __construct(Mage_GoogleOptimizer_Model_Code $code)
    {
        $this->_codeModel = $code;
    }

    /**
     * Get loaded Code object by Entity
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function getCodeObjectByEntity(Mage_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;

        $this->_checkEntityIsEmpty();
        if ($entity instanceof Mage_Cms_Model_Page) {
            $this->_codeModel->loadByEntityIdAndType($entity->getId(), $this->_getEntityType());
        } else {
            $this->_codeModel->loadByEntityIdAndType($entity->getId(), $this->_getEntityType(), $entity->getStoreId());
        }

        return $this->_codeModel;
    }

    /**
     * Get Entity Type by Entity object
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function _getEntityType()
    {
        $type = $this->_getTypeString();

        if (empty($type)) {
            throw new InvalidArgumentException('The model class is not valid');
        }

        return $type;
    }

    /**
     * Get Entity Type string
     *
     * @return string
     */
    protected function _getTypeString()
    {
        $type = '';
        if ($this->_entity instanceof Mage_Catalog_Model_Category) {
            $type = Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY;
        }

        if ($this->_entity instanceof Mage_Catalog_Model_Product) {
            $type = Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT;
        }

        if ($this->_entity instanceof Mage_Cms_Model_Page) {
            $type = Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE;
        }
        return $type;
    }

    /**
     * Check if Entity is Empty
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    protected function _checkEntityIsEmpty()
    {
        if (!$this->_entity->getId()) {
            throw new InvalidArgumentException('The model is empty');
        }
        return $this;
    }
}