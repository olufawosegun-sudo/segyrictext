<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Middleware\ValidatePostSize;

class SafeValidatePostSize extends ValidatePostSize
{
    /**
     * Handle an incoming request with safe numeric parsing for Content-Length headers.
     * Fixes PHP 8 string comparison bug when reverse proxies duplicate Content-Length headers (e.g. "89, 89").
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\PostTooLargeException
     */
    public function handle($request, Closure $next)
    {
        $max = $this->getPostMaxSize();

        if ($max > 0) {
            $rawContentLength = $request->server('CONTENT_LENGTH');

            if ($rawContentLength !== null && $rawContentLength !== '') {
                // If proxy duplicated header (e.g., "89, 89"), extract first numeric value
                if (is_string($rawContentLength) && str_contains($rawContentLength, ',')) {
                    $parts = explode(',', $rawContentLength);
                    $rawContentLength = trim($parts[0]);
                }

                $contentLength = (int) $rawContentLength;

                if ($contentLength > $max) {
                    throw new PostTooLargeException('The POST data is too large.');
                }
            }
        }

        return $next($request);
    }
}
