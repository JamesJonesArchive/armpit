<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace USF\IdM;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
/**
 * Description of ImportCommand
 *
 * @author james
 */
class ImportAccountsCommand extends Command {
    public function __construct() {
        $this->usfARMImportFileProcessor = new \USF\IdM\UsfARMImportFileProcessor();
        parent::__construct();
    }
    protected function configure() {
        $this->setName("import:accounts")
            ->setDescription("Imports accounts from line feed separated json account objects.")
            ->addArgument('fileName',InputArgument::REQUIRED,'What is the accounts filename?')
            ->addOption('track',null,InputOption::VALUE_NONE,'If set, accounts will be tracked against existing to remove missing ones')
            ->addOption('type',null,InputOption::VALUE_REQUIRED,'What is the system type (used in tracking)?',false)
            ->setHelp("Usage: <info>php console.php import:accounts <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $filePath = $input->getArgument('fileName');
        if($input->getOption('track')) {
            $output->writeln("Tracking is turned on! Missing accounts will be deleted");
            if($input->getOption('type')) {
                $output->writeln("The specified type for tracking was: ".$input->getOption('type'));
            } else {
                $output->writeln("Tracking is set for all account types!");
            }
            $output->writeln($this->usfARMImportFileProcessor->parseFileByType($filePath,'accounts',($input->getOption('track'))?true:false,($input->getOption('type'))?$input->getOption('type'):null));            
        } else {
            $output->writeln($this->usfARMImportFileProcessor->parseFileByType($filePath,'accounts'));
        }
    }
    
}
