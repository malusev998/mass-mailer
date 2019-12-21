<?php


namespace App\Contracts\Applications;


use App\Application;
use App\Dto\CreateApplication;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface ApplicationContract
{
    public function getApplications(User $user, int $page, int $perPage);

    public function getApplication(User $user, int $id): Application;

    /**
     * @param CreateApplication $createApplication
     * @param User              $user
     *
     * @return Application
     */
    public function createApplication(CreateApplication $createApplication, User $user): Application;

    /**
     * @throws ModelNotFoundException
     *
     * @param User $user
     * @param int  $appId
     *
     * @return string
     */
    public function generateNewAppKey(int $appId, User $user): string;

    /**
     * @param int $appId
     *
     * @return bool
     */
    public function deleteApplication(int $appId): bool;
}
