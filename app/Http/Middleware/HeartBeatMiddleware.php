<?php

namespace App\Http\Middleware;

use App\Http\Transformers\HeartTransformer;
use App\Models\Heart;
use Closure;
use Auth;
use Log;
use Spatie\Fractal\ArraySerializer;

class HeartBeatMiddleware
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
//        return $next($request);
        try
        {
            $response = $next($request);
            if (method_exists($response, 'getData'))
            {
                $body = $response->getData();
                if (isset($body) && property_exists($body, 'data'))
                {
                    $data = $body->data;
                    if ($data && gettype($data) == "object" && !property_exists($data, 'heart'))
                    {
                        try
                        {
                            $user = Auth::user();
                        }
                        catch (\Exception $e)
                        {
                            $user = null;
                        }
                        $heart = new Heart();
                        $heart->version = 0;

                        if ($user && $user->heart)
                        {
                            $heart = $user->heart;
                        }

                        $heart_json = fractal()
                            ->item($heart)
                            ->transformWith(new HeartTransformer())
                            ->serializeWith(new ArraySerializer())
                            ->toArray();

                        $data->heart = $heart_json;
                        $body->data = $data;

                        $response->setData($body);
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            throw $e;
        }

        return $response;
    }
}
