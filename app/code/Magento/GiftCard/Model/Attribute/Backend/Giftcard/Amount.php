<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Attribute\Backend\Giftcard;

class Amount
    extends \Magento\Catalog\Model\Product\Attribute\Backend\Price
{
    /**
     * Giftcard amount backend resource model
     *
     * @var \Magento\GiftCard\Model\Resource\Attribute\Backend\Giftcard\Amount
     */
    protected $_amountResource;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory helper
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\GiftCard\Model\Resource\Attribute\Backend\Giftcard\Amount $amountResource
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Core\Model\Config $config,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\GiftCard\Model\Resource\Attribute\Backend\Giftcard\Amount $amountResource,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_storeManager = $storeManager;
        $this->_directoryHelper = $directoryHelper;
        $this->_amountResource = $amountResource;
        parent::__construct($logger, $currencyFactory, $storeManager, $catalogData, $config);
    }

    /**
     * Validate data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  \Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount
     * @throws \Magento\Core\Exception
     */
    public function validate($object)
    {
        $rows = $object->getData($this->getAttribute()->getName());
        if (empty($rows)) {
            return $this;
        }
        $dup = array();

        foreach ($rows as $row) {
            if (!isset($row['price']) || !empty($row['delete'])) {
                continue;
            }

            $key1 = implode('-', array($row['website_id'], $row['price']));

            if (!empty($dup[$key1])) {
                throw new \Magento\Core\Exception(
                    __('Duplicate amount found.')
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign amounts to product data
     *
     * @param   \Magento\Catalog\Model\Product $object
     * @return  \Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount
     */
    public function afterLoad($object)
    {
        $data = $this->_amountResource->loadProductData($object, $this->getAttribute());

        foreach ($data as $i=>$row) {
            if ($data[$i]['website_id'] == 0) {
                $rate = $this->_storeManager->getStore()->getBaseCurrency()
                    ->getRate($this->_directoryHelper->getBaseCurrencyCode());
                if ($rate) {
                    $data[$i]['website_value'] = $data[$i]['value']/$rate;
                } else {
                    unset($data[$i]);
                }
            } else {
                $data[$i]['website_value'] = $data[$i]['value'];
            }

        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    /**
     * Save amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return \Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount
     */
    public function afterSave($object)
    {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_amountResource->deleteProductData($object, $this->getAttribute());
        $rows = $object->getData($this->getAttribute()->getName());

        if (!is_array($rows)) {
            return $this;
        }

        foreach ($rows as $row) {
            // Handle the case when model is saved whithout data received from user
            if (((!isset($row['price']) || empty($row['price'])) && !isset($row['value']))
                || !empty($row['delete'])
            ) {
                continue;
            }

            $data = array();
            $data['website_id']   = $row['website_id'];
            $data['value']        = (isset($row['price'])) ? $row['price'] : $row['value'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_amountResource->insertProductData($object, $data);
        }

        return $this;
    }

    /**
     * Delete amounts data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return \Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount
     */
    public function afterDelete($object)
    {
        $this->_amountResource->deleteProductData($object, $this->getAttribute());
        return $this;
    }

    /**
     * Retreive storage table
     *
     * @return string
     */
/*
    public function getTable()
    {
        return $this->_amountResource->getMainTable();
    }
*/
}
