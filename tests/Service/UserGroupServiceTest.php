<?php

namespace Tests;

use App\Constants\UserGroupStatusConstant;
use App\Models\UserGroup;
use App\Services\UserGroupService;
use App\Services\UserGroupServices;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserServiceTest
 * @package Tests
 */
class UserGroupServiceTest extends TestCase
{

    /**
     * @var null
     */
    private $userGroupService = null;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->userGroupService = app(UserGroupService::class);
    }

    /**
     *
     */
    public function testfindUserGroupById()
    {
        $createUserGroup = factory(UserGroup::class)->create();
        $findUserGroupById = $this->userGroupService->findUserGroupById($createUserGroup->id);

        $this->assertContainsOnlyInstancesOf(
            UserGroup::class,
            [$createUserGroup, $findUserGroupById]
        );

        $this->assertEquals($createUserGroup->id, $findUserGroupById->id);
    }

    /**
     *
     */
    public function testListAll()
    {
        $listUserGroup = $this->userGroupService->list()->get();

        $listUserGroup->each(function ($userGroup) {
            $this->assertDatabaseHas(with(new UserGroup)->getTable(), $userGroup->toArray());
        });
    }

    /**
     *
     */
    public function testListAllWithSearchString()
    {
        $searchStringTest = 'yzfg';

        factory(UserGroup::class)->create(['name' =>  $searchStringTest . ' AdminTest']);

        $listUserGroup = $this->userGroupService->list([
            'searchString' => $searchStringTest
        ])->get();

        $listUserGroup->each(function ($userGroup) use($searchStringTest){
            $this->assertDatabaseHas(with(new UserGroup)->getTable(), $userGroup->toArray());
            $this->assertContains($searchStringTest, $userGroup->name);
        });
    }

    /**
     *
     */
    public function testListAllWithSearchStatusActive()
    {
        $searchStatusTest = UserGroupStatusConstant::ACTIVE;

        if (UserGroup::where('status', UserGroupStatusConstant::ACTIVE)->count() == 0) {
            factory(UserGroup::class)->create([
                'status' => UserGroupStatusConstant::ACTIVE
            ]);
        }

        $listUserGroup = $this->userGroupService->list([
            'searchStatus' => $searchStatusTest
        ])->get();

        $listUserGroup->each(function ($userGroup) use($searchStatusTest){
            $this->assertDatabaseHas(with(new UserGroup)->getTable(), $userGroup->toArray());
            $this->assertContains($searchStatusTest, $userGroup->status);
        });
    }

    public function testListAllWithSearchStatusInactive()
    {
        $searchStatusTest = UserGroupStatusConstant::INACTIVE;

        if (UserGroup::where('status', UserGroupStatusConstant::INACTIVE)->count() == 0) {
            factory(UserGroup::class)->create([
                'status' => UserGroupStatusConstant::INACTIVE
            ]);
        }

        $listUserGroup = $this->userGroupService->list([
            'searchStatus' => $searchStatusTest
        ])->get();

        $listUserGroup->each(function ($userGroup) use($searchStatusTest){
            $this->assertDatabaseHas(with(new UserGroup)->getTable(), $userGroup->toArray());
            $this->assertContains($searchStatusTest, $userGroup->status);
        });
    }

    public function testCreate()
    {
        $userGroup = factory(UserGroup::class)->make();

        $this->assertTrue($this->userGroupService->create($userGroup));
    }

    public function testUpdate()
    {
        $userGroup = factory(UserGroup::class)->create();

        $name = $userGroup->name;
        $newName = 'Test Modulo';
        $userGroup->name = $newName;

        $this->assertTrue($this->userGroupService->update($userGroup));
        $findUserGroupById = $this->userGroupService->findUserGroupById($userGroup->id);
        $this->assertEquals($findUserGroupById->name, $newName);
    }

    public function testDelete()
    {
        $userGroup = factory(UserGroup::class)->create();

        $this->assertTrue($this->userGroupService->delete($userGroup));

        $findUserGroupById = $this->userGroupService->findUserGroupById($userGroup->id);

        $this->assertEquals($findUserGroupById, null);
    }

}
