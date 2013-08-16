<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerSegment_Model_Segment_Report_Detail_Grid_UrlGenerator
    extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Mage_Core_Model_Registry $registry
     */
    public function __construct(Mage_Core_Model_Registry $registry)
    {
        $this->_registryManager = $registry;
        parent::__construct();
    }

    /**
     * Convert template params array and merge with preselected params
     *
     * @param $item
     * @return array|mixed
     */
    protected function _prepareParameters($item)
    {
        $params = array();
        foreach ($this->_extraParamsTemplate as $paramKey => $paramValueMethod) {
            $params[$paramKey] = $this->_registryManager->registry('current_customer_segment')->$paramValueMethod();
        }
        return array_merge($this->_params, $params);
    }
}
