<?php


namespace FluentDOM\Loader\Supports {

  use FluentDOM\TestCase;

  require_once(__DIR__.'/../../TestCase.php');

  class Json_TestProxy {

    use Json;

    public function getSupported() {
      return ['json'];
    }

    public function getSource($source, $type = 'json') {
      return $this->getJson($source, $type);
    }

    public function getValue($json) {
      return $this->getValueAsString($json);
    }

    protected function transferTo(\DOMNode $node, $json) {
      $node->appendChild($node->createElement('success'));
    }

    public function getNamespace($nodeName, $namespaces, $parent) {
      return $this->getNamespaceForNode($nodeName, $namespaces, $parent);
    }

  }

  class JsonTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithArrayAsString() {
      $loader = new Json_TestProxy();
      $this->assertEquals(['foo'], $loader->getSource(json_encode(['foo'])));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObjectAsString() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(json_encode($json)));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithObject() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource($json));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithFile() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertEquals($json, $loader->getSource(__DIR__.'/TestData/loader.json'));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithUnsupportedTypeExpectingFalse() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertFalse($loader->getSource($json, 'invalid'));
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetSourceWithInvalidJsonExpectingException() {
      $loader = new Json_TestProxy();
      $this->setExpectedException('FluentDOM\Exceptions\JsonError');
      $loader->getSource('{invalid');
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testLoad() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $dom = $loader->load($json, 'json');
      $this->assertXmlStringEqualsXmlString(
        '<success/>', $dom->saveXml()
      );
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testLoadExpectingNull() {
      $json = new \stdClass();
      $json->foo = 'bar';
      $loader = new Json_TestProxy();
      $this->assertNull(
        $loader->load(NULL, 'json')
      );
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetNamespaceForNodeFromNode() {
      $dom = new \DOMDocument();
      $dom->loadXml('<foo:foo xmlns:foo="urn:foo"/>');
      $loader = new Json_TestProxy();
      $this->assertEquals(
        'urn:foo',
        $loader->getNamespace(
          'foo:bar', new \stdClass(), $dom->documentElement
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     */
    public function testGetNamespaceForNodeFromJsonProperties() {
      $dom = new \DOMDocument();
      $dom->loadXml('<foo:foo xmlns:foo="urn:foo"/>');
      $properties = new \stdClass();
      $properties->{'xmlns:foo'} = 'urn:bar';
      $loader = new Json_TestProxy();
      $this->assertEquals(
        'urn:bar',
        $loader->getNamespace(
          'foo:bar', $properties, $dom->documentElement
        )
      );
    }

    /**
     * @covers FluentDOM\Loader\Supports\Json
     * @dataProvider provideJsonValues
     */
    public function testGetValueAsJson($expected, $value) {
      $loader = new Json_TestProxy();
      $this->assertSame(
        $expected,
        $loader->getValue($value)
      );
    }

    public static function provideJsonValues() {
      return [
        ['true', TRUE],
        ['false', FALSE],
        ['', ''],
        ['foo', 'foo'],
        ['42', 42],
        ['42.21', 42.21]
      ];
    }
  }
}