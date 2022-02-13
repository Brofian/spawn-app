<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;


use bin\spawn\IO;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;
use SpawnCore\Defaults\Commands\ListModulesCommand;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleNamespacer;

class ScssHelper
{

    public const SCSS_FILES_PATH = ROOT . '/vendor/scssphp/scssphp/scss.inc.php';
    public string $cacheFilePath = ROOT . '/public/cache';
    public string $baseFolder = ROOT . CACHE_DIR . '/resources/modules/scss';
    private bool $alwaysReload = false;
    private array $baseVariables = array();

    public function __construct()
    {
        $this->alwaysReload = (MODE === 'dev');
        require_once self::SCSS_FILES_PATH;
    }

    public function cacheExists(): bool
    {
        return file_exists($this->cacheFilePath);
    }

    public function createCss(?string $selectedNamespace = null)
    {
        $moduleCollection = ListModulesCommand::getModuleList();
        $namespaces = NamespaceHelper::getNamespacesFromModuleCollection($moduleCollection);

        foreach($namespaces as $namespace => $moduleList) {
            if($selectedNamespace && $selectedNamespace !== $namespace) {
                continue;
            }

            $this->setBaseVariable("asset-path", '/cache/'.ModuleNamespacer::hashNamespace($namespace));
            $baseFile = $this->baseFolder . '/' . $namespace . '_index.scss';

            if(file_exists($baseFile)) {
                $css = $this->compile($baseFile);
                $cssMinified = $this->compile($baseFile, true);

                $hashedNamespace = ModuleNamespacer::hashNamespace($namespace);
                $targetFolder = $this->cacheFilePath . '/' . $hashedNamespace . '/css';

                //create output file
                /** @var FileEditor $fileWriter */
                $fileWriter = new FileEditor();
                $fileWriter::createFolder($targetFolder);
                $fileWriter::createFile($targetFolder.'/all.css', $css->getCss());
                $fileWriter::createFile($targetFolder.'/all.min.css', $cssMinified->getCss());

                IO::printLine(IO::TAB . '- ' . $namespace, '', 1);
            }

        }

    }

    private function compile(string $baseFile, bool $compressed = false)
    {
        $scss = new Compiler();

        //set the output style
        $outputStyle = $compressed ? OutputStyle::COMPRESSED : OutputStyle::EXPANDED;
        $scss->setOutputStyle($outputStyle);

        $this->registerFunctions($scss);

        $baseVariables = $this->compileBaseVariables();

        //set Base path for files
        $scss->setImportPaths([dirname($baseFile)]);

        try {
            $css = $scss->compileString('
              ' . $baseVariables . '
              @import "' . basename($baseFile) . '";
            ');
        } catch (SassException $e) {
            $css = "";

            if (MODE === 'dev') {
                Debugger::ddump($e);
            }
        }


        return $css;
    }

    private function registerFunctions(Compiler $scss)
    {
        //register custom scss functions
        $scss->registerFunction(
            'degToPadd',
            function ($args) {
                $deg = $args[0][1];
                $a = $args[1][1];


                $magicNumber = tan(deg2rad($deg) / 2);
                $contentWidth = $a;

                $erg = $magicNumber * $contentWidth;
                return $erg . "px";
            }
        );


        $scss->registerFunction(
            'assetURL',
            function ($args) {
                $path = $args[0][1];
                $fullpath = ROOT . 'src/Resources/public/assets/' . $path;

                return "url('" . $fullpath . "')";
            }
        );

    }

    private function compileBaseVariables()
    {
        $result = "";

        foreach ($this->baseVariables as $name => $value) {
            $result .= '$' . $name . ' : "' . $value . '";' . PHP_EOL;
        }

        return $result;
    }

    public function setBaseVariable(string $name, string $value)
    {
        $this->baseVariables[$name] = $value;
    }
}