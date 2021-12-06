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
		
		if($image) {

			$path = $userId.'/logo';
			Storage::disk('s3')->deleteDirectory($path);
	
			$upload = Storage::disk('s3')->put($path, $image);
			$urlPublic = Storage::disk('s3')->url($upload);
		
			$inputs = ['id' => $userId, 'logo'	=> $urlPublic];
	
			$this->userRepository->update($inputs);
	
			return $urlPublic;

		}else{

			$path = $userId.'/logo';
			Storage::disk('s3')->deleteDirectory($path);

			$inputs = ['id' => $userId, 'logo'	=> null];
			$this->userRepository->update($inputs);
			
			return env('DEFAULT_LOGO');
		}
	}
}
