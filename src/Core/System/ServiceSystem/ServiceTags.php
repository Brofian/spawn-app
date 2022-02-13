<?php declare(strict_types=1);

namespace SpawnCore\System\ServiceSystem;

class ServiceTags
{

    /* Some undefined service without any special definition */
    const BASE_SERVICE = 'base.service';
    /* A Controller that contains callable Actions */
    const BASE_CONTROLLER = 'base.controller';
    /* An extension of the Controller class that is protected by the administration login */
    const BACKEND_CONTROLLER = 'backend.controller';
    /* Any Service that serves a technical function */
    const TECHNICAL_SERVICE = 'technical.service';
    /* The definition of an auto generated database table */
    const DATABASE_TABLE = 'database.table';
    /* Classes with these tags are loaded and added to the twig loader */
    const EXTENSION_TWIG = 'extension.twig';
    /* A class, that implements the EventSubscriber interface to be called by an event */
    const EVENT_SUBSCRIBER_SERVICE = 'event.subscriber';
    /* Makes classes executable on the CLI via bin/console (they should always extend the AbstractCommand class) */
    const CONSOLE_COMMAND = 'console.command';
    /* Defines a service, that will be executed by the cron manager */
    const CRON_SERVICE = 'cron.service';

}