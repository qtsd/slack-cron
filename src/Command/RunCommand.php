<?php

namespace SlackCron\Command;

use SlackCron\SlackCron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run command and send error to slack.')
            
            // args
            ->addArgument('exec', InputArgument::REQUIRED, 'The command to execute.')
            ->addArgument('path', InputArgument::OPTIONAL, 'The working directory.', null)
            
            // opts
            ->addOption('timeout',    null, InputOption::VALUE_OPTIONAL, 'Command timeout in milliseconds', 120)
            ->addOption('output-all', null, InputOption::VALUE_NONE,     'Send all output to slack (not just errors)')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // args
        $command = $input->getArgument('exec');
        $cwd = $input->getArgument('path');
        
        // opts
        $timeout = $input->getOption('timeout');
        $outputAll = $input->getOption('output-all');
        
        // process
        $process = new Process($command, $cwd, null, null, $timeout);
        $process->run();
        
        // slack
        if (!$process->isSuccessful() || $outputAll) {
            $this->sendToSlack($command, $process);
        }
    }
    
    protected function sendToSlack($command, Process $process)
    {
        $slackUrl = SlackCron::$slackUrl;
        
        // command
        $user = get_current_user();
        $host = gethostname();
        $cwd = $process->getWorkingDirectory();
        
        $text = "*$user@$host:$cwd$* $command\n>>>";
        
        // output
        if (!$process->isSuccessful()) {
            $exitCode = $process->getExitCodeText();
            $text .= "`$exitCode`\n";
            $output = $process->getErrorOutput();
        } else {
            $output = $process->getOutput();
        }
        
        $lines = explode(PHP_EOL, $output);
        foreach ($lines as $line) {
            if (strlen($line)) {
                $text .= "*>* $line\n";
            }
        }
        
        // web hook
        if (!empty($exitCode) || strlen($output)) {
            $payload = json_encode(array(
                'text' => $text
            ));

            $process = new Process("curl -X POST --data-urlencode 'payload=$payload' $slackUrl");
            $process->run();
        }
    }
}
