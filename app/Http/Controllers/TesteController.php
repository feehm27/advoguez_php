<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TesteController extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function teste()
	{
		try {
			echo ("oi, sou um teste");
		} catch (\Exception $e) {
			dd($e);
		}
	}
}
