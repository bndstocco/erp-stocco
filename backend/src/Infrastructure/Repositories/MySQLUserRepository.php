<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\User;
use ErpStocco\Domain\Repositories\UserRepositoryInterface;
use ErpStocco\Domain\ValueObjects\Email;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLUserRepository implements UserRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'users');
    }

    public function findById(int $id): ?User
    {
        $data = (clone $this->qb)->where('id', $id)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $data = (clone $this->qb)->where('email', $email)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;

        if (!empty($filters['search'])) {
            $qb->whereLike('name', $filters['search']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        if (!empty($filters['role_id'])) {
            $qb->where('role_id', $filters['role_id']);
        }

        $qb->orderBy('created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        $data = $qb->get();
        return array_map(fn($d) => $this->hydrate($d)->toArray(), $data);
    }

    public function save(User $user): User
    {
        $id = $this->qb->insert([
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'password' => $user->getPassword(),
            'phone' => $user->getPhone(),
            'avatar' => $user->getAvatar(),
            'role_id' => $user->getRoleId(),
            'status' => $user->getStatus(),
        ]);
        $user->setId((int)$id);
        return $user;
    }

    public function update(User $user): User
    {
        $qb = clone $this->qb;
        $qb->where('id', $user->getId());
        $qb->update([
            'name' => $user->getName(),
            'email' => $user->getEmail()->value(),
            'phone' => $user->getPhone(),
            'avatar' => $user->getAvatar(),
            'role_id' => $user->getRoleId(),
            'status' => $user->getStatus(),
        ]);
        return $user;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        return $qb->count();
    }

    private function hydrate(array $data): User
    {
        return new User(
            id: (int) $data['id'],
            name: $data['name'],
            email: new Email($data['email']),
            password: $data['password'],
            phone: $data['phone'] ?? null,
            avatar: $data['avatar'] ?? null,
            roleId: $data['role_id'] ? (int) $data['role_id'] : null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
