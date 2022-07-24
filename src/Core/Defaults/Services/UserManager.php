<?php declare(strict_types=1);

namespace SpawnCore\Defaults\Services;

use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlTable;
use SpawnCore\Defaults\Database\UserTable\UserEntity;
use SpawnCore\Defaults\Database\UserTable\UserRepository;
use SpawnCore\Defaults\Database\UserTable\UserTable;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\Gadgets\SessionHelper;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Criteria\Filters\OrFilter;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Helpers\DatabaseConnection;

class UserManager {

    public const USER_LOGIN_TOKEN = 'user_login_token';

    protected UserRepository $userRepository;
    protected Request $request;
    protected SessionHelper $sessionHelper;

    public function __construct(
        UserRepository $userRepository,
        Request $request,
        SessionHelper $sessionHelper
    )   {
        $this->userRepository = $userRepository;
        $this->request = $request;
        $this->sessionHelper = $sessionHelper;
    }


    public function upsertUser(UserEntity $entity): void
    {
        $this->userRepository->upsert($entity);
    }


    public function getUserById(string $id): ?UserEntity {
        if(!$id) {
            return null;
        }
        return $this->getUserByCriteria(new Criteria(
            new EqualsFilter('id', UUID::hexToBytes($id))
        ))->first();
    }

    public function getUserByToken(string $token): ?UserEntity {
        if(!$token) {
            return null;
        }

        return $this->getUserByCriteria(new Criteria(
            new EqualsFilter('loginHash', $token)
        ))->first();
    }

    public function getUserByEmailOrUsername(string $value): ?UserEntity {
        if(!$value) {
            return null;
        }

        return $this->getUserByCriteria(new Criteria(
            new OrFilter(
                new EqualsFilter('email', $value),
                new EqualsFilter('username', $value)
            )
        ))->first();
    }

    protected function getUserByCriteria(Criteria $criteria): EntityCollection {
        return $this->userRepository->search($criteria);
    }

    public function tryLogin(string $name, string $password): ?UserEntity {
        if(!$name || !$password) {
            return null;
        }

        $user = $this->getUserByEmailOrUsername($name);
        if(!$user) {
            return null;
        }
        if(password_verify($password, $user->getPassword())) {
           return $user;
        }
        return null;
    }

    public function logoutUser(bool $destructive): void {
        $this->sessionHelper->set(self::USER_LOGIN_TOKEN, null);
        if($destructive) {
            $this->sessionHelper->destroySession();
        }
    }

    public function getCurrentlyLoggedInUser(bool $allowPostToken = true): ?UserEntity {
        //check for token in session and post
        $sessionToken = $this->sessionHelper->get(self::USER_LOGIN_TOKEN);
        $filter = new OrFilter(
            new EqualsFilter('loginHash', $sessionToken)
        );

        $postToken = null;
        if($allowPostToken) {
            $postToken = $this->request->getPost()->get(self::USER_LOGIN_TOKEN);
            $filter->addFilter(
                new EqualsFilter('loginHash', $postToken)
            );
        }

        if(!$sessionToken && !$postToken) {
            return null;
        }

        $users = $this->getUserByCriteria(new Criteria($filter));
        if($users->count() !== 1) {
            return null;
        }

        return $users->first();
    }

    public function getTotalNumberOfUsers(): int {
        $queryBuilder = DatabaseConnection::getConnection()->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('COUNT(*) as count')
            ->from(UserTable::ENTITY_NAME);

        return (int)$stmt->executeQuery()->fetchAssociative()['count'];
    }

    public function getUsers(int $limit = 9999, int $offset = 0): EntityCollection {
        return $this->userRepository->search(new Criteria(), $limit, $offset);
    }

}