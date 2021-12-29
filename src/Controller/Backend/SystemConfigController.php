<?php


namespace spawnApp\Controller\Backend;

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
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\Database\Helpers\DatabaseConnection;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\Service;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class SystemConfigController extends AbstractBackendController {

    protected ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository
    )
    {
        parent::__construct();
        $this->configurationRepository = $configurationRepository;
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
     */
    public function folderSaveSubmitAction(): AbstractResponse {

        return new JsonResponse([
            'success' => false,
            'errors' => ['TODO: ' . __CLASS__ . ':' . __LINE__]
        ]);
    }

}