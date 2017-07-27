<?php
declare(strict_types=1);

namespace Tmilos\Xero;

use Invoiced\OAuth1\Client\Server\Xero;
use Tmilos\Xero\Application\BaseApplication;

class PartnerApplication extends BaseApplication
{
    public function __construct(
        string $identifier,
        string $secret,
        string $callbackUri,
        $rsaPrivateKey,
        $rsaPublicKey,
        $httpTrustCertificate,
        $httpTrustPrivateKey,
        $httpTrustPassword,
        $httpVerify = false
    ) {
        $this->server = new Xero([
            'identifier' => $identifier,
            'secret' => $secret,
            'callback_uri' => $callbackUri,
            'partner' => false,
            'rsa_private_key' => $rsaPrivateKey,
            'rsa_public_key'  => $rsaPublicKey,
            'http_client'     => [
                'cert'     => $httpTrustCertificate,
                'ssl_key'  => [$httpTrustPrivateKey, $httpTrustPassword],
                // certificate verification would require installing Xero's certificate issuer to the trust store
                'verify'   => $httpVerify,
            ],
        ]);
    }
}
