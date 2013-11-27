<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Redirect Page
 *
 * @category    Magento
 * @package     Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Page\Block;

class Redirect extends \Magento\View\Element\Template
{
    /**
     *  HTML form hidden fields
     */
    protected $_formFields = array();

    /**
     * @var \Magento\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_formFactory = $formFactory;
        parent::__construct($context, $coreData, $data);
    }

    /**
     *  URL for redirect location
     *
     *  @return	  string URL
     */
    public function getTargetURL ()
    {
        return '';
    }

    /**
     *  Additional custom message
     *
     *  @return	  string Output message
     */
    public function getMessage ()
    {
        return '';
    }

    /**
     *  Client-side redirect engine output
     *
     *  @return	  string
     */
    public function getRedirectOutput ()
    {
        if ($this->isHtmlFormRedirect()) {
            return $this->getHtmlFormRedirect();
        } else {
            return $this->getRedirect();
        }
    }

    /**
     *  Redirect via JS location
     *
     *  @return	  string
     */
    public function getRedirect ()
    {
        return '<script type="text/javascript">
            (function($){
                $($.mage.redirect("' . $this->getTargetURL() . '"));
            })(jQuery);
        </script>';
    }

    /**
     *  Redirect via HTML form submission
     *
     *  @return	  string
     */
    public function getHtmlFormRedirect ()
    {
        $form = $this->_formFactory->create();
        $form->setAction($this->getTargetURL())
            ->setId($this->getFormId())
            ->setName($this->getFormId())
            ->setAttr('data-auto-submit', 'true')
            ->setMethod($this->getMethod())
            ->setUseContainer(true);
        foreach ($this->_getFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        return $form->toHtml();
    }

    /**
     *  HTML form or JS redirect
     *
     *  @return	  boolean
     */
    public function isHtmlFormRedirect ()
    {
        return is_array($this->_getFormFields()) && count($this->_getFormFields()) > 0;
    }

    /**
     *  HTML form id/name attributes
     *
     *  @return	  string Id/name
     */
    public function getFormId()
    {
        return '';
    }

    /**
     *  HTML form method attribute
     *
     *  @return	  string Method
     */
    public function getFormMethod ()
    {
        return 'POST';
    }

    /**
     *  Array of hidden form fields (name => value)
     *
     *  @return	  array
     */
    public function getFormFields()
    {
        return array();
    }

    /**
     *  Optimized getFormFields() method
     *
     *  @return	  array
     */
    protected function _getFormFields()
    {
        if (!is_array($this->_formFields) || count($this->_formFields) == 0) {
            $this->_formFields = $this->getFormFields();
        }
        return $this->_formFields;
    }

}
