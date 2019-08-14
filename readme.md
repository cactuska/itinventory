# Project goal
The goal was to keep track of compny owned assets tools and devices. With this system we can manage the inventory, keep track of the tools, notify the necessarty collegues of the department change.

You can use this tool for not IT related assets as well, with some changes in the properties
The PDFs and mails are in hungarian, you need to do the translation, or create new text

# Installation

- Clone the repo  
`git clone https://github.com/cactuska/itinventory.git itinventory`  
`cd itinventory`
- Install Composer Dependencies  
`composer install`

- Install NPM Dependencies  
`npm install`

- Copy .env_example file  
`cp .env.example .env`

- Create encryption key  
`php artisan key:generate`

- Create a database

- Adjust the .env file

- Migrate the tables  
(Now it has some extra table renameing, and insertion, just delete lines starting with DB::)  
`php artisan migrate`


# Written in
## Laravel PHP Framework - version 5.2.45

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

### Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

### Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

### Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
