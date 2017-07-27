<?php
declare(strict_types=1);

namespace Tmilos\Xero;

use Invoiced\OAuth1\Client\Server\Xero;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use Tmilos\Xero\Application\BaseApplication;
use Tmilos\Xero\Store\CredentialStoreInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class PublicApplication extends BaseApplication
{
    /** @var CredentialStoreInterface */
    private $credentialStore;

    /** @var TokenCredentials */
    private $tokenCredentials;

    /**
     * @param string $identifier
     * @param string $secret
     * @param string $callbackUri
     */
    public function __construct(string $identifier, string $secret, string $callbackUri, CredentialStoreInterface $credentialStore)
    {
        $this->server = new Xero([
            'identifier' => $identifier,
            'secret' => $secret,
            'callback_uri' => $callbackUri,
            'partner' => false,
        ]);
        $this->credentialStore = $credentialStore;
    }

    /**
     * @return RedirectResponse
     */
    public function authorize() : RedirectResponse
    {
        $temporaryCredentials = $this->server->getTemporaryCredentials();
        $this->credentialStore->save(CredentialStoreInterface::TEMPORARY, $temporaryCredentials);

        return new RedirectResponse($this->server->getAuthorizationUrl($temporaryCredentials));
    }

    /**
     * @param ServerRequest $request
     *
     * @return bool
     */
    public function handleCallback(ServerRequest $request) : boolean
    {
        $query = $request->getQueryParams();
        if (isset($query['oauth_token']) && isset($query['oauth_verifier'])) {
            // Retrieve the temporary credentials we saved before
            $temporaryCredentials = $this->credentialStore->get(CredentialStoreInterface::TEMPORARY);

            if ($temporaryCredentials instanceof TemporaryCredentials) {
                // We will now retrieve token credentials from the server
                $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
                $this->credentialStore->save(CredentialStoreInterface::TOKEN, $tokenCredentials);

                return true;
            }
        }

        return false;
    }
}
