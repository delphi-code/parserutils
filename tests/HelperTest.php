<?php

namespace delphi\Tests\ParserUtils;

use delphi\ParserUtils\Exception\UnexpectedNodeType;
use delphi\ParserUtils\Helper;
use PhpParser\Node;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\UnknownType;

/**
 * @coversDefaultClass \delphi\ParserUtils\Helper
 */
class HelperTest extends TestCase
{
    public function namespaceNameProvider()
    {
        $r   = [];
        $r[] = ['name', ['', 'name']];
        $r[] = ['\\\\name', ['', '\\\\name']];
        $r[] = ['base\\next', ['base', 'next']];
        $r[] = ['base\\next\\other', ['base\\next', 'other']];
        $r[] = ['base\\next\\more\\other', ['base\\next\\more', 'other']];
        return $r;
    }

    /**
     * @dataProvider namespaceNameProvider
     * @covers ::splitNamespaceName
     */
    public function testSplitNamespaceName($name, $expect)
    {
        $this->assertEquals($expect, Helper::splitNamespaceName($name));
    }

    /**
     * @covers ::typeDebug
     */
    public function testTypeDebugWithEmptyString()
    {
        $type = $this->createMock(\SebastianBergmann\Type\Type::class);
        $sut  = new Helper();
        $out  = $sut->typeDebug($type);
        $this->assertEquals(get_class($type), $out);
    }

    /**
     * @covers ::typeDebug
     */
    public function testTypeDebugWithString()
    {
        $typeString = 'CustomString';
        $type       = $this->createStub(\SebastianBergmann\Type\Type::class);
        $type->method('asString')->willReturn($typeString);

        $sut = new Helper();
        $out = $sut->typeDebug($type);
        $this->assertEquals($typeString, $out);
    }

    /**
     * @covers ::typeDebug
     */
    public function testTypeDebugWithUnknownType()
    {
        $type = new UnknownType();
        $sut  = new Helper();
        $out  = $sut->typeDebug($type);
        $this->assertEquals('unknown', $out);
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldJustUseName()
    {
        $expect     = 'expected';
        $node       = $this->createStub(Node::class);
        $node->name = $expect;

        $sut = new Helper();
        $this->assertEquals($expect, $sut->getIdentifier($node));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldUseNameForVariable()
    {
        $expect = '$var_name';
        $node   = new Node\Expr\Variable('var_name');

        $sut = new Helper();
        $this->assertEquals($expect, $sut->getIdentifier($node));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldUseNameForProperty()
    {
        $expect     = '$var_name->prop_name';
        $node       = $this->createStub(Node\Expr\PropertyFetch::class);
        $node->name = 'prop_name';
        $node->var  = new Node\Expr\Variable('var_name');

        $sut = new Helper();
        $this->assertEquals($expect, $sut->getIdentifier($node));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldHandleArrayAccess()
    {
        $expect  = '$var_name';
        $nodeVar = new Node\Expr\Variable('var_name');
        $node    = new Node\Expr\ArrayItem($nodeVar);

        $sut = new Helper();
        $this->assertEquals($expect, $sut->getIdentifier($node));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldHandleArrays()
    {
        $expect  = '[$var_name1, $var_name2, $var_name3, $var_name]';
        $items   = [];
        $items[] = new Node\Expr\Variable('var_name1');
        $items[] = new Node\Expr\Variable('var_name2');
        $items[] = new Node\Expr\Variable('var_name3');
        $items[] = new Node\Expr\ArrayItem(new Node\Expr\Variable('var_name'));
        $node    = new Node\Expr\Array_($items);

        $sut = new Helper();
        $this->assertEquals($expect, $sut->getIdentifier($node));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGetIdentifierShouldThrowOnUnexpected()
    {
        $node = $this->createStub(Node::class);
        $sut  = new Helper();
        $this->expectExceptionObject(new UnexpectedNodeType($node));
        $sut->getIdentifier($node);
    }
}
