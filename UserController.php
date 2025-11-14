// App/DTOs/CreateUserData.php
// https://github.com/npapratovic/laravel-checklist/blob/master/CreateUserData-DTO.php


public function store(CreateUserRequest $request, UserService $service)
{
    // Create a validated, immutable data object
    $userData = CreateUserData::fromRequest($request);

    // Pass the DTO to your service
    $user = $service->createUser($userData);

    return response()->json($user, 201);
}
