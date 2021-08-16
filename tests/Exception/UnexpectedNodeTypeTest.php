<?php

namespace delphi\Tests\ParserUtils\Exception;

use delphi\ParserUtils\Exception\UnexpectedNodeType;
use PhpParser\Node;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \delphi\ParserUtils\Exception\UnexpectedNodeType
 */
class UnexpectedNodeTypeTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruction()
    {

        $node = $this->createMock(Node::class);
        $sut  = new UnexpectedNodeType($node);
        $this->assertEquals('Unexpected Node Type: ' . get_class($node), $sut->getMessage());
    }

    /**
     * @covers ::getNode
     */
    public function testGetNode()
    {
        $node = $this->createMock(Node::class);
        $sut  = new UnexpectedNodeType($node);
        $this->assertSame($node, $sut->getNode());
    }
}
