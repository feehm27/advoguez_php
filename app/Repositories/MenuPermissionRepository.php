<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;

/**
 * Class MenuPermissionRepository
 */
class MenuPermissionRepository
{

	public function __construct(MenuPermission $model)
	{
		$this->model = $model;
	}

	/**
	 * Obtém todos os menus e suas permissões
	 */
	public function getAllMenuPermissions()
	{
		$menus = Menu::where('is_active', true)->get();

		foreach ($menus as $menu) {
			$permissionsIds = $menu->permissions_ids;
			$permissions = Permission::whereIn('id', $permissionsIds)->get();
			$menu->permissions = $permissions;
		}

		return $menus;
	}

	/**
	 * Atualiza os menus e suas permissçoes
	 * @param array $menuPermissions
	 * 
	 */
	public function updateMenuPermissions(array $menuPermissions)
	{
		foreach ($menuPermissions as $menuPermission) {
			$menuPermissionId =  $menuPermission['id'];
			unset($menuPermission['id']);
			$this->model->where('id', $menuPermissionId)->update($menuPermission);
		}
		return $menuPermissions;
	}
}
