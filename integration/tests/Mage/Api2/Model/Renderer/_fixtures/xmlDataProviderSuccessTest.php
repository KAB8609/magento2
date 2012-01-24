<?php
//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <data_item>test1</data_item>
  <data_item>test2</data_item>
  <data_item>
    <test01>some1</test01>
    <test02>some2</test02>
    <test03>
      <test002>some02</test002>
      <data_item_100test>some01</data_item_100test>
    </test03>
  </data_item>
</zend-config>

XML;
$data[] = array(
    'test1',
    'test2',
    (object)array(
        'test01' => 'some1',
        'test02' => 'some2',
        'test03' => array(
            '100test' => 'some01',
            'test002' => 'some02',
        ),
    )
);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <assoc.test002>1 &gt; 0</assoc.test002>
  <assoc_test003.>chars ]]&gt;</assoc_test003.>
  <assoc_test004>chars  !"#$%&amp;'()*+,/;&lt;=&gt;?@[\]^`{|}~  chars </assoc_test004>
  <data_item_0>assoc_item1</data_item_0>
  <data_item_1>assoc_item2</data_item_1>
  <assoc_test001>&lt;some01&gt;text&lt;/some01&gt;</assoc_test001>
  <key_chars__.>chars</key_chars__.>
</zend-config>

XML;
$data[] = array(
    'assoc_item1',
    'assoc_item2',
    'assoc:test001' => '<some01>text</some01>',
    'assoc.test002' => '1 > 0',
    'assoc_test003.' => 'chars ]]>',
    'assoc_test004' => 'chars  !"#$%&\'()*+,/;<=>?@[\]^`{|}~  chars ',
    'key chars `\/;:][{}"|\'.,~!@#$%^&*()_+' => 'chars',
);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <foo_bar></foo_bar>
</zend-config>

XML;
$data[] = array('foo_bar' => '');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <data_item>some1</data_item>
</zend-config>

XML;
$data[] = array('1' => 'some1');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <data_item_1.234>0.123</data_item_1.234>
</zend-config>

XML;
$data[] = array('1.234' => .123);

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <foo>bar</foo>
</zend-config>

XML;
$data[] = array('foo' => 'bar');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <data_item>string</data_item>
</zend-config>

XML;
$data[] = 'string';

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/">
  <foo>&gt;bar=</foo>
</zend-config>

XML;
$data[] = array('foo' => '>bar=');

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/"/>

XML;
$data[] = array();

//case
$xml[] = <<<XML
<?xml version="1.0"?>
<zend-config xmlns:zf="http://framework.zend.com/xml/zend-config-xml/1.0/"/>

XML;
$data[] = new stdClass();


$cnt = 0;
return array(
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt++]),
    array($xml[$cnt], $data[$cnt]),
);
