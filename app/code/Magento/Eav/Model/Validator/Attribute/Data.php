<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV attribute data validator
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Validator\Attribute;

class Data extends \Magento\Validator\ValidatorAbstract
{
    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var array
     */
    protected $_attributesWhiteList = array();

    /**
     * @var array
     */
    protected $_attributesBlackList = array();

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var \Magento\Eav\Model\Attribute\Data
     */
    protected $_dataModelFactory;

    /**
     * Set list of attributes for validation in isValid method.
     *
     * @param \Magento\Eav\Model\Attribute[] $attributes
     * @return \Magento\Eav\Model\Validator\Attribute\Data
     */
    public function setAttributes(array $attributes)
    {
        $this->_attributes = $attributes;
        return $this;
    }

    /**
     * Set codes of attributes that should be filtered in validation process.
     *
     * All attributes not in this list 't be involved in validation.
     *
     * @param array $attributesCodes
     * @return \Magento\Eav\Model\Validator\Attribute\Data
     */
    public function setAttributesWhiteList(array $attributesCodes)
    {
        $this->_attributesWhiteList = $attributesCodes;
        return $this;
    }

    /**
     * Set codes of attributes that should be excluded in validation process.
     *
     * All attributes in this list won't be involved in validation.
     *
     * @param array $attributesCodes
     * @return \Magento\Eav\Model\Validator\Attribute\Data
     */
    public function setAttributesBlackList(array $attributesCodes)
    {
        $this->_attributesBlackList = $attributesCodes;
        return $this;
    }

    /**
     * Set data for validation in isValid method.
     *
     * @param array $data
     * @return \Magento\Eav\Model\Validator\Attribute\Data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Validate EAV model attributes with data models
     *
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return bool
     */
    public function isValid($entity)
    {
        /** @var $attributes \Magento\Eav\Model\Attribute[] */
        $attributes = $this->_getAttributes($entity);

        $data = array();
        if ($this->_data) {
            $data = $this->_data;
        } elseif ($entity instanceof \Magento\Object) {
            $data = $entity->getData();
        }

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if (!$attribute->getDataModel() && !$attribute->getFrontendInput()) {
                continue;
            }
            $dataModel = $this->getAttributeDataModelFactory()->factory($attribute, $entity);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = null;
            }
            $result = $dataModel->validateValue($data[$attributeCode]);
            if (true !== $result) {
                $this->_addErrorMessages($attributeCode, (array)$result);
            }
        }
        return count($this->_messages) == 0;
    }

    /**
     * Get attributes involved in validation.
     *
     * This method return specified $_attributes if they defined by setAttributes method, otherwise if $entity
     * is EAV-model it returns it's all available attributes, otherwise it return empty array.
     *
     * @param mixed $entity
     * @return array
     */
    protected function _getAttributes($entity)
    {
        /** @var \Magento\Customer\Model\Attribute[] $attributes */
        $attributes = array();

        if ($this->_attributes) {
            $attributes = $this->_attributes;
        } elseif ($entity instanceof \Magento\Core\Model\AbstractModel
                  && $entity->getResource() instanceof \Magento\Eav\Model\Entity\AbstractEntity
        ) { // $entity is EAV-model
            $attributes = $entity->getEntityType()->getAttributeCollection()->getItems();
        }

        $attributesByCode = array();
        $attributesCodes = array();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributesByCode[$attributeCode] = $attribute;
            $attributesCodes[] = $attributeCode;
        }

        $ignoreAttributes = $this->_attributesBlackList;
        if ($this->_attributesWhiteList) {
            $ignoreAttributes = array_merge(
                $ignoreAttributes,
                array_diff($attributesCodes, $this->_attributesWhiteList)
            );
        }

        foreach ($ignoreAttributes as $attributeCode) {
            unset($attributesByCode[$attributeCode]);
        }

        return $attributesByCode;
    }

    /**
     * Get factory object for creating Attribute Data Model
     *
     * @return \Magento\Eav\Model\Attribute\Data
     */
    public function getAttributeDataModelFactory()
    {
        if (!$this->_dataModelFactory) {
            $this->_dataModelFactory = new \Magento\Eav\Model\Attribute\Data;
        }
        return $this->_dataModelFactory;
    }

    /**
     * Set factory object for creating Attribute Data Model
     *
     * @param \Magento\Eav\Model\Attribute\Data $factory
     * @return \Magento\Eav\Model\Validator\Attribute\Data
     */
    public function setAttributeDataModelFactory($factory)
    {
        $this->_dataModelFactory = $factory;
        return $this;
    }

    /**
     * Add error messages
     *
     * @param string $code
     * @param array $messages
     */
    protected function _addErrorMessages($code, array $messages)
    {
        if (!array_key_exists($code, $this->_messages)) {
            $this->_messages[$code] = $messages;
        } else {
            $this->_messages[$code] = array_merge($this->_messages[$code], $messages);
        }
    }
}
