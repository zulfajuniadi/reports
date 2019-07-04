<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Cookie;

use Closure;

class CheckRemoteAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }
        $rawCookies = [
            'session' => $request->cookie(config('session.upstream_session_key'))
        ];
        $userAgent = $request->userAgent();
        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray($rawCookies, $request->getHttpHost());
        $client = new \GuzzleHttp\Client(['base_uri' => config('session.upstream_server') . '/check-auth']);
        try {
            $results = $client->request('GET', '/check-auth', [
                    'headers' => [
                        'User-Agent' => $userAgent,
                    ],
                    'cookies' => $cookieJar
                ]);
        } catch (\Exception $e) {
            \Log::info('guzzle_error', [$e]);
            return $next($request);
        }
        
        if (!json_decode($results->getBody()->__toString())) {
            \Log::info('invalid_cookie');
            return redirect(config('session.upstream_server'))->withCookie(\Cookie::forget(config('session.upstream_session_key')));
        }
                
        $response = $next($request);
        $cookies = $cookieJar->toArray();
        foreach ($cookies as $cookieData) {
            $cookie = new Cookie($cookieData['Name'], $cookieData['Value'], $cookieData['Expires'], $cookieData['Path'], $cookieData['Domain'], $cookieData['Secure'], $cookieData['HttpOnly']);
            // fix error on file download
            if (get_class($response) != \Symfony\Component\HttpFoundation\BinaryFileResponse::class) {
                $response->cookie($cookie);
            }
        }
        return $response;
    }
}
