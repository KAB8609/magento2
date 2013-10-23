<?php
/**
 * Common functions needed for twig extension
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Twig;

class CommonFunctions
{
    /**
     * @var \Magento\Core\Model\UrlInterface
     */
    private $_urlBuilder;

    /**
     * @var \Magento\Core\Helper\Url
     */
    private $_urlHelper;

    /**
     * @var \Magento\Core\Helper\Data
     */
    private $_dataHelper;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    private $_storeManager;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    private $_viewUrl;

    /**
     * @var \Magento\View\ConfigInterface
     */
    private $_viewConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $_helperImage;

    /**
     * @var \Magento\Logger
     */
    private $_logger;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    private $_locale;

    public function __construct(
        \Magento\Core\Model\UrlInterface $urlBuilder,
        \Magento\Core\Helper\Url $urlHelper,
        \Magento\Core\Helper\Data $dataHelper,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\View\ConfigInterface $viewConfig,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Logger $logger,
        \Magento\Core\Model\LocaleInterface $locale
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_urlHelper = $urlHelper;
        $this->_dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->_viewUrl = $viewUrl;
        $this->_viewConfig = $viewConfig;
        $this->_helperImage = $helperImage;
        $this->_logger = $logger;
        $this->_locale = $locale;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        $options = array('is_safe' => array('html'));
        return array(
            new \Twig_SimpleFunction('viewFileUrl', array($this, 'getViewFileUrl'), $options),
            new \Twig_SimpleFunction('getSelectHtml', array($this, 'getSelectHtml'), $options),
            new \Twig_SimpleFunction('getDateFormat', array($this->_locale, 'getDateFormat')),
            new \Twig_SimpleFunction('getSelectFromToHtml', array($this, 'getSelectFromToHtml'), $options),
            new \Twig_SimpleFunction('getUrl', array($this->_urlBuilder, 'getUrl'), $options),
            new \Twig_SimpleFunction('encodeUrl', array($this->_urlHelper, 'getEncodedUrl'), $options),
            new \Twig_SimpleFunction('getCurrentUrl', array($this->_urlHelper, 'getCurrentUrl'), $options),
            new \Twig_SimpleFunction('isModuleOutputEnabled',
                array($this->_dataHelper, 'isModuleOutputEnabled'), $options),
            new \Twig_SimpleFunction('getStoreConfig', array($this->_storeManager->getStore(), 'getConfig'), $options),
            new \Twig_SimpleFunction('getDesignVarValue', array($this->_viewConfig->getViewConfig(), 'getVarValue'),
                $options),
            new \Twig_SimpleFunction('getDefaultImage', array($this->_helperImage, 'getDefaultImage'), $options),
        );
    }

    /**
     * Retrieve url of themes file
     *
     * @param string $file path to file in theme
     * @param array $params
     * @return string
     * @throws \Magento\Exception
     */
    public function getViewFileUrl($file = null, array $params = array())
    {
        try {
            return $this->_viewUrl->getViewFileUrl($file, $params);
        } catch (\Magento\Exception $e) {
            $this->_logger->logException($e);
            return $this->_urlBuilder->getUrl('', array('_direct' => 'core/index/notfound'));
        }
    }

    /**
     * @param \Magento\Core\Block\Html\Select $selectBlock
     * @param $identifier
     * @param $name
     * @param $nameOptionsById
     * @param null $selectedValue
     * @return mixed
     */
    public function getSelectHtml($selectBlock, $identifier, $name, $nameOptionsById, $selectedValue = null)
    {

        $options = array();
        foreach ($nameOptionsById as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }
        return $this->_initSelectBlock($selectBlock, $identifier, $name, $nameOptionsById, $selectedValue)
            ->setOptions($options)
            ->getHtml();
    }

    /**
     * From \Magento\Catalog\Block\Product\View\Options\Type\Date: Return drop-down html with range of values
     *
     * @param \Magento\Core\Block\Html\Select $selectBlock
     * @param string $name Id/name of html select element
     * @param int $fromNumber  Start position
     * @param int $toNumber    End position
     * @param $nameOptionsById
     * @param $optionsId
     * @param null $value Value selected
     * @return string Formatted Html
     */
    public function getSelectFromToHtml(
        $selectBlock, $name, $fromNumber, $toNumber,
        $nameOptionsById, $optionsId, $value = null
    ) {
        $options = array(
            array('value' => '', 'label' => '-')
        );
        for ($i = $fromNumber; $i <= $toNumber; $i++) {
            $options[] = array('value' => $i, 'label' => $this->_getValueWithLeadingZeros($i));
        }
        return $this->_initSelectBlock($selectBlock, $optionsId, $name, $nameOptionsById, $value)
            ->setOptions($options)
            ->getHtml();
    }

    /**
     * Initializes values in the selection list.
     * From \Magento\Catalog\Block\Product\View\Options\Type\Date: HTML select element
     *
     * @param \Magento\Core\Block\Html\Select $selectBlock
     * @param $identifier
     * @param $name
     * @param $nameOptionsById
     * @param null $value
     * @return \Magento\Core\Block\Html\Select
     */
    protected function _initSelectBlock($selectBlock, $identifier, $name, $nameOptionsById, $value = null)
    {
        $selectBlock->setId('options_' . $identifier . '_' . $name);
        $selectBlock->setClass('product-custom-option datetime-picker');
        $selectBlock->setExtraParams();
        $selectBlock->setName('options[' . $identifier . '][' . $name . ']');

        $extraParams = 'style="width:auto"';
        $selectBlock->setExtraParams($extraParams);

        if (is_null($value)) {
            $value = $nameOptionsById;
        }
        if (!is_null($value)) {
            $selectBlock->setValue($value);
        }

        return $selectBlock;
    }

    /**
     * From \Magento\Catalog\Block\Product\View\Options\Type\Date: Add Leading Zeros to number less than 10
     *
     * @param int|string $value value to pad with zeros
     * @return string
     */
    protected function _getValueWithLeadingZeros($value)
    {
        return $value < 10 ? '0'.$value : $value;
    }
}
