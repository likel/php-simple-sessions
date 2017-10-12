# php-simple-sessions
Include this package in any PHP project to store encrypted $_SESSION variables in your MySQL database

## Getting Started

This package is designed to store PHP sessions in a MySQL database so that you can encrypt and access your site's active sessions.

### Prerequisites

* MySQL database
* PHP 5.1 and above for use of PDO

### Installing on your server

1. Create a session table in your MySQL database by running [install/setup.sql](install/setup.sql)

```
CREATE TABLE `likel_sessions` (
    `id` char(128) NOT NULL DEFAULT '',
    `set_time` char(10) NOT NULL,
    `data` text NOT NULL,
    `session_key` char(128) NOT NULL,
    `iv` varchar(16) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

2. Move the files under /src into a directory on your server such as "session"

```
e.g. session/ini, session/models, session/autoload.php, session/example.php
```

3. Move the [ini/credentials.ini](ini/credentials.ini) file to a location not accessible by the public

```
e.g. $ mv ini/credentials /var/www/html/
```

4. Update the database information in the credentials.ini file

5. Ensure that when you create a new session you specify the new credentials.ini location

```
$session = new Likel\Session\Handler(array(
    'credentials_location' => "/path/to/new/credentials.ini"
));
```

6. Run [src/example.php](src/example.php) and check your database for the newly created session

## Running the tests

Run [test/SessionHandlerTest.php](test/SessionHandlerTest.php) with PHPUnit

```
$ phpunit SessionHandlerTest.php
```

## Author

**Liam Kelly** - [likel](https://github.com/likel)

## License

This project is licensed under the MIT - see the [LICENSE.md](LICENSE.md) file for details
