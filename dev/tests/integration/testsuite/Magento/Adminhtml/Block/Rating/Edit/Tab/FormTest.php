<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Rating\Edit\Tab;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento\Adminhtml\Block\Rating\Edit\Tab\Form',
            \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tab\Form')
        );
    }
}
