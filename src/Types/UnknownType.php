<?php

namespace U89Man\Dumper\Types;

class UnknownType
{
    /**
     * @return string
     */
    public static function render()
    {
        return '<span class="unknown">Неизвестный тип</span>';
    }
}

