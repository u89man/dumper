<?php

namespace U89Man\Dumper\Types;

use Closure;
use ReflectionFunction;
use ReflectionObject;
use U89Man\Dumper\Dumper;

class ObjectType
{
    /**
     * @var int
     */
    protected static $shortNamespaceLength = 5;

    /**
     * @var array
     */
    protected static $list = array();


    /**
     * @param Dumper $dumper
     * @param object $object
     *
     * @return string
     */
    public static function render(Dumper $dumper, $object)
    {
        $out  = '<span class="object">';
        $out .= self::renderClass($object);
        $out .= '<span class="hash" title="Уникальный идентификатор объекта"> #'.self::getObjectId($object).' </span>';
        $out .= '<span class="braces">{</span>';

        if (in_array($object, self::$list)) {
            $out .= '<span class="recursion" title="Рекурсия объекта"> + recursion + </span>';
        } else {
            $out .= '<a class="toggle">>></a>';
            $out .= '<span class="content">';

            array_push(self::$list, $object);

            switch (true) {
                case $object instanceof Closure:
                    $out .= self::renderClosure($object, $dumper);
                    break;
                default:
                    $out .= self::renderObject($object, $dumper);
            }

            array_pop(self::$list);

            $out .= '</span>';
        }

        $out .= '<span class="brackets">}</span>';
        $out .= '</span>';

        return $out;
    }


    /**
     * @param object $object
     *
     * @return string
     */
    protected static function renderClass($object)
    {
        $out = '';

        $class = get_class($object);
        $separator = strrpos($class, '\\');

        if ($separator > 0) {
            $namespace = substr($class, 0, $separator);
            $class = substr($class, $separator + 1);

            if (self::$shortNamespaceLength < strlen($namespace) - 3) {
                $shortNamespace = substr($namespace, 0, self::$shortNamespaceLength);
                $out .= '<span class="namespace" title="Пространство имен" data-ns="'.$namespace.'\\">';
                $out .= $shortNamespace.'...\\';
                $out .= '</span>';
            } else {
                $out .= '<span class="namespace" title="Пространство имен">'.$namespace.'\\</span>';
            }
        }

        $out .= '<span class="class" title="Класс">';
        $out .= $class;
        $out .= '</span>';

        return $out;
    }

    /**
     * @param object $obj
     *
     * @return int|string
     */
    protected static function getObjectId($obj)
    {
        if (version_compare('7.2', phpversion(), '<=')) {
            return spl_object_id($obj);
        } else {
            return substr(spl_object_hash($obj), -4);
        }
    }


    /**
     * @param object $object
     * @param Dumper $dumper
     *
     * @return string
     */
    protected static function renderObject($object, Dumper $dumper)
    {
        $out = '';
        $reflection = new ReflectionObject($object);

        foreach ($reflection->getProperties() as $property) {
            $modifier = '';
            $title = '';

            switch (true) {
                case ! $property->isDefault():
                    $modifier = '=';
                    $title = 'default';
                    break;
                case $property->isPublic():
                    $modifier = '+';
                    $title = 'public';
                    break;
                case $property->isPrivate():
                    $modifier = '-';
                    $title = 'private';
                    break;
                case $property->isProtected():
                    $modifier = '#';
                    $title = 'protected';
                    break;
            }

            $property->setAccessible(true);

            $out .= '<span class="row">';

            $out .= $property->isStatic()
                ? '<span class="modifier" title="'.$title.' static">('.$modifier.')</span>'
                : '(<span class="modifier" title="'.$title.'">'.$modifier.'</span>)';

            $out .= ' ';
            $out .= '<span class="property">$'.$property->getName().'</span>';
            $out .= '<span class="operator"> = </span>';
            $out .= $dumper->resolve($property->getValue($object));
            $out .= '</span>';
        }

        return $out;
    }

    /**
     * @param object $object
     * @param Dumper $dumper
     *
     * @return string
     */
    protected static function renderClosure($object, Dumper $dumper)
    {
        $out = '';
        $reflection = new ReflectionFunction($object);

        // Имя файла
        $out .= '<span class="row">';
        $out .= '<span class="property">file</span>';
        $out .= '<span class="operator">: </span>';
        $out .= '<span class="string">"'.$reflection->getFileName().'"</span>';
        $out .= '</span>';

        // Номера строк
        $start = $reflection->getStartLine();
        $end = $reflection->getEndLine();

        $out .= '<span class="row">';
        $out .= '<span class="property">'.($start < $end ? 'lines' : 'line').'</span>';
        $out .= '<span class="operator">: </span>';
        $out .= '<span class="number">'.($start < $end ? $start.'-'.$end : $start).'</span>';
        $out .= '</span>';

        // Входные параметры
        $out .= self::renderVariable($reflection->getParameters(), 'parameters', $dumper);

        // Статические переменные
        $out .= self::renderVariable($reflection->getStaticVariables(), 'use', $dumper);

        // Возвращаемый тип
        if ($type = $reflection->getReturnType()) {
            $out .= '<span class="row">';
            $out .= '<span class="property">return</span>';
            $out .= '<span class="operator">: </span>';
            $out .= '<span class="type">'.$type.'</span>';
            $out .= '</span>';
        }

        return $out;
    }

    /**
     * @param array $vars
     * @param string $type
     * @param Dumper $dumper
     *
     * @return string
     */
    protected static function renderVariable(array $vars, $type, Dumper $dumper)
    {
        $out = '';
        $count = count($vars);

        if ($count > 0) {
            $out .= '<span class="row">';
            $out .= '<span class="property">'.$type.'</span>';
            $out .= '<span class="operator">: </span>';
            $out .= '<span class="braces">{</span>';
            $out .= '<a class="toggle">>></a>';
            $out .= '<span class="content">';

            switch ($type) {
                case 'parameters':
                    foreach ($vars as $param) {
                        $out .= '<span class="row">';
                        $pType = ($pType = $param->getType()) ? $pType->getName() : '';
                        $out .= '<span class="property" title="'.$pType.'">$'.$param->getName().'</span>';
                        $out .= '<span class="operator"> = </span>';
                        if ($param->isDefaultValueAvailable()) {
                            $out .= $dumper->resolve($param->getDefaultValue());
                        }
                        $out .= '</span>';
                    }
                    break;
                case 'use':
                    foreach ($vars as $key => $value) {
                        $out .= '<span class="row">';
                        $out .= '<span class="property">$'.$key.'</span>';
                        $out .= '<span class="operator"> = </span>';
                        $out .= $dumper->resolve($value);
                        $out .= '</span>';
                    }
                    break;
            }

            $out .= '</span>';
            $out .= '<span class="braces">}</span>';
            $out .= '</span>';
        }

        return $out;
    }
}

