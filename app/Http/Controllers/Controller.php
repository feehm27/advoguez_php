<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="https://advoguez-php.herokuapp.com/api",
 *     basePath="/api",
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Swagger Advoguez",
 *         description="Esta é uma documentação do sistema Advoguez",
 *         termsOfService="",
 *     ),
 *    @OA\Server(
 *      url="https://advoguez-php.herokuapp.com/api",
 *     ),
 *     @OA\SecurityScheme(
 *      type="http",
 *      scheme="bearer",
 *      securityScheme="bearerAuth",
 *     )
 * )
 */
