<?php
/**
 * Adminhtml all tags grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_All extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/tag/index.phtml');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('createButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => __('Add New Tag'),
                    'onclick' => "window.location.href='" . Mage::getUrl('*/*/edit') . "'",
                ))
        );
        $this->setChild('tagsGrid',
            $this->getLayout()->createBlock('adminhtml/tag_grid_all')
        );
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('createButton');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('tagsGrid');
    }

    public function getHeaderHtml()
    {
        return __('All Tags');
    }

}
