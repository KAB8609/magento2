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

namespace Magento\Backend\Test\Block\Review;

use Mtf\Fixture;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Review edit form
 *
 * @package Magento\Backend\Test\Block\Review
 */
class Edit extends Form
{
    /**
     * Posted by selector
     *
     * @var string
     */
    protected $postedBySelector = 'customer';

    /**
     * Status selector
     *
     * @var string
     */
    protected $statusSelector = 'status_id';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->saveButton = '#save_button';
    }

    /**
     * Get data from 'Posted By' field
     *
     * @return string
     */
    public function getPostedBy()
    {
        return $this->_rootElement->find($this->postedBySelector, Locator::SELECTOR_ID)->getText();
    }

    /**
     * Get data from Status field
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_rootElement->find($this->statusSelector, Locator::SELECTOR_ID, 'select')->getText();
    }

    /**
     * Approve review
     */
    public function approveReview()
    {
        $this->_rootElement->find($this->statusSelector, Locator::SELECTOR_ID, 'select')->setValue('Approved');
        $this->save();
    }
}