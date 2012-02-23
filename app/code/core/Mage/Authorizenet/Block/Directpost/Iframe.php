<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost iframe block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Iframe extends Mage_Core_Block_Template
{
    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $params = Mage::registry(Mage_Authorizenet_Directpost_PaymentController::REGISTRY_FORM_PARAMS_KEY);
        if (is_null($params)) {
            $params = array();
        }
        $this->setParams($params);
        return parent::_prepareLayout();
    }
}
