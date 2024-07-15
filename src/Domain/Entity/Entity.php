<?php

namespace IFX\Domain\Entity;

use Ramsey\Uuid\Uuid;

abstract class Entity
{
    protected string $id;

    public function __construct()
    {
        $this->id = UUID::uuid4()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isEqual(Entity $entity): bool
    {
        return $this->id === $entity->id;
    }
}