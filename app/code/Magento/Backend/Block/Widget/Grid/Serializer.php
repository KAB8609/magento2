<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method string|array getInputNames()
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid;

class Serializer extends \Magento\View\Block\Template
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $grid = $this->getGridBlock();
        if (is_string($grid)) {
            $grid = $this->getLayout()->getBlock($grid);
        }
        if ($grid instanceof \Magento\Backend\Block\Widget\Grid) {
            $this->setGridBlock($grid)
                ->setSerializeData($grid->{$this->getCallback()}());
        }
        return parent::_prepareLayout();
    }

    /**
     * Set serializer template
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Backend::widget/grid/serializer.phtml');
    }

    /**
     * Get grid column input names to serialize
     *
     * @param bool $asJSON
     *
     * @return string|array
     */
    public function getColumnInputNames($asJSON = false)
    {
        if ($asJSON) {
            return $this->_coreData->jsonEncode((array)$this->getInputNames());
        }
        return (array)$this->getInputNames();
    }

    /**
     * Get object data as JSON
     *
     * @return string
     */
    public function getDataAsJSON()
    {
        $result = array();
        $inputNames = $this->getInputNames();
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        } elseif (!empty($inputNames)) {
            return '{}';
        }
        return $this->_coreData->jsonEncode($result);
    }
}
