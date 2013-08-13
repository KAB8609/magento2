<?php
/**
 * URL Generator for Customer Online Grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Log_Model_Resource_Visitor_Online_Grid_Row_UrlGenerator
    extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(Magento_AuthorizationInterface $authorization, array $args = array())
    {
        $this->_authorization = $authorization;
        parent::__construct($args);
    }

    /**
     * Create url for passed item using passed url model
     * @param Magento_Object $item
     * @return string
     */
    public function getUrl($item)
    {
        if ($this->_authorization->isAllowed('Mage_Customer::manage') && $item->getCustomerId()) {
            return parent::getUrl($item);
        }
        return false;
    }
}
