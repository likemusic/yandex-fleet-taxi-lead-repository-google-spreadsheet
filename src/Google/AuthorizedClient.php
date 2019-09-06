<?php

namespace Likemusic\YandexFleetTaxi\LeadRepository\GoogleSpreadsheet\Google;

use Exception;
use Google_Client;
use Google_Exception;
use Google_Service_Sheets;

/**
 * Class AuthorizedClient
 */
class AuthorizedClient extends Google_Client
{
    /**
     * AuthorizedClient constructor.
     * @param string $credentialsPath
     * @param string $tokenPath
     * @param array $config
     * @throws Google_Exception
     * @throws Exception
     */
    public function __construct(string $credentialsPath, string $tokenPath, array $config = array())
    {
        $config['application_name'] = 'Google-spreadsheet based Leads repository.';

        parent::__construct($config);

        $this->setScopes(Google_Service_Sheets::SPREADSHEETS);
//        $this->setAuthConfig(__DIR__.'/../credentials.json');
        $this->setAuthConfig($credentialsPath);
        $this->setAccessType('offline');
        $this->setPrompt('select_account consent');

        $this->setAccessTokenIfAccessTokenFileExists($tokenPath);

        if (!$this->isAccessTokenExpired()) {
            return;
        }

        // Refresh the token if possible, else fetch a new one.
        if ($refreshToken = $this->getRefreshToken()) {
            $this->fetchAccessTokenWithRefreshToken($refreshToken);
        } else {
            $authCode = $this->RequestVerificationCodeFromUser();
            $this->setAccessTokenByAuthCode($authCode);
        }

        $this->saveTokenFile($tokenPath);
    }

    /**
     * @param string $tokenPath
     * @return mixed
     */
    private function setAccessTokenIfAccessTokenFileExists(string $tokenPath): ?array
    {
        if (!file_exists($tokenPath)) {
            return null;
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->setAccessToken($accessToken);

        return $accessToken;
    }

    /**
     * @param string $tokenPath
     */
    private function saveTokenFile(string $tokenPath): void
    {
// Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }

        file_put_contents($tokenPath, json_encode($this->getAccessToken()));
    }

    /**
     * @return string
     */
    private function RequestVerificationCodeFromUser(): string
    {
// Request authorization from the user.
        $authUrl = $this->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));
        return $authCode;
    }

    /**
     * @param string $authCode
     * @throws Exception
     */
    private function setAccessTokenByAuthCode(string $authCode): void
    {
// Exchange authorization code for an access token.
        $accessToken = $this->fetchAccessTokenWithAuthCode($authCode);
        $this->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }
    }
}
