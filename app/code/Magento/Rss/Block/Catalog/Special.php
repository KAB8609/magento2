<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Catalog_Special extends Magento_Rss_Block_Catalog_Abstract
{
    /**
     * Zend_Date object for date comparsions
     *
     * @var Zend_Date $_currentDate
     */
    protected static $_currentDate = null;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_iterator;

    /**
     * @param Magento_Core_Model_Resource_Iterator $iterator
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource_Iterator $iterator,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_iterator = $iterator;
        parent::__construct($catalogData, $customerSession, $coreData, $context, $data);
    }

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_special_'.$this->_getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
         //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        //customer group id
        $customerGroupId = $this->_getCustomerGroupId();

        $product = Mage::getModel('Magento_Catalog_Model_Product');

        $fields = array(
            'final_price',
            'price'
        );
        $specials = $product->setStoreId($storeId)->getResourceCollection()
            ->addPriceDataFieldFilter('%s < %s', $fields)
            ->addPriceData($customerGroupId, $websiteId)
            ->addAttributeToSelect(
                    array(
                        'name', 'short_description', 'description', 'price', 'thumbnail',
                        'special_price', 'special_to_date',
                        'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
                    ),
                    'left'
            )
            ->addAttributeToSort('name', 'asc')
        ;

        $newurl = Mage::getUrl('rss/catalog/special/store_id/' . $storeId);
        $title = __('%1 - Special Products', Mage::app()->getStore()->getFrontendName());
        $lang = $this->_storeConfig->getConfig('general/locale/code');

        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        $this->_iterator->walk(
            $specials->getSelect(),
            array(array($this, 'addSpecialXmlCallback')),
            array('rssObj'=> $rssObj, 'results'=> &$results)
        );

        if (sizeof($results)>0) {
            foreach ($results as $result) {
                // render a row for RSS feed
                $product->setData($result);
                $html = sprintf('<table><tr>
                    <td><a href="%s"><img src="%s" alt="" border="0" align="left" height="75" width="75" /></a></td>
                    <td style="text-decoration:none;">%s',
                    $product->getProductUrl(),
                    $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75),
                    $this->helper('Magento_Catalog_Helper_Output')->productAttribute(
                        $product,
                        $product->getDescription(),
                        'description'
                    )
                );

                // add price data if needed
                if ($product->getAllowedPriceInRss()) {
                    if ($this->_catalogData->canApplyMsrp($product)) {
                        $html .= '<br/><a href="' . $product->getProductUrl() . '">'
                            . __('Click for price') . '</a>';
                    } else {
                        $special = '';
                        if ($result['use_special']) {
                            $special = '<br />' . __('Special Expires On: %1',
                                    $this->formatDate($result['special_to_date'],
                                        Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM));
                        }
                        $html .= sprintf('<p>%s %s%s</p>',
                            __('Price: %1', $this->_coreData->currency($result['price'])),
                            __('Special Price: %1', $this->_coreData->currency($result['final_price'])),
                            $special
                        );
                    }
                }

                $html .= '</td></tr></table>';

                $rssObj->_addEntry(array(
                    'title'       => $product->getName(),
                    'link'        => $product->getProductUrl(),
                    'description' => $html
                ));
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addSpecialXmlCallback($args)
    {
        if (!isset(self::$_currentDate)) {
            self::$_currentDate = new Zend_Date();
        }

        // dispatch event to determine whether the product will eventually get to the result
        $product = new Magento_Object(array('allowed_in_rss' => true, 'allowed_price_in_rss' => true));
        $args['product'] = $product;
        $this->_eventManager->dispatch('rss_catalog_special_xml_callback', $args);
        if (!$product->getAllowedInRss()) {
            return;
        }

        // add row to result and determine whether special price is active (less or equal to the final price)
        $row = $args['row'];
        $row['use_special'] = false;
        $row['allowed_price_in_rss'] = $product->getAllowedPriceInRss();
        if (isset($row['special_to_date']) && $row['final_price'] <= $row['special_price']
            && $row['allowed_price_in_rss']
        ) {
            $compareDate = self::$_currentDate->compareDate($row['special_to_date'],
                Magento_Date::DATE_INTERNAL_FORMAT);
            if (-1 === $compareDate || 0 === $compareDate) {
                $row['use_special'] = true;
            }
        }

        $args['results'][] = $row;
    }


    /**
     * Function for comparing two items in collection
     *
     * @param $a
     * @param $b
     * @return  boolean
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date']>$b['start_date'] ? -1 : ($a['start_date']<$b['start_date'] ? 1 : 0);
    }
}
