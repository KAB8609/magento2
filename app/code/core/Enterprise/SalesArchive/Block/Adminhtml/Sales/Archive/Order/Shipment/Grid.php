<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive shipments grid block
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Shipment_Grid
    extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('sales_shipment_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Enterprise_SalesArchive_Model_Resource_Order_Shipment_Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/shipmentsgrid', array('_current' => true));
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        if (!empty($this->_exportTypes)) {
            foreach ($this->_exportTypes as $exportType) {
                $url = Mage::helper('core/url')->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl(Mage::helper('core/url')->addRequestParam($url, array('action' => 'shipment')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}
