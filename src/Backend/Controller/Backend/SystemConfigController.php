<?php declare(strict_types = 1);
namespace SpawnBackend\Controller\Backend;

use Exception;
use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationEntity;
use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationRepository;
use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationTable;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AlwaysFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Criteria\Filters\LikeFilter;
use SpawnCore\System\Database\Criteria\Filters\OrFilter;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\InvalidRepositoryInteractionException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\Database\Helpers\DatabaseConnection;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use Throwable;

class SystemConfigController extends AbstractBackendController {

    protected ConfigurationRepository $configurationRepository;
    protected Request $request;


    public function __construct(
        ConfigurationRepository $configurationRepository,
        Request $request
    )
    {
        parent::__construct();
        $this->configurationRepository = $configurationRepository;
        $this->request = $request;
    }



    public static function getSidebarMethods(): array
    {
        return [
            'configuration' => [
                'title' => "config", //Kategory title
                'color' => "#00ff00", //Kategory color
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'overviewAction',
                        'title' => 'system_config'
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend/config/overview
     * @locked
     * @return AbstractResponse
     */
    public function overviewAction(): AbstractResponse {

        try {
            $qb = DatabaseConnection::getConnection()->createQueryBuilder();
            $stmt = $qb->select('DISTINCT (folder)')
                ->from(ConfigurationTable::TABLE_NAME)
                ->executeQuery();
            $folders = array_column($stmt->fetchAllAssociative(), 'folder');
        }
        catch (Exception $e) {
            $folders = [];
        }

        $this->twig->assign('available_folders', $folders);
        $this->twig->assign('content_file', 'backend/contents/config/overview/content.html.twig');
        return new TwigResponse('backend/index.html.twig');
    }


    /**
     * @route /backend/config/folder/{}
     * @locked
     * @return AbstractResponse
     */
    public function folderAction(string $folderName): AbstractResponse {

        try {
            $configurationFields = $this->configurationRepository->search(
                new Criteria(
                    new EqualsFilter('folder', $folderName)
                )
            );
        }
        catch (Exception $e) {
            $configurationFields = new EntityCollection(ConfigurationEntity::class);
        }


        /** @var ConfigurationEntity $configurationField */
        foreach($configurationFields as $configurationField) {
            // if this field is an entity search and has a value, load the selected label
            if($configurationField->getType() === 'entity' && $configurationField->getValue()) {
                /** @var TableRepository $repository */
                try {
                    $definition = $configurationField->getDefinition(true);
                    $repositoryID = $definition['repository'];
                    $identifierColumn = $definition['identifier'];
                    $labelGetter = $definition['label'];

                    $value = ($identifierColumn === 'id') ? UUID::hexToBytes($configurationField->getValue()) : $configurationField->getValue();

                    $repository = ServiceContainerProvider::getServiceContainer()->get($repositoryID);
                    $el = $repository->search(new Criteria(new EqualsFilter($identifierColumn, $value)))->first();
                    if($el) {
                        $configurationField->set('selectedEntityLabel', $el->{$labelGetter}());
                    }
                }
                catch (Exception $e) {}
            }
        }

        $this->twig->assign('config_folder', $folderName);
        $this->twig->assign('config_fields', $configurationFields);
        $this->twig->assign('content_file', 'backend/contents/config/folder/content.html.twig');
        return new TwigResponse('backend/index.html.twig');
    }


    /**
     * @route /backend/config/submit/folder
     * @locked
     * @return AbstractResponse
     * @throws DatabaseConnectionException
     */
    public function folderSaveSubmitAction(): AbstractResponse {

        $queryBuilder = DatabaseConnection::getConnection()->createQueryBuilder();
        $stmt = $queryBuilder->update(ConfigurationTable::TABLE_NAME, 'c')
            ->set('c.value', '?')
            ->where('c.internalName = ?');

        $errors = [];
        try {
            foreach($this->request->getPost() as $key => $value) {
                if(strpos($key, 'field__') === 0) {
                    $key = str_replace('field__', '', $key);
                    $stmt->setParameters([$value, $key]);
                    $stmt->executeStatement();
                }
            }
        }
        catch (\Doctrine\DBAL\Exception $e) {
            $errors[] = $e->getMessage();
        }

        //save the fields

        return new JsonResponse([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }


    /**
     * @route /backend/api/config/entity/{}
     * @locked
     * @param string $internalName
     * @param string $search
     * @return AbstractResponse
     */
    public function getEntitySearchAction(string $internalName): AbstractResponse {
        $config = null;
        try {
            $config = $this->configurationRepository->search(new Criteria(new EqualsFilter('internalName', $internalName)))->first();
            if(!$config) {
                throw new InvalidRepositoryInteractionException('Could not find configuration for "'.$internalName.'"');
            }


            /** @var ConfigurationEntity $config */
            $repositoryID = $config->getDefinition(true)['repository'];
            $entityGetLabel = $config->getDefinition(true)['label'];
            $entityGetIdentifier = $config->getDefinition(true)['identifier_getter'];
            $entitySearchColumns = $config->getDefinition(true)['search'] ?? [];


            /** @var TableRepository $repository */
            $repository = ServiceContainerProvider::getServiceContainer()->getServiceInstance($repositoryID);
            if(!$repository) {
                throw new InvalidRepositoryInteractionException('Invalid repository ID "'.$repositoryID.'"');
            }

            $criteria = new Criteria();
            $search = $this->request->getPost()->get('search') ?? '';
            $search = trim(urldecode($search));
            if(!empty($entitySearchColumns) && strlen($search) > 2) {
                $searchFilter = new OrFilter(new AlwaysFilter(false));
                foreach($entitySearchColumns as $entitySearchColumn) {
                    $searchFilter->addFilter(new LikeFilter($entitySearchColumn, "%$search%"));
                }
                $criteria->addFilter($searchFilter);
            }
            $entities = $repository->search($criteria, 10);
            $entries = [];
            foreach ($entities as $entity) {
                $entries[] = [
                    'identifier' => $entity->{$entityGetIdentifier}(),
                    'label' => $entity->{$entityGetLabel}(),
                ];
            }

            return new JsonResponse([
                'success' => true,
                'entities' => $entries
            ]);
        }
        catch (Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }



}