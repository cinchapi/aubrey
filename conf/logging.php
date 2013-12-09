<?php
// See https://logging.apache.org/log4php/docs/configuration.html
return array(
    'rootLogger' => array(
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutSimple'
            ),
            'params' => array(
            	'file' => '/Users/jnelson/log/my.log',
            	'append' => true
            )
        )
    )
);
?>
