<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array
(
    'options_node_is_required' => array(
        '<?xml version="1.0"?><config><inputType name="name_one" /></config>',
        array("Element 'inputType': This element is not expected. Expected is ( option ).")),
    'inputType_node_is_required' => array(
        '<?xml version="1.0"?><config><option name="name_one"/></config>',
        array("Element 'option': Missing child element(s). Expected is ( inputType ).")),
    'options_name_must_be_unique' => array(
        '<?xml version="1.0"?><config><option name="name_one"><inputType name="name"/>'
        . '</option><option name="name_one"><inputType name="name_two"/></option></config>',
        array("Element 'option': Duplicate key-sequence ['name_one'] in unique identity-constraint "
        . "'uniqueOptionName'.")),
    'inputType_name_must_be_unique' => array(
        '<?xml version="1.0"?><config><option name="name"><inputType name="name_one"/>'
        . '<inputType name="name_one"/></option></config>',
        array("Element 'inputType': Duplicate key-sequence ['name_one'] in unique identity-constraint "
        . "'uniqueInputTypeName'.")),
    'renderer_attribute_with_invalid_value' => array(
        '<?xml version="1.0"?><config><option name="name_one" renderer="true12"><inputType name="name_one"/>'
        . '</option></config>',
        array("Element 'option', attribute 'renderer': [facet 'pattern'] The value 'true12' is not accepted by the "
        . "pattern '[a-zA-Z_\\\\\\\\]+'.",
              "Element 'option', attribute 'renderer': 'true12' is not a valid value of the atomic"
        . " type 'modelName'.")),
    'disabled_attribute_with_invalid_value' => array(
        '<?xml version="1.0"?><config><option name="name_one"><inputType name="name_one" disabled="7"/>'
        . '<inputType name="name_two" disabled="some_string"/></option></config>',
        array("Element 'inputType', attribute 'disabled': '7' is not a valid value of the atomic type 'xs:boolean'.",
        "Element 'inputType', attribute 'disabled': 'some_string' is not a valid value of the atomic type "
        . "'xs:boolean'.")),
);