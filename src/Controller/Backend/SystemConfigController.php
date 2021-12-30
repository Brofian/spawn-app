<?php


namespace spawnApp\Controller\Backend;

use Error;
use Exception;
use spawnApp\Database\ConfigurationTable\ConfigurationEntity;
use spawnApp\Database\ConfigurationTable\ConfigurationRepository;
use spawnApp\Database\ConfigurationTable\ConfigurationTable;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Services\SeoUrlManager;
use spawnCore\CardinalSystem\Request;
use spawnCore\Custom\FoundationStorage\AbstractBackendController;
use spawnCore\Custom\Response\AbstractResponse;
use spawnCore\Custom\Response\JsonResponse;
use spawnCore\Custom\Response\TwigResponse;
use spawnCore\Custom\Throwables\DatabaseConnectionException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Criteria\Filters\InvalidFilterValueException;
use spawnCore\Database\Criteria\Filters\LikeFilter;
use spawnCore\Database\Criteria\Filters\OrFilter;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\Database\Entity\InvalidRepositoryInteractionException;
use spawnCore\Database\Entity\RepositoryException;
use spawnCore\Database\Entity\TableRepository;
use spawnCore\Database\Helpers\DatabaseConnection;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\Service;
use spawnCore\ServiceSystem\ServiceContainerProvider;
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
                'title' => "Einstellungen", //Kategory title
                'color' => "#00ff00", //Kategory color
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'overviewAction',
                        'title' => 'System config'
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
     * @route /backend/api/config/entity/{}/{}
     * @locked
     * @param string $internalName
     * @param string $search
     * @return AbstractResponse
     * @throws InvalidFilterValueException
     */
    public function getEntitySearchAction(string $internalName, string $search = ''): AbstractResponse {

        $config = null;
        try {
            $config = $this->configurationRepository->search(new Criteria(new EqualsFilter('internalName', $internalName)))->first();
            if(!$config) {
                throw new InvalidRepositoryInteractionException('Could not find configuration for "'.$internalName.'"');
            }


            /** @var ConfigurationEntity $config */
            $entity = $config->getDefinition(true)['entity'];
            $entityGetLabel = $config->getDefinition(true)['label'];
            $entityGetIdentifier = $config->getDefinition(true)['identifier'];


            /** @var TableRepository $repository */
            $repository = ServiceContainerProvider::getServiceContainer()->getServiceInstance($entity);
            if(!$repository) {
                throw new InvalidRepositoryInteractionException('Invalid entity "'.$entity.'"');
            }


            $criteria = new Criteria();
            if($search) {
                //TODO
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