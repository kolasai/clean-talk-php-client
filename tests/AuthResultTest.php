<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use CleanTalk\AuthResult;
use CleanTalk\Exception\CleanTalkException;

class AuthResultTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testFromArrayWithValidData($data, $expected): void
    {
        $result = AuthResult::fromArray($data);
        $this->assertEquals($expected['access_token'], $result->getAccessToken());
        $this->assertEquals($expected['token_type'], $result->getTokenType());
        $this->assertEquals($expected['expires_in'], $result->getExpiresIn());
    }

    public static function validDataProvider(): array
    {
        return [
            'basic valid' => [
                [
                    'access_token' => 'token123',
                    'token_type' => 'bearer',
                    'expires_in' => '3600'
                ],
                [
                    'access_token' => 'token123',
                    'token_type' => 'bearer',
                    'expires_in' => '3600'
                ]
            ]
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayWithInvalidData($data): void
    {
        $this->expectException(CleanTalkException::class);
        AuthResult::fromArray($data);
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing access_token' => [
                [
                    // 'access_token' is missing
                    'token_type' => 'bearer',
                    'expires_in' => '3600'
                ]
            ],
            'missing token_type' => [
                [
                    'access_token' => 'token123',
                    // 'token_type' is missing
                    'expires_in' => '3600'
                ]
            ],
            'missing expires_in' => [
                [
                    'access_token' => 'token123',
                    'token_type' => 'bearer',
                    // 'expires_in' is missing
                ]
            ],
            'empty access_token' => [
                [
                    'access_token' => '',
                    'token_type' => 'bearer',
                    'expires_in' => '3600'
                ]
            ],
            'empty token_type' => [
                [
                    'access_token' => 'token123',
                    'token_type' => '',
                    'expires_in' => '3600'
                ]
            ],
            'empty expires_in' => [
                [
                    'access_token' => 'token123',
                    'token_type' => 'bearer',
                    'expires_in' => ''
                ]
            ]
        ];
    }
}
