<?php

namespace App\Repositories;

use App\Http\Utils\ProfileTypesUtils;

//Models
use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Class AuthRepository.
 */
class AuthRepository
{
	/**
	 * Salva as permissões do usuário como ativas ao cadastrar um usuário
	 */
	public function attachPermissions(User $user)
	{
		$menuPermissions = [];
		$menus = Menu::where('is_active', TRUE)->get();

		foreach ($menus as $menu) 
		{
			foreach ($menu->permissions_ids as $permissionId) {
				array_push($menuPermissions, [
					'menu_id' 		=> $menu->id,
					'permission_id' => $permissionId,
					'user_id'		=> $user->id
				]);
			}
		}
		MenuPermission::insert($menuPermissions);
	}

	/**
	 * Obtém as permissões do usuário
	 */
	public function getPermissionsByUser(User $user)
	{
		$allMenus = Menu::orderBy('id')->get();
		$permissionsChecked = [];
		$menusChecked = [];

		foreach ($allMenus as $allMenu)
		{
			$permissionsIds = $allMenu->permissions_ids;

			$permissions = MenuPermission::where('menu_id', $allMenu->id)
				->where('user_id', $user->id)
				->whereIn('permission_id', $permissionsIds)
				->get(['menu_id', 'permission_id', 'permission_is_active as checked']);

			$menus = MenuPermission::where('menu_id', $allMenu->id)
				->where('user_id', $user->id)
				->first(['menu_id', 'menu_is_active as checked']);
		
			array_push($permissionsChecked, $permissions);
			array_push($menusChecked, $menus);

		}

		return [
			"permissions_checked" => $permissionsChecked,
			"menus_checked"       => $menusChecked
		];		
	}

	/**
	 * Obtém o link da logomarca do usuário
	 */
	public function getLogoByUser(Int $userId)
	{
		return User::where('id', $userId)->first()->logo;
	}
}
