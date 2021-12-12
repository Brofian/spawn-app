<?php

namespace spawnCore\Database\Entity\TableDefinition\Constants;

class ColumnTypeOptions {

    public const NOTNULL = 'notnull';
    public const DEFAULT = 'default';
    public const AUTOINCREMENT = 'autoincrement';
    public const LENGTH = 'length';
    public const FIXED = 'fixed';
    public const UNSIGNED = 'unsigned';
    public const PRECISION = 'precision';
    public const SCALE = 'scale';


    public const TYPE_OPTIONS = [
        ColumnTypes::SMALL_INT => [
            self::NOTNULL,
            self::DEFAULT,
            self::AUTOINCREMENT,
            self::UNSIGNED
        ],
        ColumnTypes::INTEGER => [
            self::NOTNULL,
            self::DEFAULT,
            self::AUTOINCREMENT,
            self::UNSIGNED
        ],
        ColumnTypes::BIG_INT => [
            self::NOTNULL,
            self::DEFAULT,
            self::AUTOINCREMENT,
            self::UNSIGNED
        ],
        ColumnTypes::DECIMAL => [
            self::NOTNULL,
            self::DEFAULT,
            self::PRECISION,
            self::SCALE
        ],
        ColumnTypes::FLOAT => [
            self::NOTNULL,
            self::DEFAULT,
            self::PRECISION,
            self::SCALE
        ],
        ColumnTypes::STRING => [
            self::NOTNULL,
            self::DEFAULT,
            self::LENGTH,
            self::FIXED
        ],
        ColumnTypes::TEXT => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::BINARY => [
            self::NOTNULL,
            self::DEFAULT,
            self::LENGTH,
            self::FIXED
        ],
        ColumnTypes::BLOB => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::BOOLEAN => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATE => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATE_IMMUTABLE => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATETIME => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATETIME_IMMUTABLE => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATETIME_TZ => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::DATETIME_TZ_IMMUTABLE => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::TIME => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::TIME_IMMUTABLE => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::ARRAY => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::SIMPLE_ARRAY => [
            self::NOTNULL,
            self::DEFAULT
        ],
        ColumnTypes::JSON => [
            self::NOTNULL,
            self::DEFAULT
        ],
    ];

    public static function getOptionsForType(string $type): array {
        if(!isset(self::TYPE_OPTIONS[$type])) {
            return [];
        }

        return self::TYPE_OPTIONS[$type];
    }

    public const OPTION_DEFAULTS = [
        self::NOTNULL => true,
        self::DEFAULT => null,
        self::AUTOINCREMENT => false,
        self::LENGTH => null,
        self::FIXED => false,
        self::UNSIGNED => false,
        self::PRECISION => 10,
        self::SCALE=> 0,
    ];

    public const OPTION_GETTER_SETTER = [
        'example' => ['getterMethod', 'setterMethod'],
        self::NOTNULL => ['getNotnull', 'setNotnull'],
        self::DEFAULT => ['getDefault', 'setDefault'],
        self::AUTOINCREMENT => ['getAutoincrement', 'setAutoincrement'],
        self::LENGTH => ['getLength', 'setLength'],
        self::FIXED => ['getFixed', 'setFixed'],
        self::UNSIGNED => ['getUnsigned', 'setUnsigned'],
        self::PRECISION => ['getPrecision', 'setPrecision'],
        self::SCALE=> ['getScale', 'setScale'],
    ];





}