<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Order Pdf Items renderer Abstract
 *
 * @category   Mage
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Order_Pdf_Items_Abstract extends Magento_Core_Model_Abstract
{
    /**
     * Order model
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    /**
     * Source model (invoice, shipment, creditmemo)
     *
     * @var Magento_Core_Model_Abstract
     */
    protected $_source;

    /**
     * Item object
     *
     * @var Magento_Object
     */
    protected $_item;

    /**
     * Pdf object
     *
     * @var Magento_Sales_Model_Order_Pdf_Abstract
     */
    protected $_pdf;

    /**
     * Pdf current page
     *
     * @var Zend_Pdf_Page
     */
    protected $_pdfPage;

    /**
     * Set order model
     *
     * @param  Magento_Sales_Model_Order $order
     * @return Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Set Source model
     *
     * @param  Magento_Core_Model_Abstract $source
     * @return Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setSource(Magento_Core_Model_Abstract $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Set item object
     *
     * @param  Magento_Object $item
     * @return Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setItem(Magento_Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Set Pdf model
     *
     * @param  Magento_Sales_Model_Order_Pdf_Abstract $pdf
     * @return Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setPdf(Magento_Sales_Model_Order_Pdf_Abstract $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set current page
     *
     * @param  Zend_Pdf_Page $page
     * @return Magento_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setPage(Zend_Pdf_Page $page)
    {
        $this->_pdfPage = $page;
        return $this;
    }

    /**
     * Retrieve order object
     *
     * @throws Magento_Core_Exception
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            Mage::throwException(Mage::helper('Magento_Sales_Helper_Data')->__('The order object is not specified.'));
        }
        return $this->_order;
    }

    /**
     * Retrieve source object
     *
     * @throws Magento_Core_Exception
     * @return Magento_Core_Model_Abstract
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            Mage::throwException(Mage::helper('Magento_Sales_Helper_Data')->__('The source object is not specified.'));
        }
        return $this->_source;
    }

    /**
     * Retrieve item object
     *
     * @throws Magento_Core_Exception
     * @return Magento_Object
     */
    public function getItem()
    {
        if (is_null($this->_item)) {
            Mage::throwException(Mage::helper('Magento_Sales_Helper_Data')->__('An item object is not specified.'));
        }
        return $this->_item;
    }

    /**
     * Retrieve Pdf model
     *
     * @throws Magento_Core_Exception
     * @return Magento_Sales_Model_Order_Pdf_Abstract
     */
    public function getPdf()
    {
        if (is_null($this->_pdf)) {
            Mage::throwException(Mage::helper('Magento_Sales_Helper_Data')->__('A PDF object is not specified.'));
        }
        return $this->_pdf;
    }

    /**
     * Retrieve Pdf page object
     *
     * @throws Magento_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function getPage()
    {
        if (is_null($this->_pdfPage)) {
            Mage::throwException(Mage::helper('Magento_Sales_Helper_Data')->__('A PDF page object is not specified.'));
        }
        return $this->_pdfPage;
    }

    /**
     * Draw item line
     *
     */
    abstract public function draw();

    /**
     * Format option value process
     *
     * @param  $value
     * @return string
     */
    protected function _formatOptionValue($value)
    {
        $order = $this->getOrder();

        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * Get array of arrays with item prices information for display in PDF
     * array(
     *  $index => array(
     *      'label'    => $label,
     *      'price'    => $price,
     *      'subtotal' => $subtotal
     *  )
     * )
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        $order = $this->getOrder();
        $item  = $this->getItem();
        if (Mage::helper('Magento_Tax_Helper_Data')->displaySalesBothPrices()) {
            $prices = array(
                array(
                    'label'    => Mage::helper('Magento_Tax_Helper_Data')->__('Excl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPrice()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotal())
                ),
                array(
                    'label'    => Mage::helper('Magento_Tax_Helper_Data')->__('Incl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPriceInclTax()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax())
                ),
            );
        } elseif (Mage::helper('Magento_Tax_Helper_Data')->displaySalesPriceInclTax()) {
            $prices = array(array(
                'price' => $order->formatPriceTxt($item->getPriceInclTax()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
            ));
        } else {
            $prices = array(array(
                'price' => $order->formatPriceTxt($item->getPrice()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotal()),
            ));
        }
        return $prices;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions() {
        $result = array();
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Set font as regular
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Return item Sku
     *
     * @param  $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku'))
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }
}
