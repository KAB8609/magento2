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

namespace Magento\Payment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Method Repository
 * Shipping methods
 *
 * @package Magento\Payment\Test\Repository
 */
class Method extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['paypal_express'] = $this->_getPayPalExpress();
        $this->_data['paypal_direct'] = $this->_getPayPalDirect();
    }

    protected function _getAuthorizeNet()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Paygate\\Test\\Block\\Authorizenet\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'authorizenet'
                ),
            )
        );
    }

    protected function _getPayPalExpress()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'payment_code' => 'paypal_express'
                ),
            )
        );
    }

    protected function _getPayPalDirect()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'paypal_direct'
                ),
            )
        );
    }
}
