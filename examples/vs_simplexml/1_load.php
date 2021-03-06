<?php
require(dirname(__FILE__).'/../../vendor/autoload.php');

$file = 'data://text/xml;base64,'.base64_encode('<foo/>');
$string = '<foo/>';

/* SimpleXML load xml from string */
$element = simplexml_load_string($string);
echo $element->saveXML();

/* SimpleXML load xml from file */
$element = simplexml_load_file($file);
echo $element->saveXML();

/* FluentDOM load xml from string */
$document = FluentDOM::load($string);
echo $document->saveXML();

/* FluentDOM load xml from string */
$document = FluentDOM::load($file);
echo $document->saveXML();

/* FluentDOM load html from string */
$document = FluentDOM::load('<div/>', 'html');
echo $document->saveHTML();

/* FluentDOM load json from string */
$document = FluentDOM::load('{"foo": "bar"}', 'json');
echo $document->saveXML();
