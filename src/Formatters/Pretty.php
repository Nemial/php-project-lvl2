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

function stringify($value, $valueGap, $gap)
{
    if (is_bool($value)) {
        if ($value) {
            return 'true';
        } else {
            return 'false';
        }
    }

    if (is_object($value)) {
        $data = [];
        $currentValue = get_object_vars($value);
        foreach ($currentValue as $key => $value) {
            $data[] = "{$valueGap}{$key}: {$value}";
        }
        array_unshift($data, "{");
        array_push($data, "{$gap}}");
        return implode("\n", $data);
    }

    return $value;
}

function render(array $tree): string
{
    $rendered = buildPrettyRender($tree, 1);

    array_unshift($rendered, '{');
    array_push($rendered, '}');

    $flatten = flattenAll($rendered);
    $filtered = array_filter($flatten, fn($item) => !is_null($item));

    return implode("\n", $filtered);
}

function buildPrettyRender($tree, $multiplier): array
{
    return array_map(
        function ($node) use ($multiplier) {
            $name = getName($node);
            $currentIndent = COUNT_INDENT * $multiplier;
            $gap = str_repeat(" ", $currentIndent);
            $valueIndent = $currentIndent + COUNT_INDENT;
            $shortIndent = (COUNT_INDENT * $multiplier) - 2;
            $shortGap = str_repeat(" ", $shortIndent);
            $valueGap = str_repeat(" ", $valueIndent);
            $oldValue = stringify(getOldValue($node), $valueGap, $gap);
            $newValue = stringify(getNewValue($node), $valueGap, $gap);

            switch (getType($node)) {
                case "object":
                    $newMultiplier = $multiplier + 1;
                    $data = [];
                    $data[] = "{$gap}{$name}: {";
                    $children = buildPrettyRender(getChildren($node), $newMultiplier);
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
