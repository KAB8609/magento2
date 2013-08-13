<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Magento_Tag
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Products tags tab
 *
 * @category   Mage
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method     Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag setTitle() setTitle(string $title)
 * @method     array getTitle() getTitle()
 */

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag
    extends Mage_Backend_Block_Template
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * Id of current tab
     */
    const TAB_ID = 'tags';

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(Mage_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);

        $this->setId(self::TAB_ID);
        $this->setTitle($this->_helperFactory->get('Magento_Tag_Helper_Data')->__('Product Tags'));
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->_authorization->isAllowed('Magento_Tag::tag_all');
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/tagGrid', array('_current' => true));
    }

    /**
     * Retrieve id of tab after which current tab will be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'product-reviews';
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs::ADVANCED_TAB_GROUP_CODE;
    }
}
