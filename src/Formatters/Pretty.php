<?php

namespace gendiff\Formatters\Pretty;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType,
};
use function Funct\Collection\flattenAll;

const COUNT_INDENT = 4;

function stringify($value, $deep): string
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }

    if (is_object($value)) {
        $currentValue = get_object_vars($value);
        $currentIndent = COUNT_INDENT * $deep;
        $gap = str_repeat(" ", $currentIndent);
        $valueIndent = $currentIndent + COUNT_INDENT;
        $valueGap = str_repeat(" ", $valueIndent);
        $keys = array_keys($currentValue);
        $data = array_map(
            function ($key) use ($currentValue, $valueGap) {
                $value = $currentValue[$key];
                return "{$valueGap}{$key}: {$value}";
            },
            $keys
        );
        return "{" . "\n" . implode("\n", $data) . "\n" . "{$gap}}";
    }

    return $value;
}

function render(array $tree): string
{
    $builded = build($tree, 1);

    $flatten = flattenAll($builded);
    $filtered = array_filter($flatten, fn($item) => !is_null($item));

    return "{" . "\n" . implode("\n", $filtered) . "\n" . "}";
}

function build($tree, $deep): array
{
    return array_map(
        function ($node) use ($deep) {
            $name = getName($node);
            $currentIndent = COUNT_INDENT * $deep;
            $gap = str_repeat(" ", $currentIndent);
            $shortIndent = (COUNT_INDENT * $deep) - 2;
            $shortGap = str_repeat(" ", $shortIndent);
            $oldValue = stringify(getOldValue($node), $deep);
            $newValue = stringify(getNewValue($node), $deep);

            switch (getType($node)) {
                case "object":
                    $newDeep = $deep + 1;
                    $data = [];
                    $data[] = "{$gap}{$name}: {";
                    $children = build(getChildren($node), $newDeep);
                    $data[] = $children;
                    $data[] = "{$gap}}";
                    return $data;
                case "unchanged":
                    return "{$gap}{$name}: {$oldValue}";
                case "changed":
                    $data = [];
                    $data[] = "{$shortGap}+ {$name}: {$newValue}";
                    $data[] = "{$shortGap}- {$name}: {$oldValue}";
                    return $data;
                case "added":
                    return "{$shortGap}+ {$name}: {$newValue}";
                case "removed":
                    return "{$shortGap}- {$name}: {$oldValue}";
                default:
                    $type = getType($node);
                    throw new \Exception("Unsupported type node {$type}");
            }
        },
        $tree
    );
}
