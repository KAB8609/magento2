<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Backend\Block;

/**
 * Base widget class
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Widget extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function getId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get HTML ID with specified suffix
     *
     * @param string $suffix
     * @return string
     */
    public function getSuffixId($suffix)
    {
        return "{$this->getId()}_{$suffix}";
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getId();
    }

    /**
     * Get current url
     *
     * @param array $params url parameters
     * @return string current url
     */
    public function getCurrentUrl($params = [])
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/*', $params);
    }

    /**
     * @param string $label
     * @param string|null $title
     * @param string|null $link
     * @return void
     */
    protected function _addBreadcrumb($label, $title = null, $link = null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }

    /**
     * Create button and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $buttonId
     * @param array $dataAttr
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class = '', $buttonId = null, $dataAttr = [])
    {
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => $label, 'onclick' => $onclick, 'class' => $class, 'type' => 'button', 'id' => $buttonId]
        )->setDataAttribute(
            $dataAttr
        )->toHtml();
    }

    /**
     * @return string
     */
    public function getGlobalIcon()
    {
        return '<img src="' . $this->getViewFileUrl(
            'images/fam_link.gif'
        ) . '" alt="' . __(
            'Global Attribute'
        ) . '" title="' . __(
            'This attribute shares the same value in all stores.'
        ) . '" class="attribute-global"/>';
    }
}
