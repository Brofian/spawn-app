<?php declare(strict_types=1);

namespace SpawnCore\System\ServiceSystem;

class ServiceTags
{

    /* Some undefined service without any special definition */
    public const BASE_SERVICE = 'base.service';
    /* A Controller that contains callable Actions */
    public const BASE_CONTROLLER = 'base.controller';
    /* An extension of the Controller class that is protected by the administration login */
    public const BACKEND_CONTROLLER = 'backend.controller';
    /* Any Service that serves a technical function */
    public const TECHNICAL_SERVICE = 'technical.service';
    /* The definition of an auto generated database table */
    public const DATABASE_TABLE = 'database.table';
    /* Defines a repository for accessing a database table */
    public const DATABASE_REPOSITORY = 'database.repository';
    /* Classes with these tags are loaded and added to the twig loader */
    public const EXTENSION_TWIG = 'extension.twig';
    /* A class, that implements the EventSubscriber interface to be called by an event */
    public const EVENT_SUBSCRIBER_SERVICE = 'event.subscriber';
    /* Makes classes executable on the CLI via bin/console (they should always extend the AbstractCommand class) */
    public const CONSOLE_COMMAND = 'console.command';
    /* Defines a service, that will be executed by the cron manager */
    public const CRON_SERVICE = 'cron.service';

}