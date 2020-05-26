<?php

namespace gendiff\Formatters\Plain;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType
};
use function gendiff\Formatters\Pretty\stringify;

function render(array $tree): string
{
    $filtered = array_filter($tree, fn($node) => getType($node) !== "unchanged");
    $filteredTree = array_map(
        function ($node) {
            if (getType($node) === "object") {
                $node["children"] = array_filter(getChildren($node), fn($child) => getType($child) !== "unchanged");
                return $node;
            }
            return $node;
        },
        $filtered
    );
    $rendered = buildPlainRender($filteredTree, '');
    return $rendered;
}

function buildPlainRender(array $tree, string $path = ""): string
{
    return implode(
        "\n",
        array_map(
            function ($node) use ($path) {
                $name = getName($node);
                $currentPath = "{$path}{$name}";
                $oldValue = is_object(getOldValue($node)) ? "complex value" : stringify(getOldValue($node));
                $newValue = is_object(getNewValue($node)) ? "complex value" : stringify(getNewValue($node));
                switch (getType($node)) {
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
                        $type = getType($node);
                        throw new \Exception("Unsupported type node {$type}");
                }
            },
            $tree
        )
    );
}
