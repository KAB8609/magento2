<?php
/**
 * Coupon API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Checkout/_files/quote_with_simple_product.php
 * @magentoDataFixture Mage/Checkout/_files/discount_10percent.php
 */
class Mage_Checkout_Model_Cart_Coupon_ApiTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Catalog_Model_Product */
    protected $_product;
    /** @var Mage_Sales_Model_Quote */
    protected $_quote;
    /** @var Mage_SalesRule_Model_Rule */
    protected $_salesRule;

    /**
     * We can't put this code inside setUp() as it will be called before fixtures execution
     */
    protected function _init()
    {
        $this->_product = Mage::getModel('Mage_Catalog_Model_Product')->load(1);
        $this->_quote = Mage::getModel('Mage_Sales_Model_Resource_Quote_Collection')->getFirstItem();
        $this->_salesRule = Mage::getModel('Mage_SalesRule_Model_Rule')->load('Test Coupon', 'name');
    }

    /**
     * Test coupon code applying.
     */
    public function testCartCouponAdd()
    {
        $this->_init();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartCouponAdd',
            array(
                'quoteId' => $this->_quote->getId(),
                'couponCode' => $this->_salesRule->getCouponCode()
            )
        );

        $this->assertTrue($soapResult, 'Coupon code was not applied');
        /** @var $discountedQuote Mage_Sales_Model_Quote */
        $discountedQuote = $this->_quote->load($this->_quote->getId());
        $discountedPrice = sprintf('%01.2f', $this->_product->getPrice() * (1 - 10 / 100));

        $this->assertEquals(
            $discountedQuote->getSubtotalWithDiscount(),
            $discountedPrice,
            'Quote subtotal price does not match discounted item price'
        );
    }

    /**
     * Test coupon code removing
     */
    public function testCartCouponRemove()
    {
        $this->_init();
        $originalPrice = $this->_product->getPrice();

        // Apply coupon
        $this->_quote->setCouponCode($this->_salesRule->getCouponCode())
            ->collectTotals()
            ->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'shoppingCartCouponRemove',
            array('quoteId' => $this->_quote->getId())
        );

        $this->assertTrue($soapResult, 'Coupon code was not removed');

        /** @var $quoteWithoutDiscount Mage_Sales_Model_Quote */
        $quoteWithoutDiscount = $this->_quote->load($this->_quote->getId());

        $this->assertEquals(
            $originalPrice,
            $quoteWithoutDiscount->getSubtotalWithDiscount(),
            'Quote subtotal price does not match its original price after discount removal'
        );
    }
}
