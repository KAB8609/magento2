<?php
/**
 * Http entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class Http extends \Magento\Core\Model\AbstractEntryPoint
{
    /**
     * Process http request, output html page or proper information about an exception (if any)
     */
    public function processRequest()
    {
        try {
            parent::processRequest();
        } catch (\Magento\Core\Model\Session\Exception $e) {
            header(
                'Location: ' . $this->_objectManager->get('Magento\Core\Model\StoreManager')->getStore()->getBaseUrl()
            );
        } catch (\Magento\Core\Model\Store\Exception $e) {
            require $this->_objectManager->get('Magento\Core\Model\Dir')
                    ->getDir(\Magento\Core\Model\Dir::PUB) . DS . 'errors' . DS . '404.php';
        } catch (\Magento\BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
        } catch (\Exception $e) {
            /** @var $store \Magento\Core\Model\Store */
            $store = $this->_objectManager->get('Magento\Core\Model\StoreManager')->getStore();
            $this->_errorHandler->processException($e, $store->getCode());
        }
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        $request = $this->_objectManager->get('Magento\Core\Controller\Request\Http');
        $response = $this->_objectManager->get('Magento\Core\Controller\Response\Http');
        $handler = $this->_objectManager->get('Magento\HTTP\Handler\Composite');
        $handler->handle($request, $response);
    }
}
