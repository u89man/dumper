<?php

namespace U89Man\Dumper;

use U89Man\Dumper\Types\ArrayType;
use U89Man\Dumper\Types\BooleanType;
use U89Man\Dumper\Types\NullType;
use U89Man\Dumper\Types\NumberType;
use U89Man\Dumper\Types\ObjectType;
use U89Man\Dumper\Types\ResourceType;
use U89Man\Dumper\Types\StringType;
use U89Man\Dumper\Types\UnknownType;

class Dumper
{
    /**
     * @var int
     */
    protected static $id = 0;

    /**
     * @var bool
     */
    protected static $resourcesLoaded = false;


    /**
     * @param mixed $var
     *
     * @return void
     */
    public function dump($var)
    {
        $out = '';

        if (! self::$resourcesLoaded) {
            $css = file_get_contents(__DIR__.'/Resources/Css/light_style.css');
            $out .= join(PHP_EOL, array('<style>', trim($css), '</style>', ''));
            $js = file_get_contents(__DIR__.'/Resources/Js/script.js');
            $out .= join(PHP_EOL, array('<script>', trim($js), '</script>', ''));
            self::$resourcesLoaded = true;
        }

        self::$id++;

        $out .= '<div id="'.self::$id.'" class="u89m_dump">';
        $out .= $this->resolve($var);
        $out .= '<script>u89mInit('.self::$id.')</script>';
        $out .= '</div>';

        echo $out.PHP_EOL;
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    public function resolve($var)
    {
        switch (strtolower(gettype($var))) {
            case 'null':
                return NullType::render();
            case 'boolean':
                return BooleanType::render($var);
            case 'integer':
            case 'double':
                return NumberType::render($var);
            case 'string':
                return StringType::render($var);
            case 'array':
                return ArrayType::render($this, $var);
            case 'object':
                return ObjectType::render($this, $var);
            case 'resource':
                return ResourceType::render($this, $var);
            default:
                return UnknownType::render();
        }
    }
}

