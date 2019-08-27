<?php

namespace Tests;

use App\Constants\UserStatusConstant;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class UserServiceTest
 * @package Tests
 */
class UserServiceTest extends TestCase
{

    /**
     * @var null
     */
    private $userService = null;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    /**
     *
     */
    public function testFindUserById()
    {
        $createUser = factory(User::class)->create();
        $findUserById = $this->userService->findUserById($createUser->id);

        $this->assertContainsOnlyInstancesOf(
            User::class,
            [$createUser,$findUserById]
        );

        $this->assertEquals($createUser->id, $findUserById->id);
    }

    /**
     *
     */
    public function testListAll()
    {
        $listUser = $this->userService->list()->get();

        $listUser->each(function ($user) {
            $this->assertDatabaseHas(with(new User)->getTable(), $user->toArray());
        });
    }

    /**
     *
     */
    public function testListAllWithSearchString()
    {
        $searchStringTest = 'yzfg';

        $createUser = factory(User::class)->create(['name' =>  $searchStringTest . ' da Silva']);

        $listUser = $this->userService->list([
            'searchString' => $searchStringTest
        ])->get();

        $listUser->each(function ($user) use($searchStringTest){
            $this->assertDatabaseHas(with(new User)->getTable(), $user->toArray());
            $this->assertContains($searchStringTest, $user->name);
        });
    }

    /**
     *
     */
    public function testListAllWithSearchStatusActive()
    {
        $searchStatusTest = UserStatusConstant::ACTIVE;

        if (User::where('status', UserStatusConstant::ACTIVE)->count() == 0) {
            factory(User::class)->create([
                'status' => UserStatusConstant::ACTIVE
            ]);
        }

        $listUser = $this->userService->list([
            'searchStatus' => $searchStatusTest
        ])->get();

        $listUser->each(function ($user) use($searchStatusTest){
            $this->assertDatabaseHas(with(new User)->getTable(), $user->toArray());
            $this->assertContains($searchStatusTest, $user->status);
        });
    }

    public function testListAllWithSearchStatusInactive()
    {
        $searchStatusTest = UserStatusConstant::INACTIVE;

        if (User::where('status', UserStatusConstant::INACTIVE)->count() == 0) {
            factory(User::class)->create([
                'status' => UserStatusConstant::INACTIVE
            ]);
        }

        $listUser = $this->userService->list([
            'searchStatus' => $searchStatusTest
        ])->get();

        $listUser->each(function ($user) use($searchStatusTest){
            $this->assertDatabaseHas(with(new User)->getTable(), $user->toArray());
            $this->assertContains($searchStatusTest, $user->status);
        });
    }

    public function testCreate()
    {
        $user = factory(User::class)->make();

        $this->assertTrue($this->userService->create($user));
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create();

        $name = $user->name;
        $newName = 'Fulano de Oliveira da Silva';
        $user->name = $newName;

        $this->assertTrue($this->userService->update($user));
        $findUserById = $this->userService->findUserById($user->id);
        $this->assertEquals($findUserById->name, $newName);
    }

    public function testDelete()
    {
        $user = factory(User::class)->create();

        $this->assertTrue($this->userService->delete($user));

        $findUserById = $this->userService->findUserById($user->id);

        $this->assertEquals($findUserById, null);
    }

}
