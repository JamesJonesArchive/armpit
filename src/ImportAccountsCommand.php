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
            ->addArgument('fileName',InputArgument::OPTIONAL,'What is the accounts filename?')
            ->addOption('type',null,InputOption::VALUE_REQUIRED,'Provide the system type')
            ->addOption('db',null,InputOption::VALUE_REQUIRED,'Provide a specific database name')                
            ->addOption('in',null,InputOption::VALUE_NONE,'Provide STDIN')
            ->setHelp("Usage: <info>php console.php import:accounts <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        if ($input->getOption('db')) {
            $this->usfARMImportFileProcessor->setARMdbName($input->getOption('db'));
        }
        if($filePath = $input->getArgument('fileName')) {
            if (\file_exists($filePath)) {
                $output->writeln("Importing accounts from ".$input->getOption('type').": Started at ".date("F j, Y, g:i a"));
                $output->writeln($this->usfARMImportFileProcessor->parseFileByType($filePath,'accounts',$input->getOption('type')));
                $output->writeln("Importing accounts from ".$input->getOption('type').": Ended at ".date("F j, Y, g:i a"));
            } else {
                $output->writeln("ERROR: File does not exist!");
            }
        } else {
            $output->writeln($this->usfARMImportFileProcessor->importAccount(\json_decode(\file_get_contents("php://stdin"),true))->encode());
        }
    }
    
}
