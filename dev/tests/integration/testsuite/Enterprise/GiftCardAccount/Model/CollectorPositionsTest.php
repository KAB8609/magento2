<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test positions of the GiftCardAccount total collectors as compared to other collectors
 */
class Enterprise_GiftCardAccount_Model_CollectorPositionsTest extends Mage_Sales_Model_CollectorPositionsTestAbstract
{
    /**
     * @return array
     */
    public function collectorPositionDataProvider()
    {
        return array(
            'quote collectors' => array(
                'giftcardaccount',
                'quote',
                array('customerbalance'),
                array('weee', 'discount', 'tax', 'tax_subtotal', 'grand_total'),
            ),
            'invoice collectors' => array(
                'giftcardaccount',
                'invoice',
                array('customerbalance'),
                array('discount', 'tax', 'grand_total', 'giftwrapping'),
            ),
            'creditmemo collectors' => array(
                'giftcardaccount',
                'creditmemo',
                array(),
                array('weee', 'discount', 'tax', 'grand_total', 'giftwrapping', 'customerbalance'),
            ),
        );
    }
}
