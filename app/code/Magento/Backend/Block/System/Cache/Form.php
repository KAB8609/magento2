<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cache management form page
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Cache;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Initialize cache management form
     *
     * @return \Magento\Backend\Block\System\Cache\Form
     */
    public function initForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend' => __('Cache Control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => __('No change'),
                'refresh' => __('Refresh'),
                'disable' => __('Disable'),
                'enable' => __('Enable'),
            ),
        ));

        foreach ($this->_coreData->getCacheTypes() as $type => $label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'    => 'enable['.$type.']',
                'label'   => __($label),
                'value'   => 1,
                'checked' => (int)$this->_cacheState->isEnabled($type),
            ));
        }
        $this->setForm($form);
        return $this;
    }
}
