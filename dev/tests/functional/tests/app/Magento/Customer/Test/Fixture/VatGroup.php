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

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class VAT Customer Group Fixture
 *
 * @package Magento\Customer\Test\Fixture
 */
class VatGroup extends DataFixture
{
    /**
     * Customer fixture
     *
     * @var \Magento\Customer\Test\Fixture\Customer
     */
    protected $customerFixture;

    /**
     * Customer groups
     *
     * @var array
     */
    protected $customerGroups;

    /**
     * Vat config
     *
     * @var \Magento\Customer\Test\Fixture\CustomerConfig
     */
    protected $vatConfig;

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'default_group' => array(
                'name' => array(
                    'value' => 'General',
                ),
            ),
            'vat' => array(
                'uk' => array(
                    'invalid' => array(
                        'value' => '123456789',
                    ),
                    'valid' => array(
                        'value' => '584451913',
                    ),
                ),
            ),
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('general_store_information');
        $config->persist();

        // temporary solution in order to apply placeholders after second switchData method call
        $this->vatConfig = Factory::getFixtureFactory()->getMagentoCustomerCustomerConfig();
        $this->vatConfig->switchData('customer_vat');
        $this->vatConfig->persist();

        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customerFixture->switchData('customer_UK_1');
        Factory::getApp()->magentoCustomerSaveCustomerWithAddress($this->customerFixture);
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customerFixture;
    }

    /**
     * Get customer group
     *
     * @return string
     */
    public function getDefaultCustomerGroup()
    {
        return $this->getData('default_group/name/value');
    }

    /**
     * Get Invalid VAT id group
     *
     * @return string
     */
    public function getInvalidVatGroup()
    {
        return $this->vatConfig->getInvalidVatGroup();
    }

    /**
     * Get group name for valid VAT intra-union
     *
     * @return string
     */
    public function getValidVatIntraUnionGroup()
    {
        return $this->vatConfig->getValidVatIntraUnionGroup();
    }

    /**
     * Get groups ids
     *
     * @return array
     */
    public function getGroupsIds()
    {
        return $this->vatConfig->getGroupIds();
    }

    /**
     * Get invalid VAT number
     *
     * @return string
     */
    public function getInvalidVatNumber()
    {
        return $this->getData('vat/uk/invalid/value');
    }

    /**
     * Get valid VAT number
     *
     * @return string
     */
    public function getValidVatNumber()
    {
        return $this->getData('vat/uk/valid/value');
    }
}
