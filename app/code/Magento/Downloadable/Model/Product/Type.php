<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable product type model
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Product_Type extends Magento_Catalog_Model_Product_Type_Virtual
{
    const TYPE_DOWNLOADABLE = 'downloadable';

    /**
     * Downloadable file
     *
     * @var Magento_Downloadable_Helper_File
     */
    protected $_downloadableFile = null;

    /**
     * @var Magento_Downloadable_Model_Resource_SampleFactory
     */
    protected $_sampleResFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Link
     */
    protected $_linkResource;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Collection_Factory
     */
    protected $_linksFactory;

    /**
     * @var Magento_Downloadable_Model_Resource_Sample_CollectionFactory
     */
    protected $_samplesFactory;

    /**
     * @var Magento_Downloadable_Model_SampleFactory
     */
    protected $_sampleFactory;

    /**
     * @var Magento_Downloadable_Model_LinkFactory
     */
    protected $_linkFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Downloadable_Helper_File $downloadableFile
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_File_Storage_Database $fileStorageDb
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Downloadable_Model_Resource_SampleFactory $sampleResFactory
     * @param Magento_Downloadable_Model_Resource_Link $linkResource
     * @param Magento_Downloadable_Model_Resource_Link_Collection_Factory $linksFactory
     * @param Magento_Downloadable_Model_Resource_Sample_CollectionFactory $samplesFactory
     * @param Magento_Downloadable_Model_SampleFactory $sampleFactory
     * @param Magento_Downloadable_Model_LinkFactory $linkFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Downloadable_Helper_File $downloadableFile,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_File_Storage_Database $fileStorageDb,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Logger $logger,
        Magento_Downloadable_Model_Resource_SampleFactory $sampleResFactory,
        Magento_Downloadable_Model_Resource_Link $linkResource,
        Magento_Downloadable_Model_Resource_Link_Collection_Factory $linksFactory,
        Magento_Downloadable_Model_Resource_Sample_CollectionFactory $samplesFactory,
        Magento_Downloadable_Model_SampleFactory $sampleFactory,
        Magento_Downloadable_Model_LinkFactory $linkFactory,
        array $data = array()
    ) {
        $this->_downloadableFile = $downloadableFile;
        $this->_sampleResFactory = $sampleResFactory;
        $this->_linkResource = $linkResource;
        $this->_linksFactory = $linksFactory;
        $this->_samplesFactory = $samplesFactory;
        $this->_sampleFactory = $sampleFactory;
        $this->_linkFactory = $linkFactory;
        parent::__construct($eventManager, $coreData, $fileStorageDb, $filesystem, $coreRegistry, $logger, $data);
    }

    /**
     * Get downloadable product links
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getLinks($product)
    {
        if (is_null($product->getDownloadableLinks())) {
            $_linkCollection = $this->_linksFactory->create()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId())
                ->addPriceToResult($product->getStore()->getWebsiteId());
            $linksCollectionById = array();
            foreach ($_linkCollection as $link) {
                /* @var Magento_Downloadable_Model_Link $link */

                $link->setProduct($product);
                $linksCollectionById[$link->getId()] = $link;
            }
            $product->setDownloadableLinks($linksCollectionById);
        }
        return $product->getDownloadableLinks();
    }

    /**
     * Check if product has links
     *
     * @param Magento_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasLinks($product)
    {
        if ($product->hasData('links_exist')) {
            return $product->getData('links_exist');
        }
        return count($this->getLinks($product)) > 0;
    }

    /**
     * Check if product has options
     *
     * @param Magento_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasOptions($product)
    {
        //return true;
        return $product->getLinksPurchasedSeparately()
            || parent::hasOptions($product);
    }

    /**
     * Check if product has required options
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function hasRequiredOptions($product)
    {
        if (parent::hasRequiredOptions($product) || $product->getLinksPurchasedSeparately()) {
            return true;
        }
        return false;
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @param Magento_Catalog_Model_Product $product
     * @return boolean
     */
    public function getLinkSelectionRequired($product)
    {
        return $product->getLinksPurchasedSeparately();
    }

    /**
     * Get downloadable product samples
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Downloadable_Model_Resource_Sample_Collection
     */
    public function getSamples($product)
    {
        if (is_null($product->getDownloadableSamples())) {
            $_sampleCollection = $this->_samplesFactory->create()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId());
            $product->setDownloadableSamples($_sampleCollection);
        }

        return $product->getDownloadableSamples();
    }

    /**
     * Check if product has samples
     *
     * @param Magento_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasSamples($product)
    {
        return count($this->getSamples($product)) > 0;
    }

    /**
     * Save Product downloadable information (links and samples)
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Downloadable_Model_Product_Type
     */
    public function save($product)
    {
        parent::save($product);

        if ($data = $product->getDownloadableData()) {
            if (isset($data['sample'])) {
                $_deleteItems = array();
                foreach ($data['sample'] as $sampleItem) {
                    if ($sampleItem['is_delete'] == '1') {
                        if ($sampleItem['sample_id']) {
                            $_deleteItems[] = $sampleItem['sample_id'];
                        }
                    } else {
                        unset($sampleItem['is_delete']);
                        if (!$sampleItem['sample_id']) {
                            unset($sampleItem['sample_id']);
                        }
                        $sampleModel = $this->_createSample();
                        $files = array();
                        if (isset($sampleItem['file'])) {
                            $files = $this->_coreData->jsonDecode($sampleItem['file']);
                            unset($sampleItem['file']);
                        }

                        $sampleModel->setData($sampleItem)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId());

                        if ($sampleModel->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $sampleFileName = $this->_downloadableFile->moveFileFromTmp(
                                $sampleModel->getBaseTmpPath(),
                                $sampleModel->getBasePath(),
                                $files
                            );
                            $sampleModel->setSampleFile($sampleFileName);
                        }
                        $sampleModel->save();
                    }
                }
                if ($_deleteItems) {
                    $this->_sampleResFactory->create()->deleteItems($_deleteItems);
                }
            }
            if (isset($data['link'])) {
                $_deleteItems = array();
                foreach ($data['link'] as $linkItem) {
                    if (isset($linkItem['is_delete']) && $linkItem['is_delete'] == '1') {
                        if ($linkItem['link_id']) {
                            $_deleteItems[] = $linkItem['link_id'];
                        }
                    } else {
                        unset($linkItem['is_delete']);
                        if (isset($linkItem['link_id']) && !$linkItem['link_id']) {
                            unset($linkItem['link_id']);
                        }
                        $files = array();
                        if (isset($linkItem['file'])) {
                            $files = $this->_coreData->jsonDecode($linkItem['file']);
                            unset($linkItem['file']);
                        }
                        $sample = array();
                        if (isset($linkItem['sample'])) {
                            $sample = $linkItem['sample'];
                            unset($linkItem['sample']);
                        }
                        $linkModel = $this->_createLink()
                            ->setData($linkItem)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId())
                            ->setWebsiteId($product->getStore()->getWebsiteId())
                            ->setProductWebsiteIds($product->getWebsiteIds());
                        if (null === $linkModel->getPrice()) {
                            $linkModel->setPrice(0);
                        }
                        if ($linkModel->getIsUnlimited()) {
                            $linkModel->setNumberOfDownloads(0);
                        }
                        $sampleFile = array();
                        if ($sample && isset($sample['type'])) {
                            if ($sample['type'] == 'url' && $sample['url'] != '') {
                                $linkModel->setSampleUrl($sample['url']);
                            }
                            $linkModel->setSampleType($sample['type']);
                            if (isset($sample['file'])) {
                                $sampleFile = $this->_coreData->jsonDecode($sample['file']);
                            }
                        }
                        if ($linkModel->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkFileName = $this->_downloadableFile->moveFileFromTmp(
                                $this->_createLink()->getBaseTmpPath(),
                                $this->_createLink()->getBasePath(),
                                $files
                            );
                            $linkModel->setLinkFile($linkFileName);
                        }
                        if ($linkModel->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkSampleFileName = $this->_downloadableFile->moveFileFromTmp(
                                $this->_createLink()->getBaseSampleTmpPath(),
                                $this->_createLink()->getBaseSamplePath(),
                                $sampleFile
                            );
                            $linkModel->setSampleFile($linkSampleFileName);
                        }
                        $linkModel->save();
                    }
                }
                if ($_deleteItems) {
                    $this->_linkResource->deleteItems($_deleteItems);
                }
                if ($product->getLinksPurchasedSeparately()) {
                    $product->setIsCustomOptionChanged();
                }
            }
        }

        return $this;
    }

    /**
     * Check if product can be bought
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Bundle_Model_Product_Type
     * @throws Magento_Core_Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof Magento_Sales_Model_Quote_Item_Option) {
            $buyRequest = new Magento_Object(unserialize($option->getValue()));
            if (!$buyRequest->hasLinks()) {
                if (!$product->getLinksPurchasedSeparately()) {
                    $allLinksIds = $this->_linksFactory->create()
                        ->addProductToFilter($product->getId())
                        ->getAllIds();
                    $buyRequest->setLinks($allLinksIds);
                    $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));
                } else {
                    throw new Magento_Core_Exception(__('Please specify product link(s).'));
                }
            }
        }
        return $this;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        if ($linkIds = $product->getCustomOption('downloadable_link_ids')) {
            $linkOptions = array();
            $links = $this->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($links[$linkId])) {
                    $linkOptions[] = $linkId;
                }
            }
            $options = array_merge($options, array('links' => $linkOptions));
        }
        $options = array_merge($options, array(
            'is_downloadable' => true,
            'real_product_type' => self::TYPE_DOWNLOADABLE
        ));
        return $options;
    }

    /**
     * Setting flag if dowenloadable product can be or not in complex product
     * based on link can be purchased separately or not
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);
        if ($this->getLinkSelectionRequired($product)) {
            $product->setTypeHasRequiredOptions(true);
        } else {
            $product->setTypeHasRequiredOptions(false);
        }

        // Update links_exist attribute value
        $linksExist = false;
        if ($data = $product->getDownloadableData()) {
            if (isset($data['link'])) {
                foreach ($data['link'] as $linkItem) {
                    if (!isset($linkItem['is_delete']) || !$linkItem['is_delete']) {
                        $linksExist = true;
                        break;
                    }
                }
            }
        }

        $product->setTypeHasOptions($linksExist);
        $product->setLinksExist($linksExist);
    }

    /**
     * Retrieve additional searchable data from type instance
     * Using based on product id and store_id data
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getSearchableData($product)
    {
        $searchData = parent::getSearchableData($product);

        $linkSearchData = $this->_createLink()->getSearchableData($product->getId(), $product->getStoreId());
        if ($linkSearchData) {
            $searchData = array_merge($searchData, $linkSearchData);
        }

        $sampleSearchData = $this->_createSample()->getSearchableData($product->getId(), $product->getStoreId());
        if ($sampleSearchData) {
            $searchData = array_merge($searchData, $sampleSearchData);
        }

        return $searchData;
    }

    /**
     * Check is product available for sale
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        return $this->hasLinks($product) && parent::isSalable($product);
    }

    /**
     * Prepare selected options for downloadable product
     *
     * @param  Magento_Catalog_Model_Product $product
     * @param  Magento_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $links = $buyRequest->getLinks();
        $links = (is_array($links)) ? array_filter($links, 'intval') : array();

        $options = array('links' => $links);

        return $options;
    }

    /**
     * Check if downloadable product has links and they can be purchased separately
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function canConfigure($product)
    {
        return $this->hasLinks($product) && $product->getLinksPurchasedSeparately();
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Downloadable product type
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function deleteTypeSpecificData(Magento_Catalog_Model_Product $product)
    {
        if ($product->getOrigData('type_id') === Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $downloadableData = $product->getDownloadableData();
            $sampleItems = array();
            if (isset($downloadableData['sample'])) {
                foreach ($downloadableData['sample'] as $sample) {
                    $sampleItems[] = $sample['sample_id'];
                }
            }
            if ($sampleItems) {
                $this->_sampleResFactory->create()->deleteItems($sampleItems);
            }
            $linkItems = array();
            if (isset($downloadableData['link'])) {
                foreach ($downloadableData['link'] as $link) {
                    $linkItems[] = $link['link_id'];
                }
            }
            if ($linkItems) {
                $this->_linkResource->deleteItems($linkItems);
            }
        }
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options for downloadable links.
     *
     * @param Magento_Object $buyRequest
     * @param Magento_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Magento_Object $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }
        // if adding product from admin area we add all links to product
        $originalLinksPurchasedSeparately = null;
        if ($product->getSkipCheckRequiredOption()) {
            $originalLinksPurchasedSeparately = $product->getLinksPurchasedSeparately();
            $product->setLinksPurchasedSeparately(false);
        }
        $preparedLinks = array();
        if ($product->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks($product) as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else {
            foreach ($this->getLinks($product) as $link) {
                $preparedLinks[] = $link->getId();
            }
        }
        if (null !== $originalLinksPurchasedSeparately) {
            $product->setLinksPurchasedSeparately($originalLinksPurchasedSeparately);
        }
        if ($preparedLinks) {
            $product->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }
        if ($this->getLinkSelectionRequired($product) && $this->_isStrictProcessMode($processMode)) {
            return __('Please specify product link(s).');
        }
        return $result;
    }

    /**
     * @return Magento_Downloadable_Model_Link
     */
    protected function _createLink()
    {
        return $this->_linkFactory->create();
    }

    /**
     * @return Magento_Downloadable_Model_Sample
     */
    protected function _createSample()
    {
        return $this->_sampleFactory->create();
    }
}
