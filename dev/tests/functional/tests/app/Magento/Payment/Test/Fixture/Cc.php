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

namespace Magento\Payment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Cc
 * Credit cards for checkout
 *
 * @package Magento\Payment\Test\Fixture
 */
class Cc extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoPaymentCc($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('visa_default');
    }

    /**
     * Retrive Credit Card validation password for 3D Secure
     *
     * @return string
     */
    public function getValidationPassword()
    {
        return $this->getData('validation/password/value');
    }
}
