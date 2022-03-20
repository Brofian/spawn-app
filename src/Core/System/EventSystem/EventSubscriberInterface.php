<?php declare(strict_types = 1);
namespace SpawnCore\System\EventSystem;

interface EventSubscriberInterface
{

    public static function getSubscribedEvents(): array;

}