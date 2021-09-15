<?php

namespace App\Repositories;

use App\Http\Utils\ProfileTypesUtils;

//Models
use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\User;

/**
 * Class AuthRepository.
 */
class AuthRepository
{
	/**
	 * Salva as permissões do usuário como ativas ao cadastrar um usuário
	 */
	public function attachPermissions(User $user, String $isAdvocate)
	{
		$profileTypeId = ProfileTypesUtils::CLIENT;

		if ($isAdvocate) {
			$profileTypeId = ProfileTypesUtils::ADVOCATE;
		}

		$menuPermissions = [];
		$menus = Menu::where('profile_type_id', $profileTypeId)->where('is_active', TRUE)->get();

		foreach ($menus as $menu) {
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
		return MenuPermission::where('user_id', $user->id)->get();
	}
}
