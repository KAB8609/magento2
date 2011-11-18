<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for fieldset of product custom options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Constructor for our block with options
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'Mage_Catalog_Block_Product_View_Options_Type_Default',
            'catalog/product/composite/fieldset/options/type/default.phtml'
        );
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     *
     * @return string
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template'])
                ->setSkipJsReloadPrice(1);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}
