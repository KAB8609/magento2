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

namespace Magento\Backend\Test\Block\Page;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Header block
 *
 * @package Magento\Backend\Test\Block
 */
class Header extends Block
{
    /**
     * Selector for Account Avatar
     *
     * @var string
     */
    protected $adminAccountLink = '.account-avatar';

    /**
     * Selector for Log Out Link
     *
     * @var string
     */
    protected $signOutLink = '.account-signout';

    /**
     * Log out Admin User
     */
    public function logOut()
    {
        $this->_rootElement->find($this->adminAccountLink, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find($this->signOutLink, Locator::SELECTOR_CSS)->click();
    }
}
