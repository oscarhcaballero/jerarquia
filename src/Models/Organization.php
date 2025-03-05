<?php

namespace Parentesys\Models;

use Parentesys\Interfaces\IMember;
use Parentesys\Interfaces\IOrganization;

class Organization implements IOrganization {
    private IMember $ceo;
    private array $members = [];

    public function __construct(IMember $ceo) {
        $this->ceo = $ceo;
        $this->members[$ceo->getId()] = $ceo;
    }

    public function getCeo(): IMember {
        return $this->ceo;
    }

    public function addMember(IMember $member): ?IMember {
        if (isset($this->members[$member->getId()])) {
            return null; // Miembro ya existe
        }
        $this->members[$member->getId()] = $member;
        return $member;
    }

    public function getMember(int $id): ?IMember {
        return $this->members[$id] ?? null;
    }

    public function tempLeave(IMember $member): bool {
        if (!$member || $member->isOnTempLeave()) return false;
        
        $member->tempLeave();
        $boss = $member->getBoss();
        if (!$boss) return true;

        // Reasignar subordinados
        foreach ($member->getSubordinates() as $subordinate) {
            $boss->addSubordinate($subordinate);
        }
        return true;
    }

    public function goBackFromTempLeave(IMember $member): bool {
        if (!$member || !$member->isOnTempLeave()) return false;
        
        $member->goBack();
        $boss = $member->getBoss();
        if ($boss) {
            foreach ($member->getSubordinates() as $subordinate) {
                $member->addSubordinate($subordinate);
            }
        }
        return true;
    }

    public function findBigBosses(int $minimumSubordinates): array {
        return array_filter($this->members, function (IMember $member) use ($minimumSubordinates) {
            return count($member->getSubordinates()) > $minimumSubordinates;
        });
    }

    public function compareMembers(IMember $memberA, IMember $memberB): ?IMember {
        if ($memberA->getBoss() === $memberB->getBoss()) {
            return $memberA->getAge() > $memberB->getAge() ? $memberA : $memberB;
        }

        $hierarchyA = $this->getHierarchyLevel($memberA);
        $hierarchyB = $this->getHierarchyLevel($memberB);

        return $hierarchyA > $hierarchyB ? $memberA : ($hierarchyA < $hierarchyB ? $memberB : null);
    }

    private function getHierarchyLevel(IMember $member): int {
        $level = 0;
        while ($member->getBoss()) {
            $level++;
            $member = $member->getBoss();
        }
        return $level;
    }
}
