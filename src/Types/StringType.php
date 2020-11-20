<?php

namespace U89Man\Dumper\Types;

class StringType
{
    /**
     * @var string
     */
    protected static $charset = 'UTF-8';

    /**
     * @var int
     */
    protected static $maxlength = 60;


    /**
     * @param string $string
     *
     * @return string
     */
    public static function render($string)
    {
        $length = mb_strlen($string, self::$charset);
        $string = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, self::$charset);

        $last = substr(strval($length), -1);

        switch ($last) {
            case '1':
                $ends = 'символ';
                break;
            case '2':
            case '3':
            case '4':
                $ends = 'символа';
                break;
            default:
                $ends = 'символов';
        }

        $out = '<span class="string" title="Строка: '.$length.' '.$ends.'">';

        if ($length > self::$maxlength) {
            $collapse = self::replaceNel($string);
            $expand = self::replaceNel(mb_substr($string, 0, self::$maxlength - 1, self::$charset));

            $out .= '<span class="collapse">"'.$collapse.'" </span>';
            $out .= '<span class="expand">"'.$expand.'..." </span>';
            $out .= '<a class="toggle">>></a>';
        } else {
            $out .= '"'.self::replaceNel($string).'"';
        }

        $out .= '</span>';

        return $out;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected static function replaceNel($string)
    {
        return str_replace(
            [
                "\r\n",
                "\r",
                "\n"
            ],
            [
                '<span class="nel" title="Windows">\r\n</span><br>',
                '<span class="nel" title="MacOS">\r</span><br>',
                '<span class="nel" title="Unix">\n</span><br>',
            ],
            $string
        );
    }
}

