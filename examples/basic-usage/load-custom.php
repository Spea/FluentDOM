<?php

require('../../vendor/autoload.php');
require('../../src/_require.php');

$xmlFile = 'hello.xml';

class ExampleLoader implements FluentDOM\Loadable {

  public function supports($contentType) {
    return TRUE;
  }

  // this could implement checks, error handling, ...
  public function load($source, $contentType = 'text/xml') {
    $dom = new DOMDocument();
    $dom->load($source);
    return $dom;
  }
}

$fd = FluentDOM();
// set loader(s)
$fd->loaders(
  new ExampleLoader
);
// load data  using custom loader
$fd->load($xmlFile);

echo $fd
  ->find('/message')
  ->text('Hello World!');