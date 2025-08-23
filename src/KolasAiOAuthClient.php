<?php

namespace CleanTalk;

use GuzzleHttp\Client;
use CleanTalk\Exception\CleanTalkException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;

class KolasAiOAuthClient
{
    private const BASE_URL = 'https://app.kolas.ai';
    private const AUTH_ENDPOINT = 'oauth/token';

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => self::BASE_URL]);
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @return AuthResult
     * @throws CleanTalkException
     */
    public function auth(string $clientId, string $clientSecret): AuthResult
    {
        try {
            $response = $this->httpClient->request('POST', self::AUTH_ENDPOINT, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
                'headers' => ['Accept' => 'application/json'],
            ]);
        } catch (RequestExceptionInterface|ClientExceptionInterface $e) {
            throw new CleanTalkException("Auth request failed.", 0, $e);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['access_token'])) {
            throw new CleanTalkException('OAuth2 authentication failed: ' . json_encode($data));
        }

        return AuthResult::fromArray($data);
    }
}
