<?php

namespace App\Repositories;

use App\Http\Utils\ProfileTypesUtils;

//Models
use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;
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
		$menuPermissions = MenuPermission::where('user_id', $user->id)->get();
		$menuIds = $menuPermissions->where('menu_is_active', true)->pluck('menu_id')->toArray();
		$menus = Menu::whereIn('id', $menuIds)->get();

		foreach ($menus as $menu) {
			$menuPermissionsUser = $menuPermissions->where('menu_id', $menu->id)
				->where('permission_is_active');

			$permissionsIds = $menuPermissionsUser->pluck('permission_id')->toArray();
			$permissions = Permission::whereIn('id', $permissionsIds)->get();
			$menu->permissions = $permissions;
		}

		return $menus;
	}

	/**
	 * Obtém o link da logomarca do usuário
	 */
	public function getLogoByUser(Int $userId)
	{
		$path = 'public/images/' . $userId;
		$existDirectory = Storage::exists($path);

		if ($existDirectory) {
			return asset('/storage/images/' . $userId . '/logo');
		}
	}
}
