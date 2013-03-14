<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Adminhtml_Banner_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $bannersGrid = $this->getLayout()->createBlock(
            'Enterprise_Banner_Block_Adminhtml_Widget_Chooser', '', array('data' => array('id' => $uniqId))
        );
        $html = $bannersGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
