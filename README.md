# zend-log-slack
Write zend-log messages to a slack channel

Installation
---
TODO

Slack Configuration
---
The log writer works by sending information to a "Incoming WebHooks" integration in Slack:

1. Open the channel you would like to receive your log in.
2. Click on the channel name in the top left corner and from the drop down menu select "Add an app or integration"
3. Click "Build" (top right)
4. Click "Make a custom integration"
5. Choose "Incoming WebHooks"
6. Click the large green "Add Incoming WebHooks integration"

You'll need the Webhook URL to configure the writer. Under "Integration Settings" you can customize some defaults.

Usage
---

### Manual

```php
$writer = new \Cytec\Log\Writer\Slack('<YOUR_SLACK_WEBHOOK_URL>');

//Optional - use this filter only if you want to send critical messages to Slack
$writer->addFilter(new \Zend\Log\Filter\Priority(\Zend\Log\Logger::CRIT));

$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);

$logger->info('Informational message');
$logger->crit('Critical message');

//second "extra" parameter is supported and printed as properties in slack
$logger->crit('Critical message', $_SERVER);
```

### Via Service manager

Somewhere in your configuration (eg. config/autoload/global.php) add

```php
...
'log' => [
    'SlackLog' => [
        'writers' => [
            'default' => [
                'name' => 'Cytec\Log\Writer\Slack',
                'options' => [
                    'webhook_url' => '<YOUR_SLACK_WEBHOOK_URL>',
                    'bot_name' => 'Project Name',   //optional
                    'channel_override' => '#alerts',//optional @person is also supported
                    'filters' => \Zend\Log\Logger::CRIT,      //optional - filter by priority
                ],
            ]
        ],
    ]
],
...
```

And then you can get the logger via the service manager:

```php
$log = $this->getServiceManager()->get('SlackLog');
$log->crit('Critical message');
```

Keep in mind
---
This writer isn't suitable for constant writing due to the fact that log messages are
sent via http interface to the Slack api. This makes it very slow and can add
significant delay in application response times. The intended use is for critical
or very high priority messages that need the immediate attention of a real person.