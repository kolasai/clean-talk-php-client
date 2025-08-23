<?php

namespace CleanTalk;

use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Request\Message;
use CleanTalk\Request\PredictRequest;
use CleanTalk\Response\PredictResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class CleanTalkPredictionClient
{
    private const BASE_URL = 'https://app.kolas.ai';
    private const SYNC_PREDICT_ENDPOINT = 'api/v1/predictions/predict';
    private const ASYNC_PREDICT_ENDPOINT = 'api/v1/predictions/asyncPredict';

    /**
     * @var Client
     */
    private $httpClient;
    /**
     * @var string
     */
    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->httpClient = new Client(['base_uri' => self::BASE_URL]);
    }

    /**
     * Sends a synchronous prediction request to the Kolas.Ai API.
     *
     * @param PredictRequest $request
     * @return PredictResponse
     * @throws CleanTalkException
     */
    public function predict(PredictRequest $request): PredictResponse
    {
        if (!$this->accessToken) {
            throw new CleanTalkException('Not authorized. Call CleanTalkClient::auth() first.');
        }

        try {
            $response = $this->httpClient->request('POST', self::SYNC_PREDICT_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $request,
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            return PredictResponse::fromArray($data);
        } catch (RequestException $e) {
            RequestExceptionHandler::handle($e);
        } catch (GuzzleException $e) {
            throw new CleanTalkException("Predict request failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Sends an asynchronous prediction request to the Kolas.Ai API. Responses will be sent on registered webhook.
     *
     * @param PredictRequest $request
     * @return void
     * @throws CleanTalkException
     */
    public function asyncPredict(PredictRequest $request): void
    {
        if (!$this->accessToken) {
            throw new CleanTalkException('Not authorized. Call CleanTalkClient::auth() first.');
        }

        try {
            $this->httpClient->request('POST', self::ASYNC_PREDICT_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $request,
            ]);
        } catch (RequestException $e) {
            RequestExceptionHandler::handle($e);
        } catch (ClientExceptionInterface $e) {
            throw new CleanTalkException("Async predict request failed: " . $e->getMessage(), 0, $e);
        }
    }
}
