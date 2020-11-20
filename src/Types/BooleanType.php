<?php

namespace U89Man\Dumper\Types;

class BooleanType
{
    /**
     * @param bool $bool
     *
     * @return string
     */
    public static function render($bool)
    {
        return '<span class="boolean" title="Тип: bool">'.($bool ? 'true' : 'false').'</span>';
    }
}

