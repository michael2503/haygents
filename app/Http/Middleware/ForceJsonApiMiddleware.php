<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $rawContent = $request->getContent();

            if (is_string($rawContent) && trim($rawContent) !== '') {
                $decoded = json_decode($rawContent, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Malformed JSON body. All API requests must send valid JSON in the request body.',
                    ], 400);
                }

                // Ensure raw JSON bodies are always parsed even if Content-Type: application/json was omitted
                if (!$request->isJson()) {
                    $request->merge($decoded);
                }
            }
        }

        return $next($request);
    }
}
