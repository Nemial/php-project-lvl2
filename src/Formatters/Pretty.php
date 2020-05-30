<?php

namespace gendiff\Formatters\Pretty;

use function gendiff\Ast\{
    getChildren,
    getName,
    getOldValue,
    getNewValue,
    getType,
};

const COUNT_INDENT = 4;

function stringify($value, $deep)
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
        $imploded = implode("\n", $data);
        return "{\n{$imploded}\n{$gap}}";
    }

    return $value;
}

function render(array $tree): string
{
    $builded = build($tree);

    return "{\n{$builded}\n}";
}

function build($tree, $depth = 1): string
{
    return implode(
        "\n",
        array_map(
            function ($node) use ($depth) {
                $name = getName($node);
                $currentIndent = COUNT_INDENT * $depth;
                $gap = str_repeat(" ", $currentIndent);
                $shortIndent = (COUNT_INDENT * $depth) - 2;
                $shortGap = str_repeat(" ", $shortIndent);
                $oldValue = stringify(getOldValue($node), $depth);
                $newValue = stringify(getNewValue($node), $depth);

                switch (getType($node)) {
                    case "object":
                        $newDepth = $depth + 1;
                        $children = build(getChildren($node), $newDepth);
                        return implode("\n", ["{$gap}{$name}: {", $children, "{$gap}}"]);
                    case "unchanged":
                        return "{$gap}{$name}: {$oldValue}";
                    case "changed":
                        return implode(
                            "\n",
                            ["{$shortGap}+ {$name}: {$newValue}", "{$shortGap}- {$name}: {$oldValue}"]
                        );
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
        )
    );
}
