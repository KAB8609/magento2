<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales controller for download purposes
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller;

class Download extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileResponseFactory;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileResponseFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileResponseFactory
    ) {
        $this->_fileResponseFactory = $fileResponseFactory;
        parent::__construct($context);
    }

    /**
     * Custom options downloader
     *
     * @param mixed $info
     */
    protected function _downloadFileAction($info)
    {
        $secretKey = $this->getRequest()->getParam('key');
        try {
            if ($secretKey != $info['secret_key']) {
                throw new \Exception();
            }

            $filePath = $this->_objectManager->get('Magento\App\Dir')
                ->getDir(\Magento\App\Dir::ROOT) . $info['order_path'];
            if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                //try get file from quote
                $filePath = $this->_objectManager->get('Magento\App\Dir')
                    ->getDir(\Magento\App\Dir::ROOT) . $info['quote_path'];
                if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                    throw new \Exception();
                }
            }
            $this->_fileResponseFactory->create($info['title'], array(
               'value' => $filePath,
               'type'  => 'filename'
            ));
        } catch (\Exception $e) {
            $this->_forward('noRoute');
        }
    }

    /**
     * Check file in database storage if needed and place it on file system
     *
     * @param string $filePath
     * @return bool
     */
    protected function _processDatabaseFile($filePath)
    {
        if (!$this->_objectManager->get('Magento\Core\Helper\File\Storage\Database')->checkDbUsage()) {
            return false;
        }

        $relativePath = $this->_objectManager->get('Magento\Core\Helper\File\Storage\Database')
            ->getMediaRelativePath($filePath);
        $file = $this->_objectManager->create('Magento\Core\Model\File\Storage\Database')
            ->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        $directory = dirname($filePath);
        @mkdir($directory, 0777, true);

        $io = new \Magento\Io\File();
        $io->cd($directory);

        $io->streamOpen($filePath);
        $io->streamLock(true);
        $io->streamWrite($file->getContent());
        $io->streamUnlock();
        $io->streamClose();

        return true;
    }

    /**
     * Profile custom options download action
     */
    public function downloadProfileCustomOptionAction()
    {
        $recurringProfile = $this->_objectManager->create('Magento\Sales\Model\Recurring\Profile')
            ->load($this->getRequest()->getParam('id'));

        if (!$recurringProfile->getId()) {
            $this->_forward('noRoute');
        }

        $orderItemInfo = $recurringProfile->getData('order_item_info');
        try {
            $request = unserialize($orderItemInfo['info_buyRequest']);

            if ($request['product'] != $orderItemInfo['product_id']) {
                $this->_forward('noRoute');
                return;
            }

            $optionId = $this->getRequest()->getParam('option_id');
            if (!isset($request['options'][$optionId])) {
                $this->_forward('noRoute');
                return;
            }
            // Check if the product exists
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($request['product']);
            if (!$product || !$product->getId()) {
                $this->_forward('noRoute');
                return;
            }
            // Try to load the option
            $option = $product->getOptionById($optionId);
            if (!$option || !$option->getId() || $option->getType() != 'file') {
                $this->_forward('noRoute');
                return;
            }
            $this->_downloadFileAction($request['options'][$this->getRequest()->getParam('option_id')]);
        } catch (\Exception $e) {
            $this->_forward('noRoute');
        }
    }

    /**
     * Custom options download action
     */
    public function downloadCustomOptionAction()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var $option \Magento\Sales\Model\Quote\Item\Option */
        $option = $this->_objectManager->create('Magento\Sales\Model\Quote\Item\Option')->load($quoteItemOptionId);

        if (!$option->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $optionId = null;
        if (strpos($option->getCode(), \Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(\Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                $optionId = null;
            }
        }
        $productOption = null;
        if ($optionId) {
            /** @var $productOption \Magento\Catalog\Model\Product\Option */
            $productOption = $this->_objectManager->create('Magento\Catalog\Model\Product\Option')->load($optionId);
        }
        if (!$productOption || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId() || $productOption->getType() != 'file'
        ) {
            $this->_forward('noRoute');
            return;
        }

        try {
            $info = unserialize($option->getValue());
            $this->_downloadFileAction($info);
        } catch (\Exception $e) {
            $this->_forward('noRoute');
        }
        exit(0);
    }
}
