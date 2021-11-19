<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuPermission\Update;
use App\Http\Utils\StatusCodeUtils;
use App\Repositories\MenuPermissionRepository;
use Exception;

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
		try {
			$data = $this->repository->getAllMenuPermissions();

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data' 			=>  $data,
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Atualiza os menus e suas permissões
	 */
	public function update(Update $request)
	{
		try {

			$menuPermissions = $request->menus_permissions;
			$user = $request->user;

			$this->repository->updateMenuPermissions($menuPermissions, $user);

			return response()->json([
				'status_code' 	=>  StatusCodeUtils::SUCCESS,
				'data'          => []
			]);
		} catch (Exception $error) {
			return response()->json(['error' => $error->getMessage()], StatusCodeUtils::INTERNAL_SERVER_ERROR);
		}
	}
}
