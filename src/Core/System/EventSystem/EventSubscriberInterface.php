<?php

namespace SpawnCore\System\EventSystem;

interface EventSubscriberInterface
{

    public static function getSubscribedEvents(): array;

}