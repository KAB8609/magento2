<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account navigation sidebar
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Customer_Block_Account_Navigation extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Object[]
     */
    protected $_links = array();

    protected $_activeLink = false;

    public function addLink($name, $path, $label, $urlParams=array())
    {
        $this->_links[$name] = new Magento_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));
        return $this;
    }

    /**
     * Removes a link from the navigation
     *
     * @param string $name
     * @return Mage_Customer_Block_Account_Navigation
     */
    public function removeLink($name)
    {
        unset($this->_links[$name]);
        return $this;
    }

    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->_frontController->getAction()->getFullActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }
        return false;
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
                // no break

            case 2:
                $path .= '/index';
        }
        return $path;
    }

}
