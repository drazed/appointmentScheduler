https://github.com/ezimuel/zend-expressive-api
with
https://github.com/dragonmantank/zendexpressive-session-middleware/tree/master/src/Http/Session

# Zend Expressive API

This is a skeleton application for building web APIs using [zend-expressive](https://github.com/zendframework/zend-expressive).

We used the zend-expressive programmatic approach configuration. You can check
out the [public/index.php](public/index.php) file to see the usage of middleware
piping.

## Setup

You need to use [composer](https://getcomposer.org/) to install the project.
You can run the following command:

```bash
$ composer install
```

## Status

The status of this project is **work-in-progress**, please consider this if you
want to use it.

## Usage

This skeleton application includes some APIs example

- REST API, using a service manager.

### REST

We provide a REST API using a User resource with a simple SQLite db with the
following schema:

```sql
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(80),
  email VARCHAR(255) NOT NULL,
  password VARCHAR(60) NOT NULL
);
```

We published the following URLs:

- GET `/api/user[/{id:\d+}]`
- POST `/api/user`
- PATCH `/api/user/{id:\d+}`
- DELETE `/api/user/{id:\d+}`

In order to execute the REST API you need to use the `public/index.php` file.
Using the internal web server of PHP you can use the following command:

```bash
$ php -S 0.0.0.0:8080 -t public public/rpc.php
```

Here we reported some example of usage using HTTPie client:

#### GET

Request:

```bash
$ http GET http://localhost:8080/api/user
```

Response:

```
HTTP/1.1 200 OK
Connection: close
Content-Length: 254
Content-Type: application/json
Host: localhost:8080

{
    " users": [
        {
            "email": "foo@host.com",
            "id": "1",
            "name": "Foo",
            "password": "$2y$10$34w0udB8WTSKEzkaRzgmHev8Lx5EcK07Fs.SMZXnNc8w3yNPUXjNW"
        },
        {
            "email": "bar@host.com",
            "id": "2",
            "name": "Bar",
            "password": "$2y$10$9wTSa.QrGxP9Q3zjLC74cebwA1ro5a7JOzvFHnSCApPDoutRfvGmW"
        }
    ]
}
```

#### POST

Request:

```bash
$ http POST http://localhost:8080/api/user name=Baz email=baz@host.com password=test --json
```

Response:

```
HTTP/1.1 201 Created
Connection: close
Content-Length: 0
Content-type: text/html; charset=UTF-8
Host: localhost:8080
Location: /api/user/3
```

The user Baz has been created in the following location `/api/user/3`.
Note that the password are stored using the [bcrypt](https://en.wikipedia.org/wiki/Bcrypt)
algorithm.

#### PATCH

Request:

```bash
$ http PATCH http://localhost:8080/api/user/3 name=Enrico --json
```

Response:

```
HTTP/1.1 200 OK
Connection: close
Content-Length: 129
Content-Type: application/json
Host: localhost:8080
X-Powered-By: PHP/7.0.4

{
    "user": {
        "email": "baz@host.com",
        "id": "3",
        "name": "Enrico",
        "password": "$2y$10$tRG3Wan7jTJOOm2w8Jy3Au4ViJwol1q6ZToykM7qhDRPv174cIf6u"
    }
}
```

#### DELETE

Request:

```bash
$ http DELETE http://localhost:8080/api/user/3
```

Response:

```
HTTP/1.1 204 No Content
Connection: close
Content-Length: 0
Content-type: text/html; charset=UTF-8
Host: localhost:8080
```
