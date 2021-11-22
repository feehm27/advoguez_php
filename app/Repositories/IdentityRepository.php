<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class IdentityRepository 
{
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function upload($image, Int $userId)
	{
		$path = $path = 'images/'. $userId;
		$existDirectory = Storage::disk('public')->exists($path);

		if ($existDirectory) {
			Storage::deleteDirectory($path);
		}

		$imagePublic = Storage::disk('public')->put($path, $image);
		$urlPublic = Storage::url($imagePublic);

		$assetUrl = asset($urlPublic);
		$urlPublic = str_replace("/storage", "", $assetUrl);
		
		$updatedLogo = [
			'id'  	=> $userId,
			'logo'	 => $urlPublic
		];

		$this->userRepository->update($updatedLogo);

		return $urlPublic;
	}
}
