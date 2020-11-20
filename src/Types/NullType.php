<?php

namespace U89Man\Dumper\Types;

class NullType
{
    /**
     * @return string
     */
    public static function render() 
    {
        return '<span class="null">null</span>';
    }
}


