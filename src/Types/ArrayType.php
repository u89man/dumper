<?php

namespace U89Man\Dumper\Types;

use U89Man\Dumper\Dumper;

class ArrayType
{
    /**
     * @var array
     */
    protected static $list = array();


    /**
     * @param Dumper $dumper
     * @param array $arr
     *
     * @return string
     */
    public static function render(Dumper $dumper, $arr)
    {
        $count = count($arr);
        $last = substr(strval($count), -1);

        switch ($last) {
            case '1':
                $ends = 'элемент';
                break;
            case '2':
            case '3':
            case '4':
                $ends = 'элемента';
                break;
            default:
                $ends = 'элементов';
        }

        $out = '<span class="array" title="Массив: '.$count.' '.$ends.'">';
        $out .= '<span class="brackets">[</span>';

        if (in_array($arr, self::$list)) {
            $out .= '<span class="recursion" title="Рекурсия массива"> + recursion + </span>';
        } else {
            if ($count > 0) {
                $out .= '<a class="toggle">>></a>';
                $out .= '<span class="content">';

                array_push(self::$list, $arr);

                foreach ($arr as $key => $value) {
                    $out .= '<span class="row" title="">';

                    $out .= is_numeric($key)
                        ? '<span class="number">'.$key.'</span>'
                        : '<span class="string">"'.$key.'"</span>';

                    $out .= '<span class="operator"> => </span>';
                    $out .= $dumper->resolve($value);
                    $out .= '</span>';
                }

                array_pop(self::$list);

                $out .= '</span>';
            }
        }

        $out .= '<span class="brackets">]</span>';
        $out .= '</span>';

        return $out;
    }
}

