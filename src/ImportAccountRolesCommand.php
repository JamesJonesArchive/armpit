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
 * Description of ImportAccountRolesCommand
 *
 * @author james
 */
class ImportAccountRolesCommand extends Command {
    public function __construct() {
        $this->usfARMImportFileProcessor = new \USF\IdM\UsfARMImportFileProcessor();
        parent::__construct();
    }
    protected function configure() {
        $this->setName("import:mapping")
            ->setDescription("Imports account roles from line feed separated json account objects.")
            ->addArgument('fileName',InputArgument::OPTIONAL,'What is the accounts filename?')
            ->addOption('in',null,InputOption::VALUE_NONE,'Provide STDIN')
            ->setHelp("Usage: <info>php console.php import:accounts <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        if($filePath = $input->getArgument('fileName')) {
            if (\file_exists($filePath)) {
                $output->writeln("Importing account roles: Started at ".date("F j, Y, g:i a"));
                $output->writeln($this->usfARMImportFileProcessor->parseFileByType($filePath,'mapping',null));
                $output->writeln("Importing account roles: Ended at ".date("F j, Y, g:i a"));
            } else {
                $output->writeln("ERROR: File does not exist!");
            }
        } else {
            $output->writeln($this->usfARMImportFileProcessor->importAccountRoles(\json_decode(\file_get_contents("php://stdin"),true))->encode());
        }
    }
}
