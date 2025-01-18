# CO698WBL CW1 Final Report
## Hal Vipan - 22200387

This is a Symfony PHP application

To run the project, you will need to have PHP and Composer installed on your machine. You can download PHP from [here](https://www.php.net/downloads) and Composer from [here](https://getcomposer.org/download/).

You will also need Symfony CLI installed on your machine. You can download Symfony CLI from [here](https://symfony.com/download).

You will also need to have a MySQL server running on your machine. You can download MySQL from [here](https://dev.mysql.com/downloads/). You will need to update the .env and .env.test file with your database credentials.

You will then need to create the database by running the following command in the project root directory:

```
symfony console doctrine:database:create   
```

You will then need to run the following command to create the database schema:
```
symfony console doctrine:migrations:migrate
```

You can then run the project by running the following command in the project root directory:
```
symfony server:start
```

Then install the project dependencies by running the following command in the project root directory:
```
composer install
```

The application will be running on http://127.0.0.1:8000/

And can run tests by running the following command in the project root directory:
```
symfony php bin/phpunit
```