<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CustomerSegment
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CustomerSegment_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Action_helper method for Create Segment
     *
     * Preconditions: 'Manage Segment' page is opened.
     *
     * @param array $segmData Array which contains DataSet for filling of the current form
     */
    public function createSegment($segmData)
    {
        $this->clickButton('add_new_segment');
        $this->fillTabs($segmData, 'general properties');
        $this->saveForm('save_segment');
    }

    /**
     * Filling tabs
     *
     * @param string|array $segmData
     */
    public function fillTabs($segmData)
    {
        if (is_string($segmData)) {
            $elements = explode('/', $segmData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $segmData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $generalPropertiesTab = (isset($segmData['general_properties'])) ? $segmData['general_properties'] : array();
        $this->fillTab($generalPropertiesTab, 'general_properties');
    }

    /**
     * Open Customer Segment.
     *
     * Preconditions: 'Customer Segment' page is opened.
     *
     * @param array $searchData
     */
    public function openSegment($searchData)
    {
        $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'customer_segment_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $cellId = $this->getColumnIdByName('Segment Name');
        $this->addParameter('segment_title', $this->getElement($xpathTR . '//td[' . $cellId . ']')->text());
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->getElement($xpathTR . '//td[' . $cellId . ']')->click();
        $this->waitForPageToLoad();
        $this->validatePage();
    }

    /**
     * Open Segment and delete
     *
     * @param array $segmentSearch
     */
    public function deleteSegment(array $segmentSearch)
    {
        $this->openSegment($segmentSearch);
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
    }
 }
