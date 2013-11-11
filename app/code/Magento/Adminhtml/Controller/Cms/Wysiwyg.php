<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wysiwyg controller for different purposes
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Cms;

class Wysiwyg extends \Magento\Backend\App\Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = $this->_objectManager->get('Magento\Core\Helper\Data')->urlDecode($directive);
        $url = $this->_objectManager->create('Magento\Core\Model\Email\Template\Filter')->filter($directive);
        $image = $this->_objectManager->get('Magento\Core\Model\Image\AdapterFactory')->create();
        $response = $this->getResponse();
        try {
            $image->open($url);
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } catch (\Exception $e) {
            $image->open($this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config')->getSkinImagePlaceholderUrl());
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
    }
}
