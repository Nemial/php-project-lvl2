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
    $rendered = plainRender($tree, '');
    $flatten = flattenAll($rendered);
    $filtered = array_filter($flatten, fn($item) => !is_null($item));


    return implode("\n", $filtered) . "\n";
}

function plainRender(array $tree, string $path = ""): array
{
    return array_map(
        function ($node) use ($path) {
            $name = getName($node);
            $currentPath = $path === '' ? "{$name}" : "{$path}.{$name}";

            if (getType($node) === "object") {
                return plainRender(getChildren($node), $currentPath);
            }
            $oldValue = is_object(getOldValue($node)) ? "complex value" : getOldValue($node);
            switch (getType($node)) {
                case "changed":
                    $newValue = getNewValue($node);
                    return "Property '{$currentPath}' was changed. From '{$oldValue}' to '{$newValue}'";
                case "added":
                    return "Property '{$currentPath}' was added with value: '{$oldValue}'";
                case "removed":
                    return "Property '{$currentPath}' was removed";
                default:
                    break;
            }
        },
        $tree
    );
}
