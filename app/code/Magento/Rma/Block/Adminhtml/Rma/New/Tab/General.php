<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Tab in New RMA form
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\New\Tab;

class General extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();
        $htmlIdPrefix = 'rma_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setForm($form);
        return $this;
    }

}
