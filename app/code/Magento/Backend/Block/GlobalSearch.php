<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_GlobalSearch extends Magento_Backend_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Adminhtml::system/search.phtml';

    /**
     * Get components configuration
     * @return array
     */
    public function getWidgetInitOptions()
    {
        return array(
            'suggest' => array(
                'dropdownWrapper' => '<div class="autocomplete-results" ></div >',
                'template' => '[data-template=search-suggest]',
                'termAjaxArgument' => 'query',
                'source' => $this->getUrl('*/index/globalSearch'),
                'filterProperty' => 'name',
                'preventClickPropagation' => false,
                'minLength' => 2
            )
        );
    }
}