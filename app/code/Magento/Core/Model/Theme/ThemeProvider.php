<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

class ThemeProvider implements \Magento\View\Design\Theme\ThemeProviderInterface
{
    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Core\Model\ThemeFactory
     */
    protected $themeFactory;

    /**
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\ThemeFactory $themeFactory
     */
    public function __construct(
        \Magento\Core\Model\Resource\Theme\CollectionFactory $collectionFactory,
        \Magento\Core\Model\ThemeFactory $themeFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->themeFactory = $themeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeByFullPath($fullPath)
    {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = $this->collectionFactory->create();
        return $themeCollection->getThemeByFullPath($fullPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeCustomizations(
        $area = \Magento\Core\Model\App\Area::AREA_FRONTEND,
        $type = \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL
    ) {
        /** @var $themeCollection \Magento\Core\Model\Resource\Theme\Collection */
        $themeCollection = $this->collectionFactory->create();
        $themeCollection->addAreaFilter($area)
            ->addTypeFilter($type);
        return $themeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeById($themeId)
    {
        /** @var $themeModel \Magento\View\Design\ThemeInterface */
        $themeModel = $this->themeFactory->create();
        return $themeModel->load($themeId);
    }
}