<?php

namespace delphi\ParserUtils;

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
}
