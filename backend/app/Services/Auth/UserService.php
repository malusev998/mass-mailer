<?php


namespace App\Services;


use App\Notifications\RequestNewPassword;
use App\User;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;
use App\Contracts\UserContract;
use Illuminate\Support\Facades\DB;
use App\Dto\ChangePassword as ChangePasswordDto;
use App\Dto\CreateUser;
use App\Notifications\ChangePassword;
use App\Notifications\UserRegistered;
use Illuminate\Contracts\Hashing\Hasher;
use Throwable;
use Tymon\JWTAuth\Manager;

class UserService implements UserContract
{
    private $hasher;
    private $manager;
    private $urlGenerator;

    public function __construct(Hasher $hasher, Manager $manager, UrlGenerator $urlGenerator)
    {
        $this->hasher = $hasher;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param CreateUser $createUser
     * @return User
     * @throws Throwable
     */
    public function createUser(CreateUser $createUser): User
    {
        $randomPassword = Str::random(16);

        $user = new User([
            'name' => $createUser->name,
            'surname' => $createUser->surname,
            'email' => $createUser->email,
            'password' => $this->hasher->make($randomPassword)
        ]);

        $user->saveOrFail();

        $user->notify(new UserRegistered($user));
        $user->notify(new ChangePassword(ChangePasswordDto::fromUser($user), $this->manager));


        return $user;
    }

    public function deleteUser(int $id): bool
    {
        return DB::transaction(static function () use ($id) {
           return User::destroy($id) > 0;
        });
    }

    public function updateUserAccount(User $user)
    {
        // TODO: Implement updateUserAccount() method.
    }


}