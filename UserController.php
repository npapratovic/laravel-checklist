// App/DTOs/CreateUserData.php
// https://github.com/npapratovic/laravel-checklist/blob/master/CreateUserData-DTO.php


public function store(CreateUserRequest $request, UserService $service)
{
    // Create a validated, immutable data object
    $userData = CreateUserData::fromRequest($request);

    // other way is to use method signature promotion - in store method add DTO as signature
    // This means that data enters controller, goes to form request then goes to DTO which is reponsible for making data immutable and then data goes to Action (service if needed) class

    // Pass the DTO to your service
    $user = $service->createUser($userData);

    return response()->json($user, 201);
}
