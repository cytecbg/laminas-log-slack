<?php
/**
 * Cytec\Zend slack log
 *
 * @link      https://github.com/cytecbg/zend-log-slack for the source repository
 * @license   https://raw.githubusercontent.com/cytecbg/zend-log-slack/master/LICENSE BSD 3-Clause License
 */

namespace CytecTest\Log\Writer;

use Cytec\Log\Writer\Slack;
use Zend\Log\Logger;
use Zend\Http\Response;
use Zend\Http\Request;

class SlackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->logger = new Logger();
    }
    
    public function testLogging()
    {
        /* @var $adapter \PHPUnit_Framework_MockObject_MockObject|\Zend\Http\Client\Adapter\AdapterInterface */
        $adapter = $this->createMock('Zend\Http\Client\Adapter\AdapterInterface');
        
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
    
    /**
     * @expectedException \Exception
     */
    public function testNoOptions()
    {
        new Slack([]);
    }
}