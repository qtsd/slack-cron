<?php

namespace SlackCron;

use SlackCron\Command\RunCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Yaml\Yaml;

class SlackCron
{
    public static $slackUrl;
    
    public function __construct()
    {
        // config
        try {
            $processor = new Processor();
            $configDatas = Yaml::parse(file_get_contents(__DIR__.'/../conf/config.yml'));
            
            $config = $processor->processConfiguration(
                new SlackCronConfiguration(),
                array($configDatas)
            );
            
            self::$slackUrl = $config['slack_webhook_url'];
            
        } catch (Exception $e) {
            $output = new ConsoleOutput();
            $error = $e->getMessage();
            $output->writeln("<error>$error</error>");
            die;
        }
        
        // app
        $application = new Application();
        $application->add(new RunCommand());
        $application->run();
    }
}
