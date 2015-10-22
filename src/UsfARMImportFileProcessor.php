<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace USF\IdM;

/**
 * Description of UsfARMImportFileProcessor
 *
 * @author james
 */
class UsfARMImportFileProcessor extends \USF\IdM\UsfARMapi {
    /**
     * Parses import files into ARM for roles, accounts and mapping
     * 
     * @param type $importfile
     * @param type $importtype
     */
    public function parseFileByType($importfile,$importtype) {
        $handle = fopen($importfile, 'r');
        $currentBlock = [];
        while (!feof($handle)) {
            $line = fgets($handle);
            if (\trim($line) === '') {                
                if (!empty($currentBlock)) {
                    if(in_array(strtolower(trim($importtype)), ['roles','accounts','mapping'])) {
                        switch (strtolower(trim($importtype))) {
                            case "roles":
                                echo $this->importRole(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                                break;
                            case "accounts":
                                echo $this->importAccount(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                                break;
                            case "mapping":
                                echo $this->importAccountRoles(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                                break;
                        }                        
                    } else {
                        exit("Import type invalid: $importtype");
                    }  
                    $currentBlock = [];
                }
            } else {
                $currentBlock[] = $line;
            }
        }
        fclose($handle);
        //if is anything left
        if (!empty($currentBlock)) {
            if(in_array(strtolower(trim($importtype)), ['roles','accounts','mapping'])) {
                switch (strtolower(trim($importtype))) {
                    case "roles":
                        echo $this->importRole(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                        break;
                    case "accounts":
                        echo $this->importAccount(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                        break;
                    case "mapping":
                        echo $this->importAccountRoles(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                        break;
                }                        
            } else {
                exit("Import type invalid: $importtype");
            }
        }
        return "IMPORT COMPLETED!";
    }
}
