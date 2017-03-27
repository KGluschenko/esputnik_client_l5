eSputnik API library

Add this to composer.json require section
```json
     "vis/esputnik_client_l5": "1.*"
```

Execute
```json
    composer update
```

Add eSputnikClientServiceProvider to ServiceProviders in config/app.php
```php
   Vis\eSputnikClient\eSputnikClientServiceProvider::class,
```

Publish config and define your login\password in it
```php
    php artisan vendor:publish --tag=esputnik-client-config --force
```

Usage
```php
    use Vis\eSputnikClient\eSputnikClient;
```

Methods example
```php
    $client = new eSputnikClient();

    $result  = $client->getVersion();
```

```php
    $letterTemplate = 'test';
    $recipient = ['k.gluschenko@vis-design.com'];
    $params = ["name" => "k.gluschenko", "message" => "test_letter"];

    $result  = $client->sendPreparedMessage($letterTemplate, $recipient, $params);
```

```php
    $id = 'some-id';
    $result = $client->getInstantEmailStatus($id);
```
