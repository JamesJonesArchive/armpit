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
 * Description of ReviewCommand
 *
 * @author james
 */
class ReviewCommand extends Command {
    public function __construct() {
        $this->usfARMImportFileProcessor = new \USF\IdM\UsfARMImportFileProcessor();
        parent::__construct();
    }
    protected function configure() {
        $this->setName("review:accounts")
            ->setDescription("Sets accounts in review from various options.")
            ->addOption('type',null,InputOption::VALUE_REQUIRED,'Provide the system type')
            ->addOption('usfid',null,InputOption::VALUE_REQUIRED,'Provide the usfid')
            ->addOption('identifier',null,InputOption::VALUE_REQUIRED,'Provide the identifier')
            ->addOption('db',null,InputOption::VALUE_REQUIRED,'Provide a specific database name')                                
            ->setHelp("Usage: <info>php console.php review:accounts <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {        
        if ($input->getOption('db')) {
            $this->usfARMImportFileProcessor->setARMdbName($input->getOption('db'));
        }        
        if(!\is_null($input->getOption('type')) && !\is_null($input->getOption('usfid'))) {
            $resp = $this->usfARMImportFileProcessor->getAccountsByTypeAndIdentity($input->getOption('type'),$input->getOption('usfid'));
            if($resp->isSuccess()) {
                foreach ($resp->getData()['accounts'] as $account) {
                    $output->writeln($this->usfARMImportFileProcessor->setReviewByAccount($input->getOption('type'),$account['identifier'])->encode());                    
                }
            } else {
                $output->writeln($resp->encode());
            }
        } elseif (!\is_null($input->getOption('type')) && !\is_null($input->getOption('identifier'))) {
            $output->writeln($this->usfARMImportFileProcessor->setReviewByAccount($input->getOption('type'),$input->getOption('identifier'))->encode());
        } elseif (!\is_null($input->getOption('identifier'))) {
            $output->writeln("ERROR: You must specify a type when specifying an identifier!");
        } elseif (!\is_null($input->getOption('type'))) {
            $type = $input->getOption('type');
            $accounts = $this->usfARMImportFileProcessor->getARMaccounts()->find(['type' => $type ],[ 'identifier' => true, '_id' => false ]);
            foreach ($accounts as $account) {
                $output->writeln($this->usfARMImportFileProcessor->setReviewByAccount($type,$account['identifier'])->encode());                    
            }
        } elseif (!\is_null($input->getOption('usfid'))) {
            $output->writeln($this->usfARMImportFileProcessor->setReviewByIdentity($input->getOption('usfid'))->encode());
        } else {
            $output->writeln("ERROR: You must specify some combination of type or type/usfid or type/identifier or usfid!");
        }
    }
}
