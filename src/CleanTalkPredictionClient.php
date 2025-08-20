<?php

namespace CleanTalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use CleanTalk\Exception\AccountBalanceEmptyException;
use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Exception\ProjectDatasetNotConfiguredException;
use CleanTalk\Exception\ProjectNotActiveException;
use CleanTalk\Exception\ProjectNotFoundException;

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
        $this->httpClient = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 10.0,
        ]);
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

        $body = [
            'projectId' => $request->getProjectId(),
            'messages' => array_map(static function (Message $message) {
                return [
                    'messageId' => $message->getMessageId(),
                    'message' => $message->getText(),
                ];
            }, $request->getMessages()),
        ];

        try {
            $response = $this->httpClient->post(self::SYNC_PREDICT_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);


            $data = json_decode($response->getBody()->getContents(), true);

            return PredictResponse::fromArray($data);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
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

        $body = [
            'projectId' => $request->getProjectId(),
            'messages' => array_map(static function (Message $message) {
                return [
                    'messageId' => $message->getMessageId(),
                    'message' => $message->getText(),
                ];
            }, $request->getMessages()),
        ];

        try {
            $this->httpClient->post(self::ASYNC_PREDICT_ENDPOINT, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        } catch (GuzzleException $e) {
            throw new CleanTalkException("Async predict request failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Handles HTTP request exceptions and throws appropriate custom exceptions.
     *
     * @param RequestException $e
     * @return void
     * @throws CleanTalkException
     */
    private function handleRequestException(RequestException $e): void
    {
        if ($e->hasResponse()) {
            switch ($e->getResponse()->getStatusCode()) {
                case 401:
                    throw new CleanTalkException('Unauthorized: Invalid access token.', 0, $e);
                case 422:
                    $this->createException($e);
                case 500:
                    throw new CleanTalkException(
                        'Internal Server Error: Please try again later or contact technical support.',
                        0,
                        $e
                    );
                default:
                    throw new CleanTalkException(
                        "Predict request failed: " . $e->getResponse()->getBody()->getContents(),
                        0,
                        $e
                    );
            }
        }

        throw new CleanTalkException("Predict request failed: " . $e->getMessage(), 0, $e);
    }

    /**
     * Creates and throws specific exceptions based on API error codes.
     *
     * @param RequestException $e
     * @return void
     * @throws AccountBalanceEmptyException
     * @throws CleanTalkException
     * @throws ProjectDatasetNotConfiguredException
     * @throws ProjectNotActiveException
     * @throws ProjectNotFoundException
     */
    private function createException(RequestException $e): void
    {
        $errors = json_decode($e->getResponse()->getBody()->getContents(), true);
        $code = $errors['errorCode'] ?? '0';
        $message = $errors['message'] ?? 'Invalid request data.';
        $errors = $errors['errors'] ?? [];

        switch ($code) {
            case ProjectNotFoundException::CODE:
                throw new ProjectNotFoundException($message);
            case ProjectDatasetNotConfiguredException::CODE:
                throw new ProjectDatasetNotConfiguredException($message);
            case ProjectNotActiveException::CODE:
                throw new ProjectNotActiveException($message);
            case AccountBalanceEmptyException::CODE:
                throw new AccountBalanceEmptyException($message);
            default:
                throw new CleanTalkException(
                    'Validation Error: ' . $message . ' ' . json_encode($errors),
                    $code
                );
        }
    }
}
