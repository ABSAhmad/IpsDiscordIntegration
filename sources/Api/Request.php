<?php

namespace IPS\discord\Api;

class _Request
{
    /** @var array */
    protected $headers = [];

    /** @var array */
    protected $payload = [];

    /** @var array */
    protected $queryParameters = [];

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getHeader(string $key)
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @return self
     *
     */
    public function clearHeaders(): self
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return _Request
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     *
     * @return _Request
     */
    public function setPayload(array $payload): _Request
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getQueryParameter(string $key)
    {
        return $this->queryParameters[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function addQueryParameter(string $key, $value): self
    {
        $this->queryParameters[$key] = $value;

        return $this;
    }

    /**
     * @param array $queryParameters
     *
     * @return self
     */
    public function setQueryParameters(array $queryParameters): self
    {
        $this->queryParameters = $queryParameters;

        return $this;
    }
}
