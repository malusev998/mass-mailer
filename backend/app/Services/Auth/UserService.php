<?php


namespace App\Services;


use App\Contracts\UserContract;
use App\Dto\CreateUser;
use App\Notifications\UserRegistered;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Tymon\JWTAuth\Manager;

class UserService implements UserContract
{
    protected Hasher $hasher;
    protected Manager $manager;
    protected UrlGenerator $urlGenerator;

    public function __construct(Hasher $hasher, Manager $manager, UrlGenerator $urlGenerator)
    {
        $this->hasher = $hasher;
        $this->manager = $manager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws Throwable
     *
     * @param CreateUser $createUser
     *
     * @return User
     */
    public function createUser(CreateUser $createUser): User
    {
        $randomPassword = Str::random(16);

        $user = new User(
            [
                'name'     => $createUser->name,
                'surname'  => $createUser->surname,
                'email'    => $createUser->email,
                'password' => $this->hasher->make($randomPassword),
            ]
        );

        $user->saveOrFail();

        $user->notify(new UserRegistered($user));


        return $user;
    }

    public function deleteUser(int $id): bool
    {
        return DB::transaction(
            static function () use ($id) {
                return User::destroy($id) > 0;
            }
        );
    }

    public function updateUserAccount(User $user)
    {
        // TODO: Implement updateUserAccount() method.
    }


}
