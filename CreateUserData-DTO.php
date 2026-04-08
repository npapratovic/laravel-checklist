<?php

// App/DTOs/CreateUserData.php
class CreateUserData
{
    public function __construct(
        public readonly string $name, // Using readonly in a Data Transfer Object (DTO) is a best practice for maintaining data integrity as information moves through your application's layers (Controller → Action → Service)
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $role = 'user'
    ) {}

    // Create a DTO from an HTTP Request
    public static function fromRequest(CreateUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            role: $request->validated('role', 'user')
        );
    }

    // Create a DTO from a Command
    public static function fromCommand(string $name, string $email, string $password): self
    {
        return new self(
            name: $name,
            email: $email,
            password: $password
        );
    }
}
