<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Backend page breadcrumbs
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Breadcrumbs extends Magento_Backend_Block_Template
{
    /**
     * breadcrumbs links
     *
     * @var array
     */
    protected $_links = array();

    protected $_template = 'Magento_Backend::widget/breadcrumbs.phtml';

    protected function _construct()
    {
        $this->addLink(Mage::helper('Magento_Backend_Helper_Data')->__('Home'),
            Mage::helper('Magento_Backend_Helper_Data')->__('Home'), $this->getUrl('*')
        );
    }

    public function addLink($label, $title=null, $url=null)
    {
        if (empty($title)) {
            $title = $label;
        }
        $this->_links[] = array(
            'label' => $label,
            'title' => $title,
            'url'   => $url
        );
        return $this;
    }

    protected function _beforeToHtml()
    {
        // TODO - Moved to Beta 2, no breadcrumbs displaying in Beta 1
        // $this->assign('links', $this->_links);
        return parent::_beforeToHtml();
    }
}
