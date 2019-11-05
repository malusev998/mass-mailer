<?php


namespace App\Services;


use App\Contracts\LoginContract;
use App\Contracts\Signer\RsaSignerContract;
use App\Dto\Login;
use App\Exceptions\IncorrectPassword;
use App\Exceptions\RefreshTokenExpired;
use App\Exceptions\RefreshTokenNotFound;
use App\RefreshToken;
use App\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Tymon\JWTAuth\JWTAuth;

class LoginService implements LoginContract
{
    /**
     * @var JWTAuth
     */
    private $auth;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var RsaSignerContract
     */
    private $rsaSigner;

    /**
     * @var Config
     */
    private $config;


    public function __construct(JWTAuth $auth, Hasher $hasher, RsaSignerContract $rsaSigner, Config $config)
    {
        $this->auth = $auth;
        $this->hasher = $hasher;
        $this->rsaSigner = $rsaSigner;
        $this->config = $config;
    }

    /**
     * @param Login $login
     * @return array
     * @throws IncorrectPassword
     * @throws Throwable
     */
    public function login(Login $login): array
    {

        /** @var User $user */
        $user = User::query()
            ->where('email', '=', $login->email)
            ->firstOrFail();

        // TODO: Add check for email verification

        if(!$this->hasher->check($login->password, $user->password)) {
            throw new IncorrectPassword();
        }

        return DB::transaction(function () use ($user) {

            $rf = new RefreshToken();

            $refreshToken = $this->generateNewRefreshToken($rf);

            $user->refreshTokens()->save($rf);
            return [
                'user' => $user->getJWTCustomClaims(),
                'token' => [
                    'token' => $this->auth->fromSubject($user),
                    'refreshToken' => $refreshToken,
                    'authType' => 'Bearer',
                ]
            ];
        });



    }


    /**
     * @param string| null $refreshToken
     * @return array
     * @throws RefreshTokenExpired
     * @throws ModelNotFoundException
     * @throws RefreshTokenNotFound
     * @throws Throwable
     */
    public function refreshToken(?string $refreshToken): array
    {
        if($refreshToken === null) {
            throw new RefreshTokenNotFound();
        }

        /** @var RefreshToken $rf */
        $rf = RefreshToken::query()
            ->with(['user'])
            ->where('token', '=', $refreshToken)
            ->firstOrFail();

        if(now()->isAfter($rf->expires)) {
            throw new RefreshTokenExpired();
        }

        return [
            'token' => $this->auth->fromUser($rf->user),
            'refreshToken' => $this->generateNewRefreshToken($rf),
            'authType' => 'Bearer'
        ];
    }

    /**
     * @param RefreshToken $refreshToken
     * @param int $length
     * @return string
     * @throws Throwable
     */
    private function generateNewRefreshToken(RefreshToken $refreshToken, int $length = 25): string {

        $refreshToken->token = $this->rsaSigner->sign(Str::random($length));
        $refreshToken->expires = now()->addMinutes($this->config->get('jwt.refresh_ttl'));
        $refreshToken->saveOrFail();
        return $refreshToken->token;
    }
}
