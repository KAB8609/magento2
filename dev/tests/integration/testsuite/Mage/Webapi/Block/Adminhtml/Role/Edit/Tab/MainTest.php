<?php
/**
 * Test for Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Main block
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Main
     */
    protected $_block;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_layout = $this->_objectManager->get('Mage_Core_Model_Layout');
        $this->_blockFactory = $this->_objectManager->get('Mage_Core_Model_BlockFactory');
        $this->_block = $this->_blockFactory->createBlock('Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Main');
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Mage_Core_Model_Layout');
        unset($this->_objectManager, $this->_urlBuilder, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method
     *
     * @dataProvider prepareFormDataProvider
     * @param Varien_Object $apiRole
     * @param array $formElements
     */
    public function testPrepareForm($apiRole, array $formElements)
    {
        // TODO Move to unit tests after MAGETWO-4015 complete
        $this->assertEmpty($this->_block->getForm());

        $this->_block->setApiRole($apiRole);
        $this->_block->toHtml();

        $form = $this->_block->getForm();
        $this->assertInstanceOf('Varien_Data_Form', $form);
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $this->assertInstanceOf('Varien_Data_Form_Element_Fieldset', $fieldset);
        $elements = $fieldset->getElements();
        foreach ($formElements as $elementId) {
            $element = $elements->searchById($elementId);
            $this->assertNotEmpty($element, "Element '$elementId' not found in form fieldset");
            $this->assertEquals($apiRole->getData($elementId), $element->getValue());
        }
    }

    /**
     * @return array
     */
    public function prepareFormDataProvider()
    {
        return array(
            'Empty API Role' => array(
                new Varien_Object(),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'New API Role' => array(
                new Varien_Object(array(
                    'role_name' => 'Role'
                )),
                array(
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            ),
            'Existed API Role' => array(
                new Varien_Object(array(
                    'id' => 1,
                    'role_name' => 'Role'
                )),
                array(
                    'role_id',
                    'role_name',
                    'in_role_user',
                    'in_role_user_old'
                )
            )
        );
    }
}
