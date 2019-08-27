<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use Cache;

class MenuService
{

    /**
     * @var Menu
     */
    private $menu;

    /**
     * MenuService constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
	{
		$this->menu = $menu;
	}

    /**
     * @param User|null $user
     * @return mixed
     */
    public function getMenu(User $user = null)
	{
		return $this->menu->withoutParentId()
			->orderMenu()
			->get();
	}

    /**
     * @param User|null $user
     * @return mixed
     */
    public function getMenuWithParent(User $user = null)
    {
        return $this->menu
            ->orderMenu()
            ->get();
    }

}
