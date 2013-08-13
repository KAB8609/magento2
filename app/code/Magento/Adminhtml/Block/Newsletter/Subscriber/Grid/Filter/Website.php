<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected $_websiteCollection = null;

    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, array('label'=>null, 'value'=>null));
        return $result;
    }

    public function getCollection()
    {
        if(is_null($this->_websiteCollection)) {
            $this->_websiteCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Website_Collection')
                ->load();
        }

        Mage::register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

    public function getCondition()
    {

        $id = $this->getValue();
        if(!$id) {
            return null;
        }

        $website = Mage::app()->getWebsite($id);

        return array('in'=>$website->getStoresIds(true));
    }

}
