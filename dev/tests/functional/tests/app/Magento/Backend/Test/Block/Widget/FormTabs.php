<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Widget;

use Mtf\Fixture;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class FormTabs
 * Is used to represent any form with tabs on the page
 *
 * @package Magento\Backend\Test\Block\Widget
 */
class FormTabs extends Form
{
    /**
     * @var array
     */
    protected $_tabClasses = array();

    /**
     * Fill form with tabs
     *
     * @param Fixture $fixture
     * @param Element $element
     * @return FormTabs
     */
    public function fill(Fixture $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            $tabElement = $this->getTabElement($tab);
            $tabElement->open($this->_rootElement);
            $tabElement->fillFormTab($tabFields, $this->_rootElement);
        }
        return $this;
    }

    /**
     * Update form with tabs
     *
     * @param Fixture $fixture
     * @return FormTabs
     */
    public function update(Fixture $fixture)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        foreach ($tabs as $tab => $tabFields) {
            $tabElement = $this->getTabElement($tab);
            $tabElement->open($this->_rootElement);
            $tabElement->updateFormTab($tabFields, $this->_rootElement);
        }
        return $this;
    }

    /**
     * Create data array for filling tabs
     *
     * @param Fixture $fixture
     * @return array
     */
    protected function getFieldsByTabs(Fixture $fixture)
    {
        $tabs = array();

        $dataSet = $fixture->getData();
        $fields = isset($dataSet['fields']) ? $dataSet['fields'] : array();

        foreach ($fields as $field => $attributes) {
            if (!isset($attributes['group'])) {
                continue;
            }
            $tabs[$attributes['group']][$field] = $attributes;
        }
        return $tabs;
    }

    /**
     * Get tab element
     *
     * @param $tab
     * @return Tab
     * @throws \Exception
     */
    private function getTabElement($tab)
    {
        $tabRootElement = $this->_rootElement->find($tab, Locator::SELECTOR_ID);

        $tabClass = isset($this->_tabClasses[$tab])
            ? $this->_tabClasses[$tab]
            : '\\Magento\\Backend\\Test\\Block\\Widget\\Tab';
        /** @var $tabElement Tab */
        $tabElement = new $tabClass($tabRootElement);
        if (!$tabElement instanceof Tab) {
            throw new \Exception('Wrong Tab Class.');
        }

        return $tabElement;
    }

    /**
     * Open tab
     *
     * @param string $tabName
     * @return FormTabs
     */
    public function openTab($tabName)
    {
        $tabElement = $this->getTabElement($tabName);
        $tabElement->open($this->_rootElement);
        return $this;
    }
}
