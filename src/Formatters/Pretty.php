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
        return get_object_vars($value);
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
            $oldValue = stringify(getOldValue($node));
            $newValue = stringify(getNewValue($node));
            $isComplexOldValue = is_array($oldValue);
            $isComplexNewValue = is_array($newValue);
            $valueIndent = $currentIndent + COUNT_INDENT;
            $shortIndent = (COUNT_INDENT * $multiplier) - 2;
            $shortGap = str_repeat(" ", $shortIndent);
            $valueGap = str_repeat(" ", $valueIndent);

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
                    if ($isComplexNewValue) {
                        $data = [];
                        $data[] = "{$shortGap}+ {$name}: {";
                        foreach ($newValue as $key => $value) {
                            $data[] = "{$valueGap}{$key}: {$value}";
                        }
                        $data[] = "{$gap}}";
                        return $data;
                    } else {
                        return "{$shortGap}+ {$name}: {$newValue}";
                    }
                case "removed":
                    if ($isComplexOldValue) {
                        $data = [];
                        $data[] = "{$shortGap}- {$name}: {";
                        foreach ($oldValue as $key => $value) {
                            $data[] = "{$valueGap}{$key}: {$value}";
                        }
                        $data[] = "{$gap}}";
                        return $data;
                    } else {
                        return "{$shortGap}- {$name}: {$oldValue}";
                    }
                default:
                    $type = getType($node);
                    throw new \Exception("Unsupported type node {$type}");
            }
        },
        $tree
    );
}
