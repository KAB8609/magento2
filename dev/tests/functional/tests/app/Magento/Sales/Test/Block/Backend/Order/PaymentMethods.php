<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Backend\Order;

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Methods
 * Order creation in backend payment methods
 *
 * @package Magento\Sales\Test\Block\Backend\Order
 */
class PaymentMethods extends Block
{
    /**
     * Global page template block
     *
     * @var Template
     */
    protected $templateBlock;

    /**
     * @inheritdoc
     */
    protected function _init()
    {
        $this->templateBlock = Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find('./ancestor::body', Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Select payment method
     *
     * @param Order $fixture
     */
    public function selectPaymentMethod(Order $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $paymentInput = $this->_rootElement->find('#p_method_' . $paymentCode, Locator::SELECTOR_CSS);
        if ($paymentInput->isVisible()) {
            $paymentInput->click();
        }
        $this->templateBlock->waitLoader();
    }
}