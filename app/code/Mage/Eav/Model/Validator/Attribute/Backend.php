<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validation EAV entity via EAV attributes' backend models
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Validator_Attribute_Backend extends Magento_Validator_ValidatorAbstract
{
    /**
     * Returns true if and only if $value meets the validation requirements.
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function isValid($entity)
    {
        $this->_messages = array();
        if (!($entity instanceof Mage_Core_Model_Abstract)) {
            throw new InvalidArgumentException('Model must be extended from Mage_Core_Model_Abstract');
        }
        /** @var Mage_Eav_Model_Entity_Abstract $resource */
        $resource = $entity->getResource();
        if (!($resource instanceof Mage_Eav_Model_Entity_Abstract)) {
            throw new InvalidArgumentException('Model resource must be extended from Mage_Eav_Model_Entity_Abstract');
        }
        $resource->loadAllAttributes($entity);
        $attributes = $resource->getAttributesByCode();
        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $backend = $attribute->getBackend();
            if (!method_exists($backend, 'validate')) {
                continue;
            }
            try {
                $result = $backend->validate($entity);
                if (false === $result) {
                    $this->_messages[$attribute->getAttributeCode()][] =
                        __('The value of attribute "%1" is invalid',
                            $attribute->getAttributeCode());
                } elseif (is_string($result)) {
                    $this->_messages[$attribute->getAttributeCode()][] = $result;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_messages[$attribute->getAttributeCode()][] = $e->getMessage();
            }
        }
        return 0 == count($this->_messages);
    }
}
