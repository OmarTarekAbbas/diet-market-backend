<?php

namespace App\Modules\Users\Middleware;

use Closure;
use Auth as BaseAuth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Auth
{
    /**
     * Application key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Application type
     *
     * @var string
     */
    protected $appType;

    /**
     * List of ignored routes that should not have any auth which is not RECOMMENDED for senstive data
     *
     * @var array
     */
    protected $ignoredRoutes = [];

    /**
     * Routes that does not have permissions in admin app
     *
     * @var array
     */
    protected $ignoredAdminRoutes = ["/api/admin/login"];

    /**
     * Routes that does not have permissions in site app
     *
     * @var array
     */
    protected $ignoredSiteRoutes = ['/api/login', '/api/register'];

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        // pred($_SERVER);
        $this->apiKey = config('app.api-key');

        $uri = $request->uri();

        if (Str::startsWith($uri, '/api')) {
            $uri = Str::replaceFirst('/api', '', $uri);
        }

        // set default auth
        if (Str::startsWith($uri, '/admin')) {
            $this->appType = 'admin';
        } elseif (Str::startsWith($uri, '/clubManagers')) {
            $this->appType = 'clubManagers';
        } elseif (Str::startsWith($uri, '/restaurantManager')) {
            $this->appType = 'restaurantManager';
        } elseif (Str::startsWith($uri, '/nutritionSpecialistMangers')) {
            $this->appType = 'nutritionSpecialistMangers';
        } elseif (Str::startsWith($uri, '/deliveryMen')) {
            $this->appType = 'deliveryMen';
        } elseif (Str::startsWith($uri, '/storeManagers')) {
            $this->appType = 'storeManager';
        } else {
            $this->appType = 'site';
        }


        $guardInfo = config('auth.guards.' . $this->appType);

        config([
            'auth.defaults.guard' => $this->appType,
            'app.type' => $this->appType,
            'app.users-repo' => $guardInfo['repository'] ?? 'users',
            'app.user-type' => $guardInfo['type'] ?? 'user',
        ]);

        return $this->middleware($request, $next);
    }

    /**
     * {@inheritDoc}
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    protected function middleware(Request $request, Closure $next)
    {
        $ignoredRoutes = $this->appType == 'admin' ? $this->ignoredAdminRoutes : $this->ignoredSiteRoutes;

        if (in_array($request->uri(), $ignoredRoutes)) {
            if ($request->authorizationValue() !== $this->apiKey) {
                return response([
                    'error' => 'Invalid API Key',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return $next($request);
        } else {
            // validate if and only if the authorization access token is sent
            $authorization = $request->authorization();

            if (!$authorization) {
                return response([
                    'error' => 'Authorization Header Is Missing',
                ], Response::HTTP_UNAUTHORIZED);
            }
            [$tokenType, $accessToken] = $authorization;

            if ($tokenType == 'Bearer') {
                $user = repo(config('app.users-repo'))->getByAccessToken($accessToken);

                if ($user) {
                    BaseAuth::login($user);

                    return $next($request);
                } else {
                    return response([
                        'error' => 'Invalid Bearer Token',
                    ], Response::HTTP_UNAUTHORIZED);
                }
            } else {
                if ($accessToken != $this->apiKey) {
                    return response([
                        'error' => 'Invalid Api Key',
                    ], Response::HTTP_UNAUTHORIZED);
                }

                return $next($request);
            }
        }
    }
}
