eSputnik API library

Add this to composer.json require section
```json
     'vis/esputnik_client_l5': '1.*'
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

Send identical email to every recipient with prepared template as %TEMPLATE.name% %TEMPLATE.message% etc
```php
    $letterTemplate = 'email_send';
    $recipient = ['email@email.com'];
    $params    = ['name' => 'k.glushchenko', 'message' => 'test_letter'];

    $result  = $client->sendPreparedMessage($letterTemplate, $recipient, $params);
```

Send identical sms to every recipient with prepared template  Params in template are defined as %TEMPLATE.name% %TEMPLATE.message% etc
```php
    $letterTemplate = 'sms_send';
    $recipient = ['+38(000)-000-00-00'];
    $params    = ['name' => 'k.glushchenko', 'message' => 'test_letter'];

    $result  = $client->sendPreparedMessage($letterTemplate, $recipient, $params, false);
```

```php
Send parametrized email for every recipient with prepared template. Params in template are defined as $!data.get('name') $!data.get('message') etc

    $letterTemplate = 'email_smartsend';
    $recipient = ['email1@email.com', 'email2@email.com'];
    $params    = [
        ['name' => 'name_for_email1', 'message' => 'message_for_email1'],
        ['name' => 'name_for_email2', 'message' => 'message_for_email2']
    ];

    $result  = $client->sendExtendedPreparedMessage($letterTemplate, $recipient, $params);
```

```php
Send parametrized sms for every recipient with prepared template. Params in template are defined as $!data.get('name') $!data.get('message') etc

    $letterTemplate = 'sms_smartsend';
    $recipient = ['+38(000)-000-000-00', '+38(000)-000-00-01'];
    $params    = [
        ['name' => 'name_for_00', 'message' => 'message_for_00'],
        ['name' => 'name_for_01', 'message' => 'message_for_01']
    ];

    $result  = $client->sendExtendedPreparedMessage($letterTemplate, $recipient, $params, false);
```

Check message status by message id
```php
    $result = $client->getInstantMessageStatus($id);
```

Send instant email
```php
    $from       = '"organization" <your@account.com>';
    $subject    = 'subject';
    $htmlText   = '<html><body><h1>test!</h1></body></html>';
    $emails     = ['email@email.com'];

    $result  = $this->client->sendEmail($from, $subject, $htmlText,$emails);
```

Check email status by message hash
```php
    $result = $client->getInstantEmailStatus($hash);
```

Send instant email

```php
     $from   = 'your_sms_sender';
     $text   = 'test';
     $phones = ["+38(000)-000-00-00"];

     $result  = $this->client->sendSMS($from, $text, $phones);
```

Check sms status by message hash
```php
    $result = $client->getInstantSmsStatus($hash);
```

