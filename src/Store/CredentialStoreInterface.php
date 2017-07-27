<?php

namespace Tmilos\Xero\Store;

use League\OAuth1\Client\Credentials\CredentialsInterface;

interface CredentialStoreInterface
{
    const TEMPORARY = 'temporary';
    const TOKEN = 'token';

    public function save($name, CredentialsInterface $credentials);

    public function get($name) : CredentialStoreInterface;
}
