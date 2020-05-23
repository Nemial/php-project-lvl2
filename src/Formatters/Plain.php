<?php

namespace gendiff\Formatters\Plain;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType
};
use function Funct\Collection\flattenAll;

function render(array $tree): string
{
    $iter = function ($tree, $path) use (&$iter) {
        return array_reduce(
            $tree,
            function ($acc, $node) use ($path, $iter) {
                $name = getName($node);
                $currentPath = $path === '' ? "{$name}" : "{$path}.{$name}";
                if (getType($node) === "object") {
                    $acc[] = $iter(getChildren($node), $currentPath);
                    return $acc;
                }
                $oldValue = is_array(getOldValue($node)) ? "complex value" : getOldValue($node);
                switch (getType($node)) {
                    case "changed":
                        $newValue = getNewValue($node);
                        $acc[] = "Property '{$currentPath}' was changed. From '{$oldValue}' to '{$newValue}'";
                        break;
                    case "added":
                        $acc[] = "Property '{$currentPath}' was added with value: '{$oldValue}'";
                        break;
                    case "removed":
                        $acc[] = "Property '{$currentPath}' was removed";
                        break;
                    default:
                        break;
                }
                return $acc;
            },
            []
        );
    };


    $rendered = $iter($tree, '');
    $flatten = flattenAll($rendered);

    return implode("\n", $flatten) . "\n";
}
