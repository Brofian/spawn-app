<?php declare(strict_types=1);


namespace spawnCore\Custom\Gadgets;


use Exception;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\Custom\Collection\AssociativeCollection;
use spawnCore\Custom\RenderExtensions\ExtensionLoader;
use spawnCore\Custom\Throwables\TwigRenderException;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\ServiceSystem\ServiceContainerProvider;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigHelper
{
    const CACHE_FOLDER_PATH = ROOT . '/var/cache/private/twig';

    protected string $targetFile = 'base.html.twig';
    protected array $templateDirs = array();
    protected ?string $customoutput = null;
    protected Environment $twig;
    protected AssociativeCollection $context;
    protected bool $isDevEnvironment = false;


    public function __construct()
    {
        $this->context = new AssociativeCollection();
        $this->isDevEnvironment = (MODE == 'dev');
        $this->loadTwig();
    }

    protected function loadTwig()
    {
        $moduleCollection = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.modules.collection');
        $this->loadTemplateDirFromModuleCollection($moduleCollection);

        $loader = new FilesystemLoader($this->templateDirs);
        $twig = new Environment($loader, [
            'debug' => (MODE == "dev"),
            'cache' => self::CACHE_FOLDER_PATH,
        ]); //<- Twig environment


        if (is_object($twig) == false) {
            Debugger::ddump("Couldn´t load Twig");
        }

        ExtensionLoader::loadTwigExtensions($twig);
        $twig->addExtension(new DebugExtension());

        $this->twig = $twig;
    }

    public function loadTemplateDirFromModuleCollection(EntityCollection $moduleCollection)
    {
        /** @var ModuleEntity[] $moduleList */
        $moduleList = $moduleCollection->getArray();
        $moduleList = ModuleEntity::sortModuleEntityArrayByWeight($moduleList);

        foreach ($moduleList as $module) {
            $resourcePath = $module->getResourceConfigValue('path');
            if (!$resourcePath) {
                continue;
            }

            $this->addTemplateDir(ROOT . $module->getPath() . $resourcePath . "/template");
        }
    }

    public function addTemplateDir(string $path): self
    {
        $this->templateDirs[] = URIHelper::pathifie($path);
        return $this;
    }

    /**
     * @param string $filePath
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderFile(string $filePath): string
    {

        try {
            return $this->render($filePath, $this->context->getArray());
        } catch (LoaderError $loaderError) {
            if ($this->isDevEnvironment) throw $loaderError;
            return "";
        } catch (RuntimeError $runtimeError) {
            if ($this->isDevEnvironment) throw $runtimeError;
            return "";
        } catch (SyntaxError $syntaxError) {
            if ($this->isDevEnvironment) throw $syntaxError;
            return "";
        }

    }

    public function render(string $file, ?array $data = null): string
    {

        if ($data === null) {
            $data = $this->context->getArray();
        }

        try {
            return $this->twig->render($file, $data);
        } catch (Exception $e) {
            if (MODE == 'dev') {
                return $e->getMessage();
            }
            return (string)(new TwigRenderException($file))->getMessage();
        }
    }

    /**
     * @param string $filePath
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderFileWithContext(string $filePath, array $context): string
    {
        try {
            return $this->render($filePath, $context);
        } catch (LoaderError $loaderError) {
            if ($this->isDevEnvironment) throw $loaderError;
            return "";
        } catch (RuntimeError $runtimeError) {
            if ($this->isDevEnvironment) throw $runtimeError;
            return "";
        } catch (SyntaxError $syntaxError) {
            if ($this->isDevEnvironment) throw $syntaxError;
            return "";
        }
    }

    /**
     * @return string
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function finish(): string
    {
        return $this->startRendering();
    }

    /**
     * Executes the twig rendering
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function startRendering(): string
    {

        //check customoutput
        if ($this->customoutput !== null) {
            return $this->customoutput;
        }

        return $this->render($this->targetFile, $this->context->getArray());
    }

    public function setRenderFile(string $file): self
    {
        $this->targetFile = $file;
        return $this;
    }

    public function assign(string $key, $value): self
    {
        $this->context->set($key, $value);
        return $this;
    }

    public function assignBulk(array $values): self {
        foreach($values as $key => $value) {
            $this->assign($key, $value);
        }
        return $this;
    }

    public function setOutput($value): self
    {
        if (is_string($value)) {
            $this->customoutput = $value;
        } else {
            $this->customoutput = json_encode($value);
        }

        return $this;
    }
}