<?php
/**
 * Cytec\Zend slack log
 *
 * @link      https://github.com/cytecbg/zend-log-slack for the source repository
 * @license   https://raw.githubusercontent.com/cytecbg/zend-log-slack/master/LICENSE BSD 3-Clause License
 */

namespace Cytec\Log\Formatter;

use Zend\Log\Logger;
use Zend\Log\Formatter\Base;

class Slack extends Base
{
    /**
     * Maps Zend\Log\Logger priorities to colors
     * 
     * @var array
     */
    protected $priority_color_map = [
        Logger::EMERG  => 'danger',
        Logger::ALERT  => 'danger',
        Logger::CRIT   => 'danger',
        Logger::ERR    => 'danger',
        Logger::WARN   => 'warning',
        Logger::NOTICE => 'warning',
        Logger::INFO   => '#439FE0',
        Logger::DEBUG  => '#bababa'
    ];
    
    /**
     * Formats data into a slack message payload compatible structure
     * 
     * For more info see:
     *  - https://api.slack.com/docs/message-attachments
     *  - https://api.slack.com/docs/message-formatting
     *
     * @param array $event event data
     * @return array Slack message payload compatible structure
     */
    public function format($event)
    {
        //the base formatter consolidates the 'extra' array to a single level array
        $base_output = parent::format($event);
        
        $color = isset($this->priority_color_map[$event['priority']]) ? $this->priority_color_map[$event['priority']] : '#bababa';
        
        $timestamp = strtotime($base_output['timestamp']);
        
        $text = '<!date^'.$timestamp.'^{date_pretty} {time_secs}|'.date('Y-m-d H:i:s', $timestamp).'> Message: *'.$base_output['message'].'*';
        
        $attachment = [
            'fallback' => $text,
            'text' => $text,
            'color' => $color,
            'mrkdwn_in' => ['text'],
            'fields' => [
                [
                    'title' => 'Priority',
                    'value' => $event['priorityName']
                ]
            ],
            'ts' => $event['timestamp']
        ];
        
        foreach($base_output['extra'] as $key=>$value)
        {
            $attachment['fields'][] = [
                'title' => $key,
                'value' => $value
            ];
        }
        
        return [
            'attachments' => [$attachment]
        ];
    }
}

