<?php

namespace IFX\Infrastructure\Repository;

use IFX\Domain\Entity\Entity;
use IFX\Infrastructure\Repository\Exception\RepositoryException;

abstract class Repository
{
    protected array $objects;

    public function add(Entity $object): void
    {
        $this->objects[$object->getId()] = $object;
    }

    protected function exists(string $id): bool
    {
        return isset($this->objects[$id]);
    }

    public function getAll(): array
    {
        return $this->objects;
    }

    /**
     * @param string $id
     * @return Entity
     * @throws RepositoryException
     */
    abstract public function get(string $id): Entity;
}