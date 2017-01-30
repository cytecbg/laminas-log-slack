<?php
/**
 * Zend log writer slack
 *
 * @link      https://github.com/cytecbg/zend-log-writer-slack for the source repository
 * @license   https://raw.githubusercontent.com/cytecbg/zend-log-writer-slack/master/LICENSE BSD 3-Clause License
 */

namespace Cytec\LogWriter;

use Zend\Http\Client;
use Zend\Log\Writer\AbstractWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;

class Slack extends AbstractWriter
{
    /**
     * Webhook url obtained by adding an "Incoming WebHooks" integration to slack
     * 
     * @var string
     */
    protected $webhook_url = null;
    
    /**
     * Webhooks have a default channel configured in the Integration Settings
     * section, but it can be overridden.
     * 
     * @var string
     */
    protected $channel_override = null;
    
    /**
     * The display name of the log bot.
     * 
     * @var string
     */
    protected $bot_name = 'zend-log';
    
    /**
     * Zend\Http\Client used for making requests to the webhook_url
     * 
     * @var Client 
     */
    protected $client = null;
    
    /**
     * Constructor
     * 
     * @param string|array|Traversable $webhook_url Webhook url
     * @param string $bot_name Bot name
     * @param string $channel_override Channel to post messages to
     * @param \Zend\Http\Client\Adapter\AdapterInterface|string $httpAdapter
     */
    public function __construct($webhook_url, $bot_name = 'zend-log', $channel_override = null, $httpAdapter = 'Zend\Http\Client\Adapter\Socket')
    {
        if ($webhook_url instanceof Traversable) {
            $webhook_url = iterator_to_array($webhook_url);
        }

        if (is_array($webhook_url)) {
            parent::__construct($webhook_url);
            
            $httpAdapter = isset($webhook_url['httpAdapter']) ? $webhook_url['httpAdapter'] : 'Zend\Http\Client\Adapter\Socket';
            $bot_name = isset($webhook_url['bot_name']) ? $webhook_url['bot_name'] : null;
            $channel_override = isset($webhook_url['channel_override']) ? $webhook_url['channel_override'] : null;
            $webhook_url = isset($webhook_url['webhook_url']) ? $webhook_url['webhook_url'] : null;
        }
        
        $this->webhook_url = $webhook_url;
        $this->channel_override = $channel_override;
        $this->bot_name = $bot_name;
        
        if(!$this->webhook_url) {
            throw new \Exception('No webhook url provided');
        }
        
        if ($this->formatter === null) {
            $this->formatter = new SimpleFormatter();
        }
        
        $this->client = new Client($this->webhook_url);
        $this->client->setMethod('POST');
        $this->client->setAdapter($httpAdapter);
    }
    
    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        $line = $this->formatter->format($event);
        
        $payload = ['text' => $line, 'username' => $this->bot_name];
        
        if($this->channel_override)
        {
            $payload['channel'] = $this->channel_override;
        }
        
        $this->client->setRawBody(json_encode($payload));
        $this->client->send();
    }

}