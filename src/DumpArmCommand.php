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
 * Description of DumpArmCommand
 *
 * @author james
 */
class DumpArmCommand extends Command {
    use ImportUtilities;
    
    protected function configure() {
        $this->setName("backup:export")
            ->setDescription("Exports ARM mongo data to zipped dump")
            ->addOption('out',null,InputOption::VALUE_REQUIRED,'What is the folder used for output?',false)
            ->setHelp("Usage: <info>php console.php backup:export <env></info>");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $tempfile = \tempnam(\sys_get_temp_dir(), '');
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        mkdir($tempfile."/dump");
        $results = [];
        $dump_cmd = "mongodump --db arm --collection ";
        $results[] = shell_exec($dump_cmd."roles --out ".$tempfile."/dump");
        $results[] = shell_exec($dump_cmd."accounts --out ".$tempfile."/dump");
        // Initialize archive object
        $zip = new \ZipArchive;        
        if($input->getOption('out')) {
            $zipPath = $input->getOption('out')."/arm_backup.zip";
        } else {
            $zipPath = \getcwd()."/arm_backup.zip";
        }
        if($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
          exit('cannot create zip');            
        }
        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempfile."/dump", \FilesystemIterator::SKIP_DOTS), 
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $files_to_delete = [];
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                // Get real path for current file            
                $filePath = $file->getRealPath();
                // Add current file to archive
                $zip->addFile($filePath,\substr($name, strlen($tempfile)));
                // add file to delete queue
                $files_to_delete[] = $filePath;
            }
        }
        
        $results[] = "Zip status: ".$zip->getStatusString();
        $zip->close();
        $this->delete_files($tempfile);
        $results[] = "Temp directory removed";
        $results[] = $zipPath." created";
        $output->writeln($results);        
    }

}
