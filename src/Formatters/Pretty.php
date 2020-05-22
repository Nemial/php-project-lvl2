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

function render(array $tree): array
{
    $iter = function ($tree, $multiplier) use (&$iter) {
        return array_reduce(
            $tree,
            function ($acc, $node) use ($multiplier, $iter) {
                $name = getName($node);
                $currentIndent = COUNT_INDENT * $multiplier;
                $gap = str_repeat(" ", $currentIndent);

                if (getType($node) === "object") {
                    $newMultiplier = $multiplier + 1;
                    $gap = str_repeat(" ", $currentIndent);
                    $acc[] = "{$gap}{$name}: {";
                    $data = $iter(getChildren($node), $newMultiplier);
                    $acc[] = flattenAll($data);
                    $acc[] = "{$gap}}";
                    return $acc;
                }

                $oldValue = getOldValue($node);
                $isComplexValue = is_array($oldValue);
                $valueIndent = $currentIndent + COUNT_INDENT;
                $shortIndent = (COUNT_INDENT * $multiplier) - 2;
                $shortGap = str_repeat(" ", $shortIndent);
                $valueGap = str_repeat(" ", $valueIndent);

                switch (getType($node)) {
                    case "unchanged":
                        $acc[] = "{$gap}{$name}: {$oldValue}";
                        break;
                    case "changed":
                        $newValue = getNewValue($node);
                        $acc[] = "{$shortGap}+ {$name}: {$newValue}";
                        $acc[] = "{$shortGap}- {$name}: {$oldValue}";
                        break;
                    case "added":
                        if ($isComplexValue) {
                            $acc[] = "{$shortGap}+ {$name}: {";
                            foreach ($oldValue as $key => $value) {
                                $acc[] = "{$valueGap}{$key}: {$value}";
                            }
                            $acc[] = "{$gap}}";
                        } else {
                            $acc[] = "{$shortGap}+ {$name}: {$oldValue}";
                        }
                        break;
                    case "removed":
                        if ($isComplexValue) {
                            $acc[] = "{$shortGap}- {$name}: {";
                            foreach ($oldValue as $key => $value) {
                                $acc[] = "{$valueGap}{$key}: {$value}";
                            }
                            $acc[] = "{$gap}}";
                        } else {
                            $acc[] = "{$shortGap}- {$name}: {$oldValue}";
                        }
                        break;
                    default:
                        break;
                }
                return $acc;
            },
            []
        );
    };
    return $iter($tree, 1);
}
