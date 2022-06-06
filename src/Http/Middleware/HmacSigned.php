<?php

namespace Idez\Bankly\Http\Middleware;

use App\Exceptions\BanklySignatureMismatchException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HmacSigned
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null ...$guards
     * @return mixed
     * @throws BanklySignatureMismatchException
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $encodedString = implode('#', [
            $request->header('PublicKey'),
            $request->getUri(),
            $request->header('RequestTimestamp'),
            $request->header('Nonce'),
            base64_encode($request->getContent()),
        ]);

        $hmacSignature = base64_encode(hash_hmac(
            algo: 'sha256',
            data: $encodedString,
            key: config('bankly.webhooks.hmac_salt'),
            binary: true
        ));

        $hmacToken = Str::of($request->header('Authorization'))->substr(5)->trim()->__toString();
        if ($hmacToken !== $hmacSignature) {
            throw new BanklySignatureMismatchException();
        }

        return $next($request);
    }
}
