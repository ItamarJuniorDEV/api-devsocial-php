<?php

namespace App\DTO\Post;

use Illuminate\Http\UploadedFile;

readonly class CreatePostDTO
{
    public function __construct(
        public string $type,
        public ?string $body,
        public ?UploadedFile $photo,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            type: (string) $request->input('type'),
            body: $request->input('body'),
            photo: $request->file('photo'),
        );
    }
}
