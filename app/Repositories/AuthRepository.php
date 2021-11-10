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
		$menus = Menu::where('profile_type_id', $user->is_advocate ? 1 : 2)->orderBy('id')->get();
		$permissionsChecked = [];
		$menusChecked = [];

		foreach ($menus as $menu)
		{
			$permissionsIds = $menu->permissions_ids;

			$permissions = MenuPermission::where('menu_id', $menu->id)
				->where('user_id', $user->id)
				->whereIn('permission_id', $permissionsIds)
				->get(['menu_id', 'permission_id', 'permission_is_active as checked']);

			$teste = MenuPermission::where('menu_id', $menu->id)
				->where('user_id', $user->id)
				->first(['menu_id', 'menu_is_active as checked']);
		
			array_push($permissionsChecked, $permissions);
			array_push($menusChecked, $teste);

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
		$path = 'public/images/' . $userId;
		$existDirectory = Storage::exists($path);

		if ($existDirectory) {
			return asset('/storage/images/' . $userId . '/logo');
		}
	}
}
