<?php
/**
 * Store actions block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Store;

use Mtf\Block\Block;

class Actions extends Block
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * Add Store button
     *
     * @var string
     */
    protected $addStoreButton = '#add_store';

    /**
     * Add store
     */
    public function addStoreView()
    {
        $this->_rootElement->find($this->addStoreButton)->click();
    }

    /**
     * Click "Save" button
     */
    public function clickSave()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }
}
