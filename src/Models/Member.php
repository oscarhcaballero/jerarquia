<?php

namespace Parentesys\Models;
use Parentesys\Interfaces\IMember;

class Member implements IMember {
    private int $id;
    private int $age;
    private ?IMember $boss = null;
    private array $subordinates = [];
    private bool $isOnTempLeave = false;

    public function __construct(int $id, int $age) {
        $this->id = $id;
        $this->age = $age;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function addSubordinate(IMember $subordinate): IMember {
        $this->subordinates[$subordinate->getId()] = $subordinate;
        $subordinate->setBoss($this);
        return $this;
    }

    public function removeSubordinate(IMember $subordinate): ?IMember {
        unset($this->subordinates[$subordinate->getId()]);
        return $subordinate;
    }

    public function getSubordinates(): array {
        return array_values($this->subordinates);
    }

    public function getBoss(): ?IMember {
        return $this->boss;
    }

    public function setBoss(?IMember $boss): IMember {
        $this->boss = $boss;
        return $this;
    }

    public function isOnTempLeave(): bool {
        return $this->isOnTempLeave;
    }

    public function tempLeave(): void {
        $this->isOnTempLeave = true;
    }

    public function goBack(): void {
        $this->isOnTempLeave = false;
    }
}
