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
    $rendered = prettyRender($tree, 1);

    array_unshift($rendered, '{');
    array_push($rendered, '}');

    $flatten = flattenAll($rendered);
    $filtered = array_filter($flatten, fn($item) => !is_null($item));

    return implode("\n", $filtered) . "\n";
}

function prettyRender($tree, $multiplier): array
{
    return array_map(
        function ($node) use ($multiplier) {
            $name = getName($node);
            $currentIndent = COUNT_INDENT * $multiplier;
            $gap = str_repeat(" ", $currentIndent);

            if (getType($node) === "object") {
                $newMultiplier = $multiplier + 1;
                $data = [];
                $data[] = "{$gap}{$name}: {";
                $children = prettyRender(getChildren($node), $newMultiplier);
                $data[] = flattenAll($children);
                $data[] = "{$gap}}";
                return flattenAll($data);
            }

            $oldValue = stringify(getOldValue($node));
            $isComplexValue = is_array($oldValue);
            $valueIndent = $currentIndent + COUNT_INDENT;
            $shortIndent = (COUNT_INDENT * $multiplier) - 2;
            $shortGap = str_repeat(" ", $shortIndent);
            $valueGap = str_repeat(" ", $valueIndent);

            switch (getType($node)) {
                case "unchanged":
                    return "{$gap}{$name}: {$oldValue}";
                case "changed":
                    $newValue = stringify(getNewValue($node));
                    $data = [];
                    $data[] = "{$shortGap}+ {$name}: {$newValue}";
                    $data[] = "{$shortGap}- {$name}: {$oldValue}";
                    return flattenAll($data);
                case "added":
                    if ($isComplexValue) {
                        $data = [];
                        $data[] = "{$shortGap}+ {$name}: {";
                        foreach ($oldValue as $key => $value) {
                            $data[] = "{$valueGap}{$key}: {$value}";
                        }
                        $data[] = "{$gap}}";
                        return flattenAll($data);
                    } else {
                        return "{$shortGap}+ {$name}: {$oldValue}";
                    }
                case "removed":
                    if ($isComplexValue) {
                        $data = [];
                        $data[] = "{$shortGap}- {$name}: {";
                        foreach ($oldValue as $key => $value) {
                            $data[] = "{$valueGap}{$key}: {$value}";
                        }
                        $data[] = "{$gap}}";
                        return flattenAll($data);
                    } else {
                        return "{$shortGap}- {$name}: {$oldValue}";
                    }
                    break;
                default:
                    break;
            }
        },
        $tree
    );
}
