<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [];
    public function __construct(EncrypterContract $contract)
    {
        parent::__construct($contract);
        $this->except[] = config('session.upstream_session_key');
    }
}
