<?php

namespace spawnCore\ServiceSystem;

use ReflectionClass;

class ServiceProperties
{

    public const _ID = 'id';
    public const _CLASS = 'class';
    public const _STATIC = 'static';
    public const _INSTANCE = 'instance';
    public const _DECORATES = 'decorates';
    public const _PARENT = 'parent';
    public const _ABSTRACT = 'abstract';
    public const _TAGS = 'tags';
    public const _MODULE_ID = 'module_id';
    public const _ARGUMENTS = 'arguments';

    public static function getPropertyGetterMethods(): array
    {
        $getterMethods = [];
        foreach (self::getPropertyList() as $property => $value) {
            $getMethodName = str_replace('_', '', ucwords('get_' . $value, '_'));
            $getterMethods[$value] = $getMethodName;
        }
        return $getterMethods;
    }

    public static function getPropertyList(): array
    {
        $oClass = new ReflectionClass(static::class);
        return $oClass->getConstants();
    }

    public static function getPropertySetterMethods(): array
    {
        $getterMethods = [];
        foreach (self::getPropertyList() as $property => $value) {
            $getMethodName = str_replace('_', '', ucwords('set_' . $value, '_'));
            $getterMethods[$value] = $getMethodName;
        }
        return $getterMethods;
    }


}