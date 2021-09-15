<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuPermission\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\MenuPermissionRepository;

class MenuPermissionController extends Controller
{
	public function __construct(MenuPermissionRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Obtém os menus e suas permissões
	 */
	public function get()
	{
		$data = $this->repository->getAllMenuPermissions();

		return response()->json([
			'status_code' 	=>  StatusCodeUtils::SUCCESS,
			'data' 			=>  $data,
		]);
	}

	/**
	 * Atualiza os menus e suas permissões
	 */
	public function update(Update $request)
	{
		$menuPermissions = $request->menu_permissions_array;
		$this->repository->updateMenuPermissions($menuPermissions);

		return response()->json([
			'status_code' 	=>  StatusCodeUtils::SUCCESS,
		]);
	}
}
