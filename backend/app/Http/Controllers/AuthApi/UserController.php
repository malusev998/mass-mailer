<?php

namespace App\Http\Controllers\AuthApi;

use App\Contracts\User\ChangeImageContract;
use App\Contracts\UserContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeImageRequest;
use Exception;

class UserController extends Controller
{
    private $userService;
    private $changeImageContract;

    public function __construct(UserContract $userContract, ChangeImageContract $changeImageContract)
    {
        $this->userService = $userContract;
        $this->changeImageContract = $changeImageContract;
    }

    public function create()
    {
    }

    public function changeImage(ChangeImageRequest $request)
    {
        try {
            $type = $request->input('type');
            $file = null;

            switch ($type) {
                case ChangeImageContract::AVATAR:
                    $file = $request->file('avatar');
                    break;
                case ChangeImageContract::BACKGROUND_IMAGE:
                    $file = $request->file('background');
                    break;
            }

            $image = $this->changeImageContract->changeImage($type, $request->user(), $file);
            return ok(['image' => $image]);
        } catch (Exception $e) {
            return internalServerError(['message' => $e->getMessage()]);
        }
    }

    public function delete(int $id)
    {
    }
}