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
 * Description of RestoreArmCommand
 *
 * @author james
 */
class RestoreArmCommand extends Command {
    protected function configure() {
        $this->setName("backup:import")
            ->setDescription("Restores a zipped mongo dump.")
            ->addArgument('fileName',InputArgument::REQUIRED,'What is the accounts filename?')
            ->addOption('track',null,InputOption::VALUE_NONE,'If set, accounts will be tracked against existing to remove missing ones')
            ->addOption('type',null,InputOption::VALUE_REQUIRED,'What is the system type (used in tracking)?',false)
            ->setHelp("Usage: <info>php console.php import:accounts <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $filePath = $input->getArgument('fileName');
        $tempfile = \tempnam(\sys_get_temp_dir(), '');
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        $zip = new \ZipArchive;
        if ($zip->open($filePath) === TRUE) {
            $zip->extractTo($tempfile);
            $zip->close();
        } else {
            echo "Failed!\n";
        }
        $results = [];
        $restore_cmd = "mongorestore --host 127.0.0.1 --port 27017 ".$tempfile."/dump/";
        $results[] = shell_exec($restore_cmd);
        $this->delete_files($tempfile);
        $output->writeln($results);
    }
    /**
     * php delete function that deals with directories recursively
     */
    private function delete_files($target) {
        if (is_dir($target)) {
            $files = \glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned

            foreach ($files as $file) {
                $this->delete_files($file);
            }
            if(\file_exists($target)) { 
                \rmdir($target);
            }
        } elseif (is_file($target)) {
            \unlink($target);
        }
    }
    
}