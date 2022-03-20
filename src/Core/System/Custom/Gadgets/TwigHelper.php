<?php declare(strict_types=1);


namespace SpawnCore\System\Custom\Gadgets;


use Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\System\Custom\Collection\AssociativeCollection;
use SpawnCore\System\Custom\RenderExtensions\ExtensionLoader;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\TwigRenderException;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigHelper
{
    public const CACHE_FOLDER_PATH = ROOT . '/var/cache/private/twig';

    protected string $targetFile = 'base.html.twig';
    protected array $templateDirs = array();
    protected ?string $customoutput = null;
    protected Environment $twig;
    protected AssociativeCollection $context;
    protected bool $isDevEnvironment = false;


    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __construct()
    {
        $this->context = new AssociativeCollection();
        $this->isDevEnvironment = (MODE === 'dev');
        $this->loadTwig();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function loadTwig(): void
    {
        $moduleCollection = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.modules.collection');
        $this->loadTemplateDirFromModuleCollection($moduleCollection);

        $loader = new FilesystemLoader($this->templateDirs);
        $twig = new Environment($loader, [
            'debug' => (MODE === "dev"),
            'cache' => self::CACHE_FOLDER_PATH,
        ]); //<- Twig environment


        if (!is_object($twig)) {
            Debugger::ddump("Couldn´t load Twig");
        }

        ExtensionLoader::loadTwigExtensions($twig);
        $twig->addExtension(new DebugExtension());

        $this->twig = $twig;
    }

    public function loadTemplateDirFromModuleCollection(EntityCollection $moduleCollection): void
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
     * @throws Exception
     */
    public function renderFile(string $filePath): string
    {

        try {
            return $this->render($filePath, $this->context->getArray());
        } catch (Exception $loaderError) {
            if ($this->isDevEnvironment) {
                throw $loaderError;
            }
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
            if (MODE === 'dev') {
                return $e->getMessage();
            }
            return (string)(new TwigRenderException($file))->getMessage();
        }
    }

    /**
     * @param string $filePath
     * @param array $context
     * @return string
     * @throws Exception
     */
    public function renderFileWithContext(string $filePath, array $context): string
    {
        try {
            return $this->render($filePath, $context);
        } catch (Exception $loaderError) {
            if ($this->isDevEnvironment) {
                throw $loaderError;
            }
            return "";
        }
    }

    public function finish(): string
    {
        return $this->startRendering();
    }

    private function startRendering(): string
    {

        //check customoutput
        return $this->customoutput ?? $this->render($this->targetFile, $this->context->getArray());
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
            $this->customoutput = json_encode($value, JSON_THROW_ON_ERROR);
        }

        return $this;
    }
}