<?php

namespace SpawnCore\System\Custom\Gadgets;

use Exception;

class FileEditor
{

    /**
     * @param $path
     * @return bool|false|string
     */
    public static function getFileContent($path)
    {
        if (!file_exists($path) || !is_file($path)) {
            return false;
        }

        return file_get_contents($path);
    }

    /**
     * @param $path
     * @param $data
     * @return bool
     */
    public static function insert($path, $data)
    {
        self::createFile($path);
        try {
            file_put_contents($path, $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $path
     * @param string $content
     * @return bool
     */
    public static function createFile($path, $content = ''): bool
    {
        self::createFolder(dirname(URIHelper::pathifie($path, "/", false)));

        try {
            file_put_contents($path, $content);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public static function createFolder($path)
    {
        if (!file_exists($path)) {
            try {
                mkdir($path, 0777, true);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $path
     * @param $data
     * @return bool
     */
    public static function append($path, $data)
    {
        self::createFolder(dirname($path));
        try {
            file_put_contents($path, $data, FILE_APPEND);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * @param $path
     * @return bool
     */
    public static function deleteFile($path)
    {
        if (file_exists($path)) {
            try {
                return unlink($path);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    public static function deleteFolder($path, $deleteContents = false)
    {
        if (!file_exists($path)) {
            //dir doesnt exist
            return true;
        } else if (!is_dir($path)) {
            //path is not a directory
            return false;
        }

        if (self::isDirEmpty($path)) {
            //delete empty dir
            return rmdir($path);
        } else if ($deleteContents) {
            //delete dir recursive
            rrmdir($path, false);
        }

        return false;
    }

    private static function isDirEmpty($path)
    {
        if (!file_exists($path)) return true;
        return count(scandir($path)) <= 0;
    }


}