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
 * Rma Item Form Renderer Block for select
 */
namespace Magento\Rma\Block\Form\Renderer;

class Image extends \Magento\CustomAttribute\Block\Form\Renderer\Image
{
    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl('media') . \Magento\Rma\Model\Item::ITEM_IMAGE_URL;
        $file = $this->getValue();
        $url = $url . $file;
        return $url;
    }
}
