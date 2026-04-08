// App/DTOs/CreateUserData.php
// https://github.com/npapratovic/laravel-checklist/blob/master/CreateUserData-DTO.php


public function store(CreateUserRequest $request, UserService $service)
{
    // Create a validated, immutable data object
    $userData = CreateUserData::fromRequest($request);

    // other way is to use type hinting it as a method signature - in store method add DTO as signature
    // This means that data enters controller, goes to form request then goes to action which calls DTO which is reponsible for making data immutable in the end controller should just return response 

    // Pass the DTO to your service
    $user = $service->createUser($userData);

    return response()->json($user, 201);
}
