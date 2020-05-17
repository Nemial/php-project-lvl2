<?php

namespace gendiff\Core;

const COUNT_INDENT = 2;

use function gendiff\Ast\{
    generateAst,
    getChildren,
    getDeep,
    getName,
    getOldValue,
    getNewValue,
    getType,
    getStatus,
    haveChildren
};
use function Funct\Collection\flattenAll;

function render($tree)
{
    return array_reduce(
        $tree,
        function ($acc, $node) {
            $name = getName($node);
            $oldValue = getOldValue($node);
            if (getType($node) === "node") {
                $gap = str_repeat(" ", COUNT_INDENT * getDeep($node));
                switch (getStatus($node)) {
                    case "unchanged":
                        $acc[] = "  {$gap}{$name}: {";
                        if (haveChildren($node)) {
                            $acc[] = render(getChildren($node));
                        } else {
                            $key = array_key_first($oldValue);
                            $acc[] = "{$gap} {$key}: {$oldValue[$key]}";
                        }
                        $acc[] = "{$gap}  }";
                        break;
                    case "removed":
                        $acc[] = "{$gap}- {$name}: {";
                        $key = array_key_first($oldValue);
                        $acc[] = "{$gap}      {$key}: {$oldValue[$key]}";
                        $acc[] = "{$gap}  }";
                        break;
                    case "added":
                        $acc[] = "{$gap}+ {$name}: {";
                        $key = array_key_first($oldValue);
                        $acc[] = "{$gap}      {$key}: {$oldValue[$key]}";
                        $acc[] = "{$gap}  }";
                        break;
                    default:
                        break;
                }
                return $acc;
            } elseif (getType($node) === "leaf") {
                $divisor = 2;
                $gap = str_repeat(" ", COUNT_INDENT * (getDeep($node) / $divisor));
                switch (getStatus($node)) {
                    case "unchanged":
                        $acc[] = "{$gap}     {$name}: {$oldValue}";
                        break;
                    case "changed":
                        $newValue = getNewValue($node);
                        $acc[] = "{$gap}   + {$name}: {$newValue}";
                        $acc[] = "{$gap}   - {$name}: {$oldValue}";
                        break;
                    case "removed":
                        $acc[] = "{$gap}   - {$name}: {$oldValue}";
                        break;
                    case "added":
                        $acc[] = "{$gap}   + {$name}: {$oldValue}";
                        break;
                    default:
                        break;
                }
                return $acc;
            }
        },
        []
    );
}

function genDiff(object $firstFile, object $secondFile)
{
    $ast = generateAst($firstFile, $secondFile);
    $diffMap = render($ast);
    array_unshift($diffMap, '{');
    array_push($diffMap, '}');
    $flatten = flattenAll($diffMap);

    return implode(PHP_EOL, $flatten) . PHP_EOL;
}
