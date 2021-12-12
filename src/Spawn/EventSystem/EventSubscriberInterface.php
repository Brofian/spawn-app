<?php

namespace spawnCore\EventSystem;

interface EventSubscriberInterface
{

    public static function getSubscribedEvents(): array;

}