<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$template = new Mage_Newsletter_Model_Template;
$template->setTemplateCode('fixture_tpl')
    ->setTemplateText('<p>Follow this link to unsubscribe</p>
<!-- This tag is for unsubscribe link  -->
<p><a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}</a></p>')
    ->setTemplateType(2)
    ->setTemplateSubject('Subject')
    ->setTemplateSenderName('CustomerSupport')
    ->setTemplateSenderEmail('support@example.com')
    ->setTemplateActual(1)
    ->save()
;
