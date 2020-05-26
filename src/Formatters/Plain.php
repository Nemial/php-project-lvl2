<?php

namespace gendiff\Formatters\Plain;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType
};

function stringify($value)
{
    if (is_bool($value)) {
        if ($value) {
            return 'true';
        } else {
            return 'false';
        }
    }

    if (is_object($value)) {
        return "complex value";
    }

    return $value;
}

function render(array $tree): string
{
    return buildPlainRender($tree, '');
}

function buildPlainRender(array $tree, string $path = ""): string
{
    $filtered = array_filter($tree, fn($node) => getType($node) !== "unchanged");

    return implode(
        "\n",
        array_map(
            function ($node) use ($path) {
                $name = getName($node);
                $type = getType($node);
                $currentPath = "{$path}{$name}";
                $oldValue = stringify(getOldValue($node));
                $newValue = stringify(getNewValue($node));
                switch ($type) {
                    case "object":
                        $newPath = "{$currentPath}.";
                        return buildPlainRender(getChildren($node), $newPath);
                    case "changed":
                        return "Property '{$currentPath}' was changed. From '{$oldValue}' to '{$newValue}'";
                    case "added":
                        return "Property '{$currentPath}' was added with value: '{$newValue}'";
                    case "removed":
                        return "Property '{$currentPath}' was removed";
                    default:
                        throw new \Exception("Unsupported type node {$type}");
                }
            },
            $filtered
        )
    );
}
