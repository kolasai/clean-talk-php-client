<?php

namespace Tests;

use CleanTalk\RequestExceptionHandler;
use CleanTalk\Exception\CleanTalkException;
use CleanTalk\Exception\ProjectNotFoundException;
use CleanTalk\Exception\ProjectDatasetNotConfiguredException;
use CleanTalk\Exception\ProjectNotActiveException;
use CleanTalk\Exception\AccountBalanceEmptyException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class RequestExceptionHandlerTest extends TestCase
{
    private const HTTP_UNAUTHORIZED = 401;
    private const HTTP_NOT_FOUND = 404;
    private const HTTP_UNPROCESSABLE_ENTITY = 422;
    private const HTTP_INTERNAL_SERVER_ERROR = 500;

    public function testHandle401ThrowsCleanTalkException(): void
    {
        $this->expectException(CleanTalkException::class);
        $this->expectExceptionMessage('Unauthorized: Invalid access token.');

        RequestExceptionHandler::handle($this->makeRequestException(self::HTTP_UNAUTHORIZED));
    }

    public function testHandle500ThrowsCleanTalkException(): void
    {
        $this->expectException(CleanTalkException::class);
        $this->expectExceptionMessage('Internal Server Error: Please try again later or contact technical support.');

        RequestExceptionHandler::handle($this->makeRequestException(self::HTTP_INTERNAL_SERVER_ERROR));
    }

    public function testHandleOtherStatusThrowsCleanTalkExceptionWithBody(): void
    {
        $this->expectException(CleanTalkException::class);
        $this->expectExceptionMessage('Predict request failed: Not found');

        RequestExceptionHandler::handle($this->makeRequestException(self::HTTP_NOT_FOUND, 'Not found'));
    }

    public function testHandleNoResponseThrowsCleanTalkExceptionWithMessage(): void
    {
        $this->expectException(CleanTalkException::class);
        $this->expectExceptionMessage('Predict request failed: No response');

        RequestExceptionHandler::handle(
            new RequestException(
                'No response',
                $this->createMock(RequestInterface::class),
                null
            )
        );
    }

    /**
     * @dataProvider provide422ErrorCases
     */
    public function testHandle422ErrorCodes(
        string $errorCode,
        string $message,
        string $expectedException,
        string $expectedMessage,
        ?array $extraErrors = null
    ): void {
        $this->expectException($expectedException);

        $bodyArr = ['errorCode' => $errorCode, 'message' => $message];

        if ($extraErrors !== null) {
            $bodyArr['errors'] = $extraErrors;
        }

        if ($expectedException === CleanTalkException::class && $extraErrors !== null) {
            $this->expectExceptionMessageMatches($expectedMessage);
        } else {
            $this->expectExceptionMessage($expectedMessage);
        }

        RequestExceptionHandler::handle(
            $this->makeRequestException(
                self::HTTP_UNPROCESSABLE_ENTITY,
                json_encode($bodyArr)
            )
        );
    }

    public static function provide422ErrorCases(): array
    {
        return [
            'project not found' => [
                ProjectNotFoundException::CODE,
                'Project not found!',
                ProjectNotFoundException::class,
                'Project not found!',
            ],
            'dataset not configured' => [
                ProjectDatasetNotConfiguredException::CODE,
                'Dataset not configured!',
                ProjectDatasetNotConfiguredException::class,
                'Dataset not configured!',
            ],
            'project not active' => [
                ProjectNotActiveException::CODE,
                'Project not active!',
                ProjectNotActiveException::class,
                'Project not active!',
            ],
            'account balance empty' => [
                AccountBalanceEmptyException::CODE,
                'Balance empty!',
                AccountBalanceEmptyException::class,
                'Balance empty!',
            ],
            'unknown code' => [
                '9999',
                'Unknown error',
                CleanTalkException::class,
                '/Validation Error: Unknown error/',
                ['foo' => 'bar'],
            ],
        ];
    }

    private function makeRequestException(int $status, ?string $body = null): RequestException
    {
        return new RequestException(
            'Error',
            $this->createMock(RequestInterface::class),
            $body !== null ? new Response($status, [], $body) : new Response($status)
        );
    }
}
