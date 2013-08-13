<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Transaction Events Types Options
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Options_TransactionEvents
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Paypal_Model_Report_Settlement_Row
     */
    protected $_model;

    /**
     * @param Mage_Paypal_Model_Report_Settlement_Row $model
     */
    public function __construct(Mage_Paypal_Model_Report_Settlement_Row $model)
    {
        $this->_model = $model;
    }

    /**
     *  Get full list of codes with their description
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->getTransactionEvents();
    }
}
