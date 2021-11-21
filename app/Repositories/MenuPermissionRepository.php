<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;
use App\Models\User;

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
	public function updateMenuPermissions(array $menuPermissions, User $user)
	{
		$menusIds = array_column($menuPermissions['menus'], 'menu_id');
		$menusFromUser = $this->model->where('user_id', $user->id)->whereIn('menu_id', $menusIds)->get();

		$this->update($menuPermissions, $menusFromUser);

		$users = User::where('advocate_user_id', $user->id)->get();

		if(!$users->isEmpty()){
			foreach($users as $user)
			{
				$menus = $this->model->where('user_id', $user->id)->whereIn('menu_id', $menusIds)->get();
				$this->update($menuPermissions, $menus);
			}
		}
	}

	private function update($menuPermissions, $menusFromUser)
	{
		/**
		 * Atualiza os menus
		 */
		foreach ($menuPermissions['menus'] as $menu) {
			$getMenus = $menusFromUser->where('menu_id', $menu['menu_id']);
			$ids = $getMenus->pluck('id')->toArray();
			$this->model->whereIn('id', $ids)->update(['menu_is_active' => $menu['checked']]);	
		}

		/**
		 * Atualiza as permissões
		 */
		foreach ($menuPermissions['permissions'] as $permissions) {

			foreach($permissions as $permission)
			{
				$getPermission = $menusFromUser->where('menu_id', $permission['menu_id'])
					->where('permission_id', $permission['permission_id'])->first();

				if($getPermission){
					$getPermission->update(['permission_is_active' => $permission['checked']]);
				}
			}
		}

	}
}
