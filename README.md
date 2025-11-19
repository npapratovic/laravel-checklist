# laravel-checklist

Rule of the thumb for placing logic: 

1) Use controllers for HTTP requests and responses
2) Use service classes for complex business logic
3) Use action classes for one-time actions
4) Use form request classes for input validaiton
5) Use policies for auth check
6) Use enums instead of constants
7) Use models to interact with database
8) Use DTO for type-safety 

laravel new project-name, cd project-name, composer require laravel/breeze --dev, php artisan breeze:install react, npm install, npm run dev


Checklist on starting a new Laravel project

- [ ] start new laravel app in 2 commands: `1) laravel new +app_name` `2) composer run dev`
- [ ] Use Laravel Pint for code  formatting tool for PHP / code structure / style https://www.youtube.com/watch?v=s4PonV1wLRQ  https://youtu.be/JUDQuymlsh0?si=e3BK0DKCPHBQQrUO&t=1140  
- [ ] Use Larastan for code analysis https://github.com/larastan/larastan
- [ ] Use Log viewer to simplify vieweing logs
- [ ] Use flareapp to quickly receive notification if error happens
- [ ] Use postmark for transactional emails
- [ ] Use PEST for Laravel tests pestphp/pest
- [ ] use phpstan/phpstan
- [ ] avoid database cascade deletes and default values https://www.youtube.com/watch?v=OZGbySrPhX0  focus on maintaining data integrity at the database level and handling business logic within the application layer. In other words, data doesn’t get deleted unless it’s explicitly defined in the application domain. 
- [ ] use rectorphp if you use legacy code rector/rector https://www.youtube.com/watch?v=15tsiv6AvnE 
- [ ] See Nuno Maduro essentials checklist: https://github.com/nunomaduro/essentials
- [ ] See another checklist: https://saasykit.com/blog/the-first-things-you-should-do-when-you-start-a-new-laravel-project 
- [ ] https://www.youtube.com/live/DR1o-u2AFPA?si=0wslGiW6VcDU0Yf3&t=1662  <-- see here for defaults also LIVE 35
- [ ] https://www.youtube.com/watch?v=Ku7sGWUKEao Fresh App - Migrations + Models + First Tests Using Factories LIVE 36
- [ ] https://www.youtube.com/watch?v=-r1UDrQJJdQ Laravel + API + Actions + React + TypeScript LIVE 37
- [ ] https://www.youtube.com/watch?v=vDcCmP0q8Kw Chat, Merging PRs on new SaaS, testing LIVE 38
- [ ] https://www.youtube.com/watch?v=uMQFMw4wfu0 Finishing models + coding a queue job and testing it! LIVE 39
- [ ] https://github.com/aamimi/offerly-backend vidi testove, modele, controllere, invokable klase, composer.json ...
- [ ] Implement logging for slow queries and lazy loading prevention https://github.com/npapratovic/laravel-checklist/blob/master/AppServiceProvider.php
- [ ] Add AppendQueryCount middleware for query tracking
   - https://github.com/npapratovic/laravel-checklist/blob/master/bootstrap-app.php
   - https://github.com/npapratovic/laravel-checklist/blob/master/AppendQueryCount-Middleware.php 
   - https://github.com/npapratovic/laravel-checklist/blob/master/LazyLoadingFlag-Trait.php
- [ ] Embrace Data Transfer Objects (DTOs) for Robust Data Handling, Benefits: Type-hinting, validation centralization, immutability, and easier testing.
   - https://github.com/npapratovic/laravel-checklist/blob/master/CreateUserData-DTO.php
   - https://github.com/npapratovic/laravel-checklist/blob/master/UserController.php
- [ ] Use Services and Actions to have slimmer controllers:

_Controllers should only handle HTTP requests and responses, delegating complex logic to service classes. This keeps code clean, reusable, and easier to test._

If it does 1 thing → Action, see example:
 
```
class CreateDriverAction
{
    public function execute(array $data): Driver
    {
        // single operation, easy to test
        return Driver::create($data);
    }
}
```

If it coordinates several things → Service, see example: (example usage: https://github.com/npapratovic/laravel-checklist/blob/master/UserController.php)

```
class CreateClientService
{
    public function handle(array $data): Client
    {
        // orchestrate actions + integration logic

        $client = (new CreateClientAction())->execute($data);

        // hitting external API
        $cloudwaysApp = (new CreateCloudwaysAppAction())->execute($client);

        // updating tenant meta
        (new UpdateTenantSysUserAction())->execute($client, $cloudwaysApp);

        return $client;
    }
}
```

Bad example:

```
public function store(Request $request)
{
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $image->storeAs('temp', $image->getClientOriginalName(), 'public');
    }
    
    // Other unrelated logic...
}
```

Good example:

```
public function store(Request $request, ArticleService $articleService)
{
    $articleService->uploadImage($request->file('image'));

    // Other unrelated logic...
}

class ArticleService
{
    public function uploadImage(?UploadedFile $image): void
    {
        if ($image) {
            $image->storeAs('uploads/temp', uniqid() . '_' . $image->getClientOriginalName(), 'public');
        }
    }
}
```

- [ ] Fat Models, Skinny Controllers:

_Shift database logic to Eloquent models to maintain cleaner controllers and reusable code_

Bad controller example: 

```
public function index()
{
    $clients = Client::verified()
        ->with(['orders' => function ($query) {
            $query->where('created_at', '>', now()->subDays(7));
        }])
        ->get();

    return view('index', compact('clients'));
}
```

Good example - separated logic, move the DB queries to Eloquent, and use query scopes in Model: 

```
public function index(Client $client)
{
    return view('index', ['clients' => $client->getVerifiedWithRecentOrders()]);
}

class Client extends Model
{
    public function getVerifiedWithRecentOrders(): Collection
    {
        return $this->verified()
            ->with(['orders' => fn($query) => $query->recent()])
            ->get();
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}

class Order extends Model
{
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>', now()->subDays(7));
    }
}
```

