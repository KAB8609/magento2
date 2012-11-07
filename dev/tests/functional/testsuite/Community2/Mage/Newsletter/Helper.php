<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Newsletter_Helper extends Core_Mage_Newsletter_Helper
{
    /**
     * Create Newsletter Templates
     * Preconditions: 'New Newsletter Template' page is opened.
     *
     * @param array|string $newsletterData
     */
    public function createNewsletterTemplate($newsletterData)
    {
        if (is_string($newsletterData)) {
            $elements = explode('/', $newsletterData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $newsletterData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        if (empty($newsletterData)) {
            $this->fail('$newsletterData parameter is empty');
        }
        $this->clickButton('add_new_template');
        $this->fillNewsletterForm($newsletterData);
        $this->saveForm('save_template');
    }

    /**
     * <p>Fill fields in Newsletter form according to the resulting array</p>
     *
     * @param array $newsletterData
     * @param string
     */
    public function fillNewsletterForm(array $newsletterData, $fieldName = 'newsletter_edit_form')
    {
        if (empty($newsletterData)) {
            return;
        }
        if (isset($newsletterData['newsletter_content_data']) && $this->buttonIsPresent('convert_to_plain_text')) {
            $this->clickButtonAndConfirm('convert_to_plain_text', 'confirmation_convert_to_plain_text', false);
        }
        $this->fillFieldset($newsletterData, $fieldName);
    }

    /**
     * <p>Edit Newsletter template</p>
     *
     * @param array $dataForSearch
     * @param array $newNewsData
     */
    public function editNewsletter(array $dataForSearch, array $newNewsData)
    {
        if (empty($dataForSearch)) {
            $this->fail('$dataForSearch parameter is empty');
        }
        if (empty($newNewsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $this->openNewsletter($this->convertToFilter($dataForSearch));
        $this->fillNewsletterForm($newNewsData);
        $this->clickButton('save_template');
    }

    /**
     * <p>Convert method. Get newsletter array and convert it to filter array for search</p>
     *
     * @param array $dataForSearch
     *
     * @return array
     */
    public function convertToFilter(array $dataForSearch)
    {
        if (empty($dataForSearch)) {
            return array();
        }

        $searchData = array();
        foreach ($dataForSearch as $key => $value) {
            if (preg_match('/^newsletter/', $key)) {
                $strArr = explode('_', $key);
                if (isset($strArr[0]) && $strArr[0] == 'newsletter') {
                    $strArr[0] = 'filter';
                }
                $key = implode('_', $strArr);
                $searchData[$key] = $value;
            }
        }
        if (isset($searchData['filter_template_sender_name'])) {
            unset($searchData['filter_template_sender_name']);
        }
        if (isset($searchData['filter_template_sender_email'])) {
            $searchData['filter_template_sender'] = $searchData['filter_template_sender_email'];
            unset($searchData['filter_template_sender_email']);
        }
        if (isset($searchData['filter_content_data'])) {
            unset($searchData['filter_content_data']);
        }
        return $searchData;
    }

    /**
     * <p>Put exists Newsletter in to queue</p>
     *
     * @param array $newsData
     * @param array $newData
     */
    public function putNewsToQueue(array $newsData, array $newData = array())
    {
        if (empty($newsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $newsletterXpath = $this->search($this->convertToFilter($newsData), 'newsletter_templates_grid');
        $this->addParameter('prexpath', $newsletterXpath);
        $this->fillDropdown('queue_newsletter', 'Queue Newsletter...');
        $this->waitForPageToLoad();
        $this->addParameter('template_id', $this->defineIdFromUrl());
        $this->validatePage('newsletter_queue_edit');
        $this->fillNewsletterForm($newData, 'queue_edit_form');
        $this->saveForm('save_newsletter');
    }

    /**
     * <p>Delete Newsletter template</p>
     *
     * @param array $newNewsData
     */
    public function deleteNewsletter(array $newNewsData)
    {
        if (empty($newNewsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $this->openNewsletter($this->convertToFilter($newNewsData));
        $this->clickButtonAndConfirm('delete_template', 'confirmation_for_delete');
    }

    /**
     * @param array $searchData
     */
    public function openNewsletter(array $searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $locator = $this->search($searchData, 'newsletter_templates_grid');
        $this->assertNotNull($locator, 'Newsletter is not found');
        $cellId = $this->getColumnIdByName('Template Name');
        $this->addParameter('tableLineXpath', $locator);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($locator));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }
}
