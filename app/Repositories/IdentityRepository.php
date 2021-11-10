<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class IdentityRepository 
{
	public function upload($image, Int $userId)
	{
		$path = 'public/images/' . $userId;
		$existDirectory = Storage::exists($path);

		if ($existDirectory) {
			Storage::deleteDirectory($path);
		}

		$imagePath = '/public/images/' . $userId;
		$image->storeAs($imagePath, 'logo');

		return asset('/storage/images/' . $userId . '/' . 'logo');
	}
}
