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
 * Description of DeleteRoleCommand
 *
 * @author james
 */
class DeleteRoleCommand extends Command {
    public function __construct() {
        $this->usfARMapi = new \USF\IdM\UsfARMapi();
        parent::__construct();
    }
    protected function configure() {
        $this->setName("delete:role")
            ->setDescription("Removes a role with the given href")
            ->addArgument('href',InputArgument::REQUIRED,'What is the role href?')
            ->setHelp("Usage: <info>php console.php delete:role <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $href = $input->getArgument('href');
        $resp = $this->usfARMapi->removeRole($href);
        if($resp->isSuccess()) {
            $output->writeln("Successfully deleted {$href}: ".json_encode($resp->getData()['role'], JSON_PRETTY_PRINT));
        } elseif($resp->isError()) {
            $output->writeln("Error deleting {$href}: ".$resp->getData()['description']);
        } elseif($resp->isFail()) {
            $output->writeln("Failure deleting {$href}: ".$resp->getData()['description']);
        }
    }
}