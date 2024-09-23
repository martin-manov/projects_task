## Simple Projects / Tasks

```composer install```

```cp .example.env .env```

Replace placeholders in .env with correct keys and DB url

Generate JWT keys:

```php bin/console lexik:jwt:generate-keypair```

Create DB:

```php bin/console doctrine:schema:create```

Execute migrations:

```php bin/console doctrine:migrations:migrate```

Populate DB using fixture data (previous data will be erased):

```php bin/console doctrine:fixtures:load```
