<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Gadgets;

use Exception;
use RuntimeException;

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
    public static function insert($path, $data): bool
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
     * @return int|bool
     */
    public static function createFile($path, $content = '')
    {
        self::createFolder(dirname(URIHelper::pathifie($path, "/", false)));
        try {
            return file_put_contents($path, $content);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public static function createFolder($path): bool
    {
        if (!file_exists($path)) {
            try {
                if (!mkdir($path, 0777, true) && !is_dir($path)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
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
    public static function append($path, $data): bool
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
    public static function deleteFile($path): bool
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

    public static function deleteFolder($path, $deleteContents = false): bool
    {
        if (!file_exists($path)) {
            //dir doesnt exist
            return true;
        }

        if (!is_dir($path)) {
            //path is not a directory
            return false;
        }

        if (self::isDirEmpty($path)) {
            //delete empty dir
            return rmdir($path);
        }

        if ($deleteContents) {
            //delete dir recursive
            rrmdir($path, false);
        }

        return false;
    }

    private static function isDirEmpty($path): bool
    {
        if (!file_exists($path)) {
            return true;
        }
        return count(scandir($path)) <= 0;
    }


}