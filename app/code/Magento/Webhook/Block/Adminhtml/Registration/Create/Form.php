<?php
/**
 * Creates registration form
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Webhook\Block\Adminhtml\Registration\Create;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /** Constants for API user details */
    const API_KEY_LENGTH = 32;
    const API_SECRET_LENGTH = 32;
    const MIN_TEXT_INPUT_LENGTH = 20;

    /** Registry key for getting subscription data */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Data key for getting subscription id out of subscription data */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';

    /**
     * Prepares registration form
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $subscription = $this->_coreRegistry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
        $apiKey = $this->_generateRandomString(self::API_KEY_LENGTH);
        $apiSecret = $this->_generateRandomString(self::API_SECRET_LENGTH);
        $inputLength = max(self::API_KEY_LENGTH, self::API_SECRET_LENGTH, self::MIN_TEXT_INPUT_LENGTH);

        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'api_user',
                'action' => $this->getUrl(
                    'adminhtml/*/register', array('id' => $subscription[self::DATA_SUBSCRIPTION_ID])
                ),
                'method' => 'post',
            ))
        );

        $fieldset = $form;

        $fieldset->addField('company', 'text', array(
            'label'     => __('Company'),
            'name'      => 'company',
            'size'      => $inputLength,
        ));

        $fieldset->addField('email', 'text', array(
            'label'     => __('Contact Email'),
            'name'      => 'email',
            'class'     => 'email',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $fieldset->addField('apikey', 'text', array(
            'label'     => __('API Key'),
            'name'      => 'apikey',
            'value'     => $apiKey,
            'class'     => 'monospace',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $fieldset->addField('apisecret', 'text', array(
            'label'     => __('API Secret'),
            'name'      => 'apisecret',
            'value'     => $apiSecret,
            'class'     => 'monospace',
            'required'  => true,
            'size'      => $inputLength,
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Generates a random alphanumeric string
     *
     * @param int $length
     * @return string
     */
    private function _generateRandomString($length)
    {
        return $this->mathRandom->getRandomString(
            $length, \Magento\Math\Random::CHARS_DIGITS . \Magento\Math\Random::CHARS_LOWERS
        );
    }
}
