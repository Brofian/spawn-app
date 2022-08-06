<?php

namespace SpawnCore\System\Custom\Gadgets;

use JsonException;

class JsonHelper {

    public static function arrayToJson(array $data, bool $throwOnError = true): string {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        }
        catch (JsonException $exception) {
            if($throwOnError) {
                throw $exception;
            }

            return '["error":"Could not parse data to JSON"]';
        }
    }

    public static function jsonToArray(string $json, bool $throwOnError = true): array {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (JsonException $exception) {
            if($throwOnError) {
                throw $exception;
            }
            return ["error" => "Could not parse JSON to array"];
        }
    }

    public static function validateJson(string $json): bool {
        try {
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return true;
        }
        catch (JsonException $exception) {
            return false;
        }
    }


}