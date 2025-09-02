# laravel-checklist

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
- [ ] https://www.youtube.com/live/DR1o-u2AFPA?si=0wslGiW6VcDU0Yf3&t=1662  <-- see here for defaults also LIVE 35
- [ ] https://www.youtube.com/watch?v=Ku7sGWUKEao Fresh App - Migrations + Models + First Tests Using Factories LIVE 36
- [ ] https://www.youtube.com/watch?v=-r1UDrQJJdQ Laravel + API + Actions + React + TypeScript LIVE 37
- [ ] https://www.youtube.com/watch?v=vDcCmP0q8Kw Chat, Merging PRs on new SaaS, testing LIVE 38
- [ ] https://www.youtube.com/watch?v=uMQFMw4wfu0 Finishing models + coding a queue job and testing it! LIVE 39
- [ ] https://github.com/aamimi/offerly-backend vidi tetsove, modele, controllere, invokable klase, composer.json ...
- [ ] Implement logging for slow queries and lazy loading prevention https://github.com/npapratovic/laravel-checklist/blob/master/AppServiceProvider.php
- [ ] Add AppendQueryCount middleware for query tracking
   - https://github.com/npapratovic/laravel-checklist/blob/master/bootstrap-app.php
   - https://github.com/npapratovic/laravel-checklist/blob/master/AppendQueryCount-Middleware.php 
   - https://github.com/npapratovic/laravel-checklist/blob/master/LazyLoadingFlag-Trait.php
 
