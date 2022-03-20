<?php

namespace SpawnCore\Defaults\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Exceptions\AddedSnippetForMissingLanguageException;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class SpawnSetupCommand extends AbstractCommand {


    public static function getCommand(): string
    {
        return 'spawn:setup';
    }

    public static function getShortDescription(): string
    {
        return 'Executes the default setup for a clean start in the project';
    }

    public static function getParameters(): array
    {
        return [];
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws AddedSnippetForMissingLanguageException
     */
    public function execute(array $parameters): int
    {
        (new PrintSpawnCommand())->execute([]);

        $confirmation = IO::readLine(IO::LIGHT_RED_TEXT.'This action depends on having an empty database. Do you want to continue? (yes/no/y/n)'.IO::DEFAULT_TEXT,
            static function ($answer) {
                return in_array($answer, ['yes','no','y','n']);
            }
        );

        if(!in_array($confirmation, ['yes', 'y'])) {
            IO::printLine('Aborting...', IO::RED_TEXT);
            return 0;
        }


        $container = ServiceContainerProvider::getServiceContainer();


        //setup minimal db structure
        (new DatabaseSetupMinimalCommand())->execute(DatabaseSetupMinimalCommand::createParameterArray([]));

        //refresh modules
        /** @var ModulesRefreshCommand $modulesRefreshCommand */
        $modulesRefreshCommand = $container->get('system.command.module_refresh');
        $modulesRefreshCommand->execute(ModulesRefreshCommand::createParameterArray(['m'=>true]));

        //update database
        (new DatabaseUpdateCommand())->execute(DatabaseUpdateCommand::createParameterArray([]));

        //refresh actions
        $modulesRefreshCommand->execute(ModulesRefreshCommand::createParameterArray(['a'=>true, 'c'=>true]));

        //execute migrations
        /** @var MigrationExecuteCommand $modulesRefreshCommand */
        $migrationExecuteCommand = $container->get('system.command.migration_execute');
        $migrationExecuteCommand->execute(MigrationExecuteCommand::createParameterArray([]));

        //refresh snippets
        $modulesRefreshCommand->execute(ModulesRefreshCommand::createParameterArray(['s'=>true]));

        //clear cache
        (new CacheClearCommand())->execute(CacheClearCommand::createParameterArray([]));

        return 0;
    }





}