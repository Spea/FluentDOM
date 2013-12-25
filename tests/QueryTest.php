<?php
namespace FluentDOM {

  require_once(__DIR__.'/../src/_require.php');

  class QueryTest extends \PHPUnit_Framework_TestCase {

    const XML = '
      <items version="1.0">
        <group id="1st">
          <item index="0">text1</item>
          <item index="1">text2</item>
          <item index="2">text3</item>
        </group>
        <html>
          <div class="test1 test2">class testing</div>
          <div class="test2">class testing</div>
        </html>
      </items>
    ';

    /**
     * @group Load
     * @covers Query::load
     */
    public function testLoadWithDocument() {
      $fd = new Query();
      $fd->load($dom = new \DOMDocument());
      $this->assertSame(
        $dom,
        $fd->document
      );
    }

    /**
     * @group CoreFunctions
     * @covers Query::spawn
     */
    public function testSpawn() {
      $fdParent = new Query;
      $fdChild = $fdParent->spawn();
      $this->assertAttributeSame(
        $fdParent,
        '_parent',
        $fdChild
      );
    }

    /**
     * @group CoreFunctions
     * @covers Query::spawn
     */
    public function testSpawnWithElements() {
      $dom = new \DOMDocument;
      $node = $dom->createElement('test');
      $dom->appendChild($node);
      $fdParent = new Query();
      $fdParent->load($dom);
      $fdChild = $fdParent->spawn($node);
      $this->assertSame(
        array($node),
        iterator_to_array($fdChild)
      );
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingTrue() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertTrue(isset($query[1]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetExists
     *
     */
    public function testOffsetExistsExpectingFalse() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertFalse(isset($query[99]));
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetGet() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertEquals('text2', $query[1]->nodeValue);
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetSetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      $query[2] = '123';
    }

    /**
     * @group Interfaces
     * @group ArrayAccess
     * @covers Query::offsetGet
     */
    public function testOffsetUnsetExpectingException() {
      $query = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->setExpectedException('BadMethodCallException');
      unset($query[2]);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOMCore::count
     */
    public function testInterfaceCountableExpecting3() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertCount(3, $fd);
    }

    /**
     * @group Interfaces
     * @group Countable
     * @covers FluentDOMCore::count
     */
    public function testInterfaceCountableExpectingZero() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//non-existing');
      $this->assertCount(0, $fd);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     * @covers FluentDOM\Query::__get
     * @covers FluentDOM\Query::__set
     */
    public function testDynamicProperty() {
      $fd = new Query();
      $this->assertEquals(FALSE, isset($fd->dynamicProperty));
      $this->assertEquals(NULL, $fd->dynamicProperty);
      $fd->dynamicProperty = 'test';
      $this->assertEquals(TRUE, isset($fd->dynamicProperty));
      $this->assertEquals('test', $fd->dynamicProperty);
    }
    /**
     * @covers FluentDOM\Query::__set
     */
    public function testSetPropertyXpath() {
      $fd = $this->getQueryFixtureFromString(self::XML);
      $this->setExpectedException('BadMethodCallException');
      $fd->xpath = $fd->xpath;
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     */
    public function testIssetPropertyLength() {
      $fd = new Query();
      $this->assertTrue(isset($fd->length));
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__get
     */
    public function testGetPropertyLength() {
      $fd = $this->getQueryFixtureFromString(self::XML, '//item');
      $this->assertEquals(3, $fd->length);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     */
    public function testSetPropertyLength() {
      $fd = new Query;
      $this->setExpectedException('BadMethodCallException');
      $fd->length = 50;
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__isset
     */
    public function testIssetPropertyContentType() {
      $fd = new Query();
      $this->assertTrue(isset($fd->contentType));
    }


    /**
     * @group Properties
     * @covers FluentDOM\Query::__get
     */
    public function testGetPropertyContentType() {
      $fd = new Query();
      $this->assertEquals('text/xml', $fd->contentType);
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::_setContentType
     * @dataProvider getContentTypeSamples
     */
    public function testSetPropertyContentType($contentType, $expected) {
      $fd = new Query();
      $fd->contentType = $contentType;
      $this->assertAttributeEquals($expected, '_contentType', $fd);
    }

    public function getContentTypeSamples() {
      return array(
        array('text/xml', 'text/xml'),
        array('text/html', 'text/html'),
        array('xml', 'text/xml'),
        array('html', 'text/html'),
        array('TEXT/XML', 'text/xml'),
        array('TEXT/HTML', 'text/html'),
        array('XML', 'text/xml'),
        array('HTML', 'text/html')
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::setContentType
     */
    public function testSetPropertyContentTypeChaining() {
      $fdParent = new Query();
      $fdChild = $fdParent->spawn();
      $fdChild->contentType = 'text/html';
      $this->assertEquals(
        'text/html',
        $fdParent->contentType
      );
    }

    /**
     * @group Properties
     * @covers FluentDOM\Query::__set
     * @covers FluentDOM\Query::setContentType
     */
    public function testSetPropertyContentTypeInvalid() {
      $fd = new Query();
      $this->setExpectedException('UnexpectedValueException');
      $fd->contentType = 'Invalid Type';
    }

    /******************************
     * Fixtures
     ******************************/

    function getQueryFixtureFromString($string = NULL, $xpath = NULL) {
      $fd = new Query();
      if (!empty($string)) {
        $dom = new \DOMDocument();
        $dom->loadXML($string);
        $fd->load($dom);
        if (!empty($xpath)) {
          $query = new Xpath($dom);
          $nodes = $query->evaluate($xpath);
          $fd = $fd->spawn();
          $fd->push($nodes);
        }
      }
      return $fd;
    }
  }
}