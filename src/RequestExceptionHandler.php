<?php

namespace CleanTalk;

use GuzzleHttp\Exception\RequestException;
use CleanTalk\Exception\AccountBalanceEmptyException;
use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Exception\ProjectDatasetNotConfiguredException;
use CleanTalk\Exception\ProjectNotActiveException;
use CleanTalk\Exception\ProjectNotFoundException;

class RequestExceptionHandler
{
    private const HTTP_UNAUTHORIZED = 401;
    private const HTTP_UNPROCESSABLE_ENTITY = 422;
    private const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Handles HTTP request exceptions and throws appropriate custom exceptions.
     *
     * @param RequestException $e
     * @return void
     * @throws CleanTalkException
     */
    public static function handle(RequestException $e): void
    {
        if ($e->hasResponse()) {
            switch ($e->getResponse()->getStatusCode()) {
                case self::HTTP_UNAUTHORIZED:
                    throw new CleanTalkException('Unauthorized: Invalid access token.', 0, $e);
                case self::HTTP_UNPROCESSABLE_ENTITY:
                    self::createException($e);
                case self::HTTP_INTERNAL_SERVER_ERROR:
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
    private static function createException(RequestException $e): void
    {
        $response = $e->getResponse();
        if ($response === null) {
            throw new CleanTalkException('No response received from server.', 0, $e);
        }
        $errors = json_decode($response->getBody()->getContents(), true);
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
