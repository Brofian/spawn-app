<?php

namespace SpawnCore\Defaults\Services;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use SpawnCore\Defaults\Database\AnalysisTable\AnalysisEntity;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\EventSystem\Events\RequestRoutedEvent;
use SpawnCore\System\EventSystem\EventSubscriberInterface;

class AnalysisManager implements EventSubscriberInterface {

    protected TableRepository $analysisRepository;
    protected ConfigurationManager $configManager;

    public function __construct(
        TableRepository $analysisRepository,
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

        $data = $this->getConnectionData();

        $crawlerDetect = new CrawlerDetect();
        $isBot = $crawlerDetect->isCrawler();
        if(!empty($crawlerDetect->getMatches())) {
            $data['possibleCrawlers'] = $crawlerDetect->getMatches();
        }

        $ipHash = md5(SALT.$request->getClientIp());

        $this->createNewAnalysisEntry($request->getSeoUrl()->getId(), $data, $isBot, $ipHash);
    }

    protected function createNewAnalysisEntry(?string $urlId, array $data, bool $isBot, string $ipHash): void {
        /** @var AnalysisEntity $existing */
        $existing = $this->analysisRepository->search(new Criteria(new AndFilter(
            new EqualsFilter('ipHash', $ipHash),
            new EqualsFilter('urlId', UUID::hexToBytes($urlId)),
        )))->first();

        if($existing) {
            $existing->setCount($existing->getCount()+1);
            $this->analysisRepository->upsert($existing);
            return;
        }

        $analysis = new AnalysisEntity($urlId, json_encode($data, JSON_THROW_ON_ERROR), $isBot, $ipHash);
        $this->analysisRepository->upsert($analysis);
    }

    protected function getConnectionData(): array {
        return [
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
        ];
    }




}