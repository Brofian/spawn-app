<?php

namespace SpawnBackend\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnBackend\Database\AdministratorTable\AdministratorEntity;
use SpawnBackend\Database\AdministratorTable\AdministratorRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class CreateAdminUserCommand extends AbstractCommand {

    public const EMAIL_PATTERN = '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/m';

    public static function getCommand(): string
    {
        return 'database:create_admin';
    }

    public static function getShortDescription(): string
    {
        return 'Creates an admin user for the backend';
    }

    public static function getParameters(): array
    {
        return [
            'username' => ['u', 'username'],
            'password' => ['p', 'password'],
            'email' => ['e', 'email'],
            'active' => ['a', 'active']
        ];
    }

    /**
     * @throws WrongEntityForRepositoryException
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws \SpawnCore\System\Database\Entity\RepositoryException
     */
    public function execute(array $parameters): int
    {
        //get username
        if($parameters['username'] && $parameters['username'] !== '') {   $username = $parameters['username'];    }
        else {
            $username = IO::readLine('Username: ', static function ($answer) {
                return $answer !== '';
            }, 'The username has to be at least one character long', 3);

            if($username === false) {
                IO::printError('Invalid input! Aborting...');
                return 1;
            }
        }

        //get password
        if($parameters['password'] && $parameters['password'] !== '') {    $password = $parameters['password'];    }
        else {
            $password = IO::readLine('Password: ', static function($answer) {
                return $answer !== '';
            }, 'The password has to be at least one character long!', 3);

            if($password === false) {
                IO::printError('Invalid input! Aborting...');
                return 1;
            }
        }

        //get email
        if($parameters['email'] && preg_match(self::EMAIL_PATTERN, $parameters['email'])) {    $email = $parameters['email'];    }
        else {
            $email = IO::readLine('Email: ', static function($answer) {
                return preg_match(self::EMAIL_PATTERN, $answer);
            }, 'This is not a valid email pattern!', 3);

            if($email === false) {
                IO::printError('Invalid input! Aborting...');
                return 1;
            }
        }

        //set active
        $active = true;
        if($parameters['active'] && is_numeric($parameters['active'])) {
            $input = (int)$parameters['active'];
            $active = ($input !== 0);
        }


        //javascript kompilieren
        IO::printWarning("> creating admin user");

        $adminEntity = new AdministratorEntity(
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $email,
            $active
        );

        /** @var AdministratorRepository $administrationRepository */
        $administrationRepository = ServiceContainerProvider::getServiceContainer()->getServiceInstance('administrator.repository');

        $administrationRepository->upsert($adminEntity);
        IO::printSuccess("> successfully created admin user ");

        return 0;
    }
}