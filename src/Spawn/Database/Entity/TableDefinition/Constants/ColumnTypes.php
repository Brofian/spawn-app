<?php

namespace spawnCore\Database\Entity\TableDefinition\Constants;

class ColumnTypes {

    /** INTEGER NUMBERS  */
    //2 Bytes
    public const SMALL_INT = 'smallint';
    //4 Bytes
    public const INTEGER = 'integer';
    //8 Bytes
    public const BIG_INT = 'bigint';


    /** DECIMAL NUMBERS */
    //converted to php´s string type (or null)
    public const DECIMAL = 'decimal';
    //converted to php´s double or float type (or null)
    public const FLOAT = 'float';


    /** STRINGS */
    //data <= maximum length
    public const STRING = 'string';
    //no maximum length
    public const TEXT = 'text';


    /** BINARY */
    //data <= maximum length
    public const BINARY = 'binary';
    //no maximum length
    public const BLOB = 'blob';


    /** BIT TYPES */
    public const BOOLEAN = 'boolean';


    /** DATE AND TIME */
    //saves date, but no time or timezone information
    public const DATE = 'date';
    //same as date, but converts to php´s DateImmutable
    public const DATE_IMMUTABLE = 'date_immutable';
    //saves date and time, but no timezone information
    public const DATETIME = 'datetime';
    //same as datetime, but converts to php´s DateTimeImmutable
    public const DATETIME_IMMUTABLE = 'datetime_immutable';
    //saves date, time and timezone
    public const DATETIME_TZ = 'datetimetz';
    //same as datetimetz, but converts to php´s DateTimeImmutable
    public const DATETIME_TZ_IMMUTABLE = 'datetimetz_immutable';
    //saves time, but no date or timezone information
    public const TIME = 'time';
    //same as time, but converts to php´s DateTimeImmutable
    public const TIME_IMMUTABLE = 'time_immutable';


    /** ARRAY TYPES */
    //saves a php array based on serialisation
    public const ARRAY = 'array';
    //saves a php array based on the implode and explode functions
    public const SIMPLE_ARRAY = 'simple_array';
    //saves valid a php array by converting it into json format based on json_encode and json_decode
    public const JSON = 'json';





}