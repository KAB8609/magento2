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

namespace Magento\Customer\Test\Block\Address;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Address;

/**
 * Class Edit
 * Customer address edit block
 *
 * @package Magento\Customer\Test\Block\Address
 */
class Edit extends Form
{
    /**
     * 'Save address' button
     *
     * @var string
     */
    private $saveAddress;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'firstname' => '#firstname',
            'lastname' => '#lastname',
            'telephone' => '#telephone',
            'street_1' => '#street_1',
            'city' => '#city',
            'region' => '#region_id',
            'postcode' => '#zip',
            'country' => '#country',
        );
        $this->saveAddress = '.action.submit';
    }

    /**
     * Fill form data. Unset 'email' field as it absent in current form
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        unset($fields['email']);
        parent::_fill($fields);
    }

    /**
     * Edit customer address
     *
     * @param Address $fixture
     */
    public function editCustomerAddress(Address $fixture)
    {
        $this->fill($fixture);
        $this->_rootElement->find($this->saveAddress, Locator::SELECTOR_CSS)->click();
    }
}