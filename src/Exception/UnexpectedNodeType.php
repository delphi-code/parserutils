<?php

namespace delphi\ParserUtils\Exception;

use PhpParser\Node;

class UnexpectedNodeType extends \Exception
{
    protected Node $node;

    public function getNode(): Node
    {
        return $this->node;
    }

    public function __construct(Node $node)
    {
        $message    = sprintf('Unexpected Node Type: %s', get_class($node));
        $this->node = $node;
        parent::__construct($message);
    }
}
