<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Template title block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Title extends Magento_Core_Block_Template
{
    /**
     * Own page title to display on the page
     *
     * @var string
     */
    protected $_pageTitle;

    /**
     * Provide own page title or pick it from Head Block
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (!empty($this->_pageTitle)) {
            return $this->_pageTitle;
        }
        return $this->getLayout()->getBlock('head')->getShortTitle();
    }

    /**
     * Set own page title
     *
     * @param $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
    }
}
