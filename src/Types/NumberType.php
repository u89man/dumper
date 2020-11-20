<?php

namespace U89Man\Dumper\Types;

class NumberType
{
    /**
     * @param int|double $num
     *
     * @return string
     */
    public static function render($num)
    {
        if (is_double($num)) {
            if ($num == (int) $num) {
                $num .= '.0';
            }
            $type = 'double';
        } else {
            $type = 'int';
        }

        return '<span class="number" title="Тип: '.$type.'">'.$num.'</span>';
    }
}

