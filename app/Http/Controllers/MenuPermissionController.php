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
     * @OA\Get(
     *     tags={"MenuPermissions"},
     *     summary="Obtém os menus e as permissões do sistema",
     *     description="Obtém os menus e as permissões do menu do sistema",
     *     path="/menu/permissions",
	 * 	   security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Menus e permissões."),
     * ),
     * 
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
     * @OA\Post(
     *     tags={"MenuPermissions"},
     *     summary="Atualiza os menus e as permissões do usuário",
     *     description="Atualiza os menus e as permissões do usuário",
     *     path="/menu/permissions",
	 *     security={{"bearerAuth": {}}},
     *     @OA\Response(response="200", description="Menus e permissões."),
	 *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="menu_permissions", type="object"),
     *          )
     *      ),
     * ),
     * 
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
