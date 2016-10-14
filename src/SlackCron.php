<?php

namespace SlackCron;

use SlackCron\Command\RunCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class SlackCron
{
    public static $slackUrl;
    
    public function __construct()
    {
        // config
        try {
            // check if file exists
            $fs = new Filesystem();
            $configPath = __DIR__.'/../conf/parameters.yml';
            
            if (!$fs->exists($configPath)) {
                throw new Exception("File $configPath could not be found.");
            }
            
            // process config
            $processor = new Processor();
            $configDatas = Yaml::parse(file_get_contents($configPath));
            
            $config = $processor->processConfiguration(
                new SlackCronConfiguration(),
                array($configDatas)
            );
            
            // store params
            $params = $config['parameters'];
            self::$slackUrl = $params['slack_webhook_url'];
            
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
