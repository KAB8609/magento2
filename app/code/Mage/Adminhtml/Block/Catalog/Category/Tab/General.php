<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category edit general tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_General extends Mage_Adminhtml_Block_Catalog_Form
{

    protected $_category;

    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('_general');
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('General Information')));

        if (!$this->getCategory()->getId()) {
//            $fieldset->addField('path', 'select', array(
//                'name'  => 'path',
//                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Category'),
//                'value' => base64_decode($this->getRequest()->getParam('parent')),
//                'values'=> $this->_getParentCategoryOptions(),
//                //'required' => true,
//                //'class' => 'required-entry'
//                ),
//                'name'
//            );
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $parentId
            ));
        } else {
            $fieldset->addField('id', 'hidden', array(
                'name'  => 'id',
                'value' => $this->getCategory()->getId()
            ));
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                'value' => $this->getCategory()->getPath()
            ));
        }

        $this->_setFieldset($this->getCategory()->getAttributes(true), $fieldset);

        if ($this->getCategory()->getId()) {
            if ($this->getCategory()->getLevel() == 1) {
                $fieldset->removeField('url_key');
                $fieldset->addField('url_key', 'hidden', array(
                    'name'  => 'url_key',
                    'value' => $this->getCategory()->getUrlKey()
                ));
            }
        }

        $form->addValues($this->getCategory()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        return array('image' => 'Mage_Adminhtml_Block_Catalog_Category_Helper_Image');
    }

    protected function _getParentCategoryOptions($node=null, &$options=array())
    {
        if (is_null($node)) {
            $node = $this->getRoot();
        }

        if ($node) {
            $options[] = array(
               'value' => $node->getPathId(),
               'label' => str_repeat('&nbsp;', max(0, 3*($node->getLevel()))) . $this->escapeHtml($node->getName()),
            );

            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }

}

