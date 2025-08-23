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

        $self = new self();
        $self->accessToken = $data['access_token'];
        $self->tokenType = $data['token_type'];
        $self->expiresIn = $data['expires_in'];
        return $self;
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
