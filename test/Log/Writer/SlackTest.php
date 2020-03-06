<?php
/**
 * Cytec\Zend slack log
 *
 * @link      https://github.com/cytecbg/zend-log-slack for the source repository
 * @license   https://raw.githubusercontent.com/cytecbg/zend-log-slack/master/LICENSE BSD 3-Clause License
 */

namespace CytecTest\Log\Writer;

use Cytec\Log\Writer\Slack;
use Laminas\Log\Logger;
use Laminas\Http\Response;
use Laminas\Http\Request;

class SlackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->logger = new Logger();
    }
    
    public function testLogging()
    {
        /* @var $adapter \PHPUnit_Framework_MockObject_MockObject|\Laminas\Http\Client\Adapter\AdapterInterface */
        $adapter = $this->createMock('Laminas\Http\Client\Adapter\AdapterInterface');
        
        $options = [
            'webhook_url' => 'https://hooks.slack.com/services/non/existent/service/hook',
            'httpAdapter' => $adapter
        ];
        
        $response = new Response();
        
        $adapter->expects($this->once())
                ->method('write')
                ->with(Request::METHOD_POST, $options['webhook_url']);
        
        $adapter->expects($this->any())
                ->method('read')
                ->will($this->returnValue($response->toString()));

        $writer = new Slack($options);
        $this->logger->addWriter($writer);
        
        $this->logger->log(Logger::INFO, 'Slack test', [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => [
                'subkey1' => 'subvalue1',
                'subkey2' => 'subvalue2',
            ]
        ]);
    }
    
    public function testNoOptions()
    {
        $this->expectException(\Exception::class);
        
        new Slack([]);
    }
}