<?php

namespace IPS\discord\Api;

trait ResponseTransformer
{
    protected static $goodStatusCodes = [200, 201, 204, 304];

    public function transformResponse(\IPS\Http\Response $response)
    {
        $statusCode = (int) $response->httpResponseCode;

        if ($this->isBadStatusCode($statusCode)) {
            return $this->createErrorResponse($response);
        }

        return $this->createSuccessResponse($response);
    }

    public function createSuccessResponse(\IPS\Http\Response $response)
    {
        $json = $response->decodeJson();

        // TODO: json to model transformer

        return $json;
    }

    public function createErrorResponse(\IPS\Http\Response $response)
    {
        $json = $response->decodeJson();

        // TODO: json to model transformer

        return $json;
    }

    public function isBadStatusCode(int $statusCode): bool
    {
        return !in_array($statusCode, static::$goodStatusCodes);
    }
}
