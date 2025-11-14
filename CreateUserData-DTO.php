<?php

// App/DTOs/CreateUserData.php
class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $role = 'user'
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
