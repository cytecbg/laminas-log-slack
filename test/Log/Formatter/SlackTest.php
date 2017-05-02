<?php
/**
 * Cytec\Zend slack log
 *
 * @link      https://github.com/cytecbg/zend-log-slack for the source repository
 * @license   https://raw.githubusercontent.com/cytecbg/zend-log-slack/master/LICENSE BSD 3-Clause License
 */

namespace CytecTest\Log\Formatter;

use Zend\Log\Logger;
use Cytec\Log\Formatter\Slack;

class SlackTest extends \PHPUnit\Framework\TestCase
{
    public function testFormatting()
    {
        $event = [
            'timestamp'    => time(),
            'message'      => 'Logger message',
            'priority'     => Logger::CRIT,
            'priorityName' => 'CRIT',
            'extra'        => [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => [
                    'subkey1' => 'subvalue1',
                    'subkey2' => 'subvalue2',
                ]
            ]
        ];
        
        $formatter = new Slack();
        
        $result = $formatter->format($event);
        
        $this->assertTrue(is_array($result));
        
        $this->assertArrayHasKey('attachments', $result);
        $this->assertCount(1, $result['attachments']);
        
        $attachment = $result['attachments'][0];
        
        $this->assertArrayHasKey('fallback', $attachment);
        $this->assertArrayHasKey('text', $attachment);
        $this->assertArrayHasKey('color', $attachment);
        $this->assertArrayHasKey('fields', $attachment);
        $this->assertArrayHasKey('ts', $attachment);
    }
}