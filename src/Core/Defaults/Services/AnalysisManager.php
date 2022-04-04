<?php

namespace SpawnCore\Defaults\Services;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use SpawnBackend\Controller\Backend\SystemConfigController;
use SpawnCore\Defaults\Database\AnalysisTable\AnalysisEntity;
use SpawnCore\Defaults\Database\AnalysisTable\AnalysisRepository;
use SpawnCore\System\EventSystem\Events\RequestRoutedEvent;
use SpawnCore\System\EventSystem\EventSubscriberInterface;

class AnalysisManager implements EventSubscriberInterface {

    protected AnalysisRepository $analysisRepository;
    protected ConfigurationManager $configManager;

    public function __construct(
        AnalysisRepository $analysisRepository,
        ConfigurationManager $configManager
    )   {
        $this->analysisRepository = $analysisRepository;
        $this->configManager = $configManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestRoutedEvent::class => 'onRequestRoutedEvent'
        ];
    }

    public function onRequestRoutedEvent(RequestRoutedEvent $event): void {
        if(!$this->configManager->getConfiguration('config_system_analysis_active', false)) {
            return;
        }

        $request = $event->getRequest();
        $controllerService = $event->getControllerService();

        $data = [
            'request' => $request->getVars(),
            'connection' => $this->getConnectionData()
        ];

        $crawlerDetect = new CrawlerDetect();
        $isBot = $crawlerDetect->isCrawler();
        if(!empty($crawlerDetect->getMatches())) {
            $data['possibleCrawlers'] = $crawlerDetect->getMatches();
        }


        $this->createNewAnalysisEntry(null, $data, $isBot);
    }

    protected function createNewAnalysisEntry(?string $urlId, array $data, bool $isBot): void {
        $analysis = new AnalysisEntity($urlId, json_encode($data, JSON_THROW_ON_ERROR), $isBot);
        $this->analysisRepository->upsert($analysis);
    }

    protected function getConnectionData(): array {
        return [
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
        ];
    }




}