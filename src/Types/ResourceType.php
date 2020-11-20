<?php

namespace U89Man\Dumper\Types;

use U89Man\Dumper\Dumper;

class ResourceType
{
    /**
     * @param resource $resource
     * @param Dumper $dumper
     *
     * @return string
     */
    public static function render(Dumper $dumper, $resource)
    {
        $out = '';
        $type = get_resource_type($resource);

        $out .= '<span class="object">';
        $out .= '<span class="resource" title="Ресурс">Resource </span>';
        $out .= '<span class="operator">: </span>';
        $out .= '<span class="type">'.$type.'</span> ';
        $out .= '<span class="braces">{</span>';

        $getData = str_replace('-', '', 'get'.ucfirst($type).'Data');

        if (method_exists(__CLASS__, $getData)) {
            $out .= '<a class="toggle">>></a>';
            $out .= '<span class="content">';

            foreach (self::$getData($resource) as $key => $value) {
                $out .= '<span class="row">';
                $out .= '<span class="property">'.$key.'</span>';
                $out .= '<span class="operator">: </span>';
                $out .= '<span class="type">'.$dumper->resolve($value).'</span> ';
                $out .= '</span>';
            }

            $out .= '</span>';
        }

        $out .= '<span class="brackets">}</span>';
        $out .= '</span>';

        return $out;
    }

    /**
     * @param resource $gd
     *
     * @return array
     */
    protected static function getGdData($gd)
    {
        return [
            'width' => imagesy($gd),
            'height' => imagesx($gd),
            'true_color' => imageistruecolor($gd),
        ];
    }

    /**
     * @param resource $curl
     *
     * @return array
     */
    protected static function getCurlData($curl)
    {
        return curl_getinfo($curl);
    }

    /**
     * @param resource $stream
     *
     * @return array
     */
    protected static function getStreamData($stream)
    {
        return stream_get_meta_data($stream) + array('context' => self::getStreamContextData($stream));
    }

    /**
     * @param resource $stream
     *
     * @return array
     */
    protected static function getStreamContextData($stream)
    {
        return stream_context_get_params($stream);
    }
}

