<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax infoirmation for order shipping
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model constructor. Initialize connection to database and associated table.
     */
    protected function _construct()
    {
        $this->_init('saas_printed_template_order_shipping_tax', 'shipping_tax_id');
    }
}
