<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

class WeightTest extends \PHPUnit_Framework_TestCase
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight
     */
    protected $_model;

    /**
     * @var \Magento\Data\Form\Element\Checkbox
     */
    protected $_virtual;

    public function testSetForm()
    {
        $coreHelper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);

        $form = new \Magento\Data\Form($factory, $collectionFactory);

        $helper = $this->getMock('Magento\Catalog\Helper\Product', array('getTypeSwitcherControlLabel'),
            array(), '', false, false
        );
        $helper->expects($this->any())->method('getTypeSwitcherControlLabel')
            ->will($this->returnValue('Virtual / Downloadable'));

        $this->_virtual = $this->getMock('Magento\Data\Form\Element\Checkbox',
            array('setId', 'setName', 'setLabel', 'setForm'),
            array(), '', false, false);
        $this->_virtual->expects($this->any())
            ->method('setId')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setName')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setLabel')
            ->will($this->returnSelf());
        $this->_virtual->expects($this->any())
            ->method('setForm')
            ->with($this->equalTo($form))
            ->will($this->returnSelf());

        $factory->expects($this->once())
            ->method('create')
            ->with($this->equalTo('checkbox'))
            ->will($this->returnValue($this->_virtual));

        $this->_model = new \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight($coreHelper, $factory,
            $collectionFactory, $helper);
        $this->_model->setForm($form);
    }
}
