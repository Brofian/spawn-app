<?php declare(strict_types=1);


namespace spawnCore\Custom\Gadgets;

/**
 * Class FileCrawler
 *
 * Allows you to simply check all files in a directory.
 * The Check function has to have the following Parameters:
 * - string (the content of the file)
 * - &array (the current array of results)
 * - string (the name of the currently selected file)
 * - string (the relative path of the currently selected file from the root)
 * - string (the relative path of the current file from the starting directory)
 *
 * In this Function, you can save infos with the $array, which is returned afterwards
 */
class FileCrawler
{

    /** @var callable $checkFunction */
    public $checkFunction;
    /** @var array $results */
    private $results = array();
    /** @var int */
    private $maxDepth = 999;

    /** @var array */
    private $ignored_dirs = [
        '.',
        '..'
    ];


    /**
     * @param string $dirname
     */
    public function addIgnoredDirName(string $dirname)
    {
        $this->ignored_dirs[] = $dirname;
    }


    /**
     * Searches in all files in the given path and its sub directories
     * @param string $rootPath
     * @param callable $checkFunction
     * @return array
     */
    public function searchInfos(string $rootPath, callable $checkFunction, int $maxDepth = 999): array
    {
        require_once(__DIR__ . "/../Gadgets/URIHelper.php");

        if (is_callable($checkFunction) == false) return [];
        $this->checkFunction = $checkFunction;

        $this->maxDepth = $maxDepth;

        $this->scanDirs($rootPath, $this->results);

        return $this->results;
    }

    /**
     * Loads all classes in the directory
     * @param string $current
     * @param array $classes
     * @return array
     */
    private function scanDirs(string $current, array &$ergs, int $depth = 0, string $relativePath = '/'): array
    {
        $currentContents = scandir($current);

        foreach ($currentContents as $content) {
            //skip relative folders and cache
            if (in_array($content, $this->ignored_dirs)) continue;
            //skip invisible folders
            if (substr($content, 0, 1) == '.') continue;

            //extend path with current content element
            $path = $current . '\\' . $content;
            URIHelper::pathifie($path);

            //check if content is file or directory
            if (is_file($path)) {

                //if class: load to classes array
                $fileContent = file_get_contents($path);

                $function = $this->checkFunction;
                $function($fileContent, $ergs, $content, $path, $relativePath);

            } else if (is_dir($path) && ($depth < $this->maxDepth || $this->maxDepth == -1)) {
                //if class is another dir, scan it
                $this->scanDirs($path, $ergs, $depth + 1, $relativePath . $content . '/');
            }

        }

        return $ergs;
    }

}