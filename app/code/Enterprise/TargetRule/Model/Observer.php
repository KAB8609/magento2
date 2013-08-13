<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * TargetRule observer
 *
 */
class Enterprise_TargetRule_Model_Observer
{
    /**
     * Prepare target rule data
     *
     * @param Magento_Event_Observer $observer
     */
    public function prepareTargetRuleSave(Magento_Event_Observer $observer)
    {
        $_vars = array('targetrule_rule_based_positions', 'tgtr_position_behavior');
        $_varPrefix = array('related_', 'upsell_', 'crosssell_');
        if ($product = $observer->getEvent()->getProduct()) {
            foreach ($_vars as $var) {
                foreach ($_varPrefix as $pref) {
                    $v = $pref . $var;
                    if ($product->getData($v.'_default') == 1) {
                        $product->setData($v, null);
                    }
                }
            }
        }
    }

    /**
     * After Catalog Product Save - rebuild product index by rule conditions
     * and refresh cache index
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_TargetRule_Model_Observer
     */
    public function catalogProductAfterSave(Magento_Event_Observer $observer)
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        Mage::getSingleton('Mage_Index_Model_Indexer')->logEvent(
            new Magento_Object(array(
                'id' => $product->getId(),
                'store_id' => $product->getStoreId(),
                'rule' => $product->getData('rule'),
                'from_date' => $product->getData('from_date'),
                'to_date' => $product->getData('to_date')
            )),
            Enterprise_TargetRule_Model_Index::ENTITY_PRODUCT,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
        return $this;
    }

    /**
     * Process event on 'save_commit_after' event
     *
     * @param Magento_Event_Observer $observer
     */
    public function catalogProductSaveCommitAfter(Magento_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Index_Model_Indexer')->indexEvents(
            Enterprise_TargetRule_Model_Index::ENTITY_PRODUCT,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
    }

    /**
     * Clear customer segment indexer if customer segment is on|off on backend
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_TargetRule_Model_Observer
     */
    public function coreConfigSaveCommitAfter(Magento_Event_Observer $observer)
    {
        if ($observer->getDataObject()->getPath() == 'customer/enterprise_customersegment/is_enabled'
            && $observer->getDataObject()->isValueChanged()) {
            Mage::getSingleton('Mage_Index_Model_Indexer')->logEvent(
                new Magento_Object(array('type_id' => null, 'store' => null)),
                Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
                Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
            );
            Mage::getSingleton('Mage_Index_Model_Indexer')->indexEvents(
                Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
                Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
            );
        }
        return $this;
    }
}
