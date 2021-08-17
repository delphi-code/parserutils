<?php

namespace delphi\ParserUtils;

use delphi\ParserUtils\Exception\UnexpectedNodeType;
use PhpParser\Node;
use SebastianBergmann\Type\Type;
use SebastianBergmann\Type\UnknownType;

class Helper
{

    /**
     * @param $namespaced_name
     *
     * @return array
     */
    public static function splitNamespaceName($namespaced_name): array
    {
        if (strpos($namespaced_name, '\\')) {
            $div = strrpos($namespaced_name, '\\');
            return [
                substr($namespaced_name, 0, $div),
                substr($namespaced_name, $div + 1),
            ];
        }

        return ['', $namespaced_name];
    }

    public function getIdentifier(Node $node): string
    {
        if (isset($node->name)) {
            $name = (string) $node->name;
            if ($node instanceof Node\Expr\Variable) {
                $name = '$' . $name;
            }
            if ($node instanceof Node\Expr\PropertyFetch) {
                $l = $this->getIdentifier($node->var);
                $name = $l . '->' . $name;
            }
            return $name;
        }

        if ($node instanceof Node\Expr\ArrayItem) {
            return $this->getIdentifier($node->value);
        }

        if ($node instanceof Node\Expr\Array_) {
            $contents = implode(', ', array_map(fn($i) => $this->getIdentifier($i), $node->items));
            return sprintf('[%s]', $contents);
        }

        throw new UnexpectedNodeType($node);
    }

    public function typeDebug(Type $type) {
        if ($type instanceof UnknownType) {
            return 'unknown';
        }
        $asString = $type->asString();
        if (empty($asString)) {
            return get_class($type);
        }
        return $asString;
    }
}
