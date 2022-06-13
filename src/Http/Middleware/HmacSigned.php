<?php

namespace Idez\Bankly\Http\Middleware;

use Idez\Bankly\Exceptions\BanklySignatureMismatchException;
use Closure;
use Illuminate\Http\Request;

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
        $separator = '&';
        $publicKey = $request->header('PublicKey');
        $requestUriEncoded = strtolower(urlencode($request->getUri()));
        $requestTimestamp = $request->header('RequestTimestamp');
        $nonce = $request->header('Nonce');
        $requestBodyBase64 = base64_encode($request->getContent());

        $preHashedString = "{$publicKey}{$separator}{$requestUriEncoded}{$separator}{$requestTimestamp}{$separator}{$nonce}{$separator}{$requestBodyBase64}";

        $hmacSignature = base64_encode(hash_hmac(
            algo: 'sha256',
            data: $preHashedString,
            key: config('bankly.webhooks.hmac_salt'),
            binary: true
        ));


        $hmacToken = trim(substr($request->header('Authorization'), 5));

        if ($hmacToken !== $hmacSignature) {
            throw new BanklySignatureMismatchException();
        }

        return $next($request);
    }
}
