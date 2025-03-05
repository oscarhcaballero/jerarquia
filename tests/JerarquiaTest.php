<?php

namespace Parentesys\Tests;

use Parentesys\Interfaces\IOrganization;
use Parentesys\Interfaces\IMember;
use Parentesys\Models\Organization;
use Parentesys\Models\Member;
use PHPUnit\Framework\TestCase;

class JerarquiaTest extends TestCase
{
    /**
     * @return IOrganization
     */
    private function populate(): IOrganization
    {
        $id = 1;

        // Define ceo
        $boss1 = new Member($id++, 80);
        $organization = new Organization($boss1);

        // Set members in different levels
        $boss2 = $organization->addMember((new Member($id++, 74))->setBoss($boss1));
        $organization->addMember((new Member($id++, 70))->setBoss($boss1));
        $boss4 = $organization->addMember((new Member($id++, 73))->setBoss($boss1));

        $boss5 = $organization->addMember((new Member($id++, 68))->setBoss($boss2));
        $organization->addMember((new Member($id++, 52))->setBoss($boss5));
        $organization->addMember((new Member($id++, 64))->setBoss($boss2));
        $organization->addMember((new Member($id++, 63))->setBoss($boss2));
        $organization->addMember((new Member($id++, 65))->setBoss($boss2));

        $organization->addMember((new Member($id++, 54))->setBoss($boss5));
        $organization->addMember((new Member($id++, 56))->setBoss($boss5));

        $boss12 = $organization->addMember((new Member($id++, 48))->setBoss($boss4));
        $organization->addMember((new Member($id++, 61))->setBoss($boss12));
        $organization->addMember((new Member($id++, 55))->setBoss($boss12));
        $organization->addMember((new Member($id++, 69))->setBoss($boss12));

        return $organization;
    }

    /**
     * Create the organization correctly and test getCeo
     */
    public function testCreateOrganization()
    {
        $organization = $this->populate();

        // Test ceo
        $this->assertEquals(1, $organization->getCeo()->getId());
        $this->assertEquals(80, $organization->getCeo()->getAge());
    }

    /**
     * Test getMember method
     */
    public function testGetMember()
    {
        $organization = $this->populate();

        // Test a middle range member
        $member5 = $organization->getMember(5);
        $this->assertInstanceOf(IMember::class, $member5);
        $this->assertEquals(68, $member5->getAge());
        $this->assertEquals($organization->getMember(2), $member5->getBoss());
        $this->assertCount(3, $member5->getSubordinates());
    }

    /**
     * From a middle range member, test his boss and his subordinates get methods
     */
    public function testGetNearMembers()
    {
        $organization = $this->populate();

        // Test a middle range member
        $member5 = $organization->getMember(5);
        $this->assertEquals($organization->getMember(2), $member5->getBoss());
        $subordinates = $member5->getSubordinates();
        $this->assertCount(3, $subordinates);
        $this->assertEquals($member5, array_pop($subordinates)->getBoss());
    }

    /**
     * Test send a member to Temporal Leave
     */
    public function testTempLeave()
    {
        $organization = $this->populate();

        // Send a middle range member to Temporal Leave
        $this->assertTrue($organization->tempLeave($organization->getMember(5)));

        // Check if the member is still in the organization
        $this->assertNull($organization->getMember(5));

        // Check moved members
        $this->assertEquals(9, $organization->getMember(10)->getBoss()->getId());
        $this->assertCount(3, $organization->getMember(9)->getSubordinates());
    }

    /**
     * Test send a member to Temporal Leave
     */
    public function testTempLeavePromoted()
    {
        $organization = $this->populate();

        // Send all the members in that level to Temporal Leave
        $this->assertTrue($organization->tempLeave($organization->getMember(5)));
        $this->assertTrue($organization->tempLeave($organization->getMember(7)));
        $this->assertTrue($organization->tempLeave($organization->getMember(9)));
        $this->assertTrue($organization->tempLeave($organization->getMember(8)));

        // Check moved members
        $this->assertEquals(11, $organization->getMember(10)->getBoss()->getId());
        $this->assertEquals(2, $organization->getMember(11)->getBoss()->getId());
    }

    /**
     * Test send the ceo to Temporal Leave
     */
    public function testSendCeoToTempLeave()
    {
        $organization = $this->populate();

        // Send a middle range member to Temporal Leave
        $this->assertTrue($organization->sendToTempLeave($organization->getMember(1)));

        // Check moved members
        $this->assertEquals(2, $organization->()->getId());
    }

    /**
     * Test goBack a member from Temporal Leave
     */
    public function testgoBackFromTempLeave()
    {
        $organization = $this->populate();
        $member = $organization->getMember(5);
        $organization->tempLeave($member);

        // goBack him from Temporal Leave
        $this->assertTrue($organization->goBackFromTempLeave($member));
        $this->assertEquals($member, $organization->getMember(5));

        // Check near members moved again
        $this->assertEquals($organization->getMember(2), $member->getBoss());
        $subordinates = $member->getSubordinates();
        $this->assertCount(3, $subordinates);
        $this->assertEquals($member, array_pop($subordinates)->getBoss());
        $this->assertEquals(5, $organization->getMember(10)->getBoss()->getId());
        $this->assertCount(0, $organization->getMember(9)->getSubordinates());
    }

    /**
     * Test goBack a member from Temporal Leave
     */
    public function testgoBackFromTempLeavePromoted()
    {
        $organization = $this->populate();
        $member = $organization->getMember(12);
        $organization->tempLeave($member);

        // goBack him from Temporal Leave
        $this->assertTrue($organization->goBackFromTempLeave($member));
        $this->assertEquals($member, $organization->getMember(12));

        // Check near members moved again
        $this->assertEquals($organization->getMember(4), $member->getBoss());
        $subordinates = $member->getSubordinates();
        $this->assertCount(3, $subordinates);
        $this->assertEquals($member, array_pop($subordinates)->getBoss());
        $this->assertEquals(12, $organization->getMember(15)->getBoss()->getId());
    }

    /**
     * Test find big bosses
     */
    public function testFindBigBosses()
    {
        $organization = $this->populate();

        // Bosses with more than 4 subordinates
        $this->assertCount(2, $organization->findBigBosses(4));
    }

    /**
     * Test compare members
     */
    public function testCompareMembers()
    {
        $organization = $this->populate();

        // Compare two organization members
        $memberA = $organization->getMember(6);
        $memberB = $organization->getMember(8);
        $this->assertEquals($memberB, $organization->compareMembers($memberA, $memberB));
    }
}
