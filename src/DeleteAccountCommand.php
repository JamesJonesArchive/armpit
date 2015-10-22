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
 * Description of DeleteAccountCommand
 *
 * @author james
 */
class DeleteAccountCommand extends Command {
    public function __construct() {
        $this->usfARMImportFileProcessor = new \USF\IdM\UsfARMImportFileProcessor();
        parent::__construct();
    }
    protected function configure() {
        $this->setName("delete:account")
            ->setDescription("Removes an account with the given href")
            ->addArgument('href',InputArgument::REQUIRED,'What is the account href?')
            ->setHelp("Usage: <info>php console.php delete:account <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $href = $input->getArgument('href');
        $output->writeln($this->usfARMImportFileProcessor->removeAccount($href)->encode());
    }
}
