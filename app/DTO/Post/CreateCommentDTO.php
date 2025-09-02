<?php

namespace App\DTO\Post;

readonly class CreateCommentDTO
{
    public function __construct(
        public string $body,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            body: (string) $request->input('body'),
        );
    }
}
