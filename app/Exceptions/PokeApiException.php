<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class PokeApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 500,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $status = $response->status();

        $message = match (true) {
            $status === 404 => 'Pokémon not found.',
            $status >= 500 => 'PokéAPI is temporarily unavailable. Please try again later.',
            default => 'Unable to fetch data from PokéAPI.',
        };

        return new self($message, $status);
    }

    public static function connectionFailed(Exception $previous): self
    {
        return new self(
            'Could not connect to PokéAPI. Please check your connection and try again.',
            503,
            $previous,
        );
    }

    public function isNotFound(): bool
    {
        return $this->statusCode === 404;
    }
}
