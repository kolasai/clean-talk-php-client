<?php

namespace CleanTalk;

use CleanTalk\Exception\CleanTalkException;

class AuthResult
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $tokenType;
    /**
     * @var string
     */
    private $expiresIn;

    /**
     * @param string[] $data
     * @return self
     * @throws CleanTalkException
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['access_token']) || empty($data['token_type']) || empty($data['expires_in'])) {
            throw new CleanTalkException('Invalid data for AuthResult');
        }

        $obj = new self();
        $obj->accessToken = $data['access_token'];
        $obj->tokenType = $data['token_type'];
        $obj->expiresIn = $data['expires_in'];
        return $obj;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): string
    {
        return $this->expiresIn;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }
}
