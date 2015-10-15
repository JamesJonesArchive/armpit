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
        switch (strtolower(trim($importtype))) {
            case "roles":
                $this->buildRoleComparison();
                break;
            case "accounts":
                $this->buildAccountComparison();
                break;
        }
        while (!feof($handle)) {
            $line = fgets($handle);
            if (\trim($line) === '') {                
                if (!empty($currentBlock)) {
                    if(in_array(strtolower(trim($importtype)), ['roles','accounts','mapping'])) {
                        switch (strtolower(trim($importtype))) {
                            case "roles":
                                $role = (array) \json_decode(\implode("\n", $currentBlock),true);
                                print_r($role);
                                print_r($this->importRole($role));
                                break;
                            case "accounts":
                                $account = (array) \json_decode(\implode("\n", $currentBlock),true);
                                print_r($account);
                                print_r($this->importAccount($account));
                                break;
                            case "mapping":
                                $accountroles = (array) \json_decode(\implode("\n", $currentBlock),true);
                                print_r($accountroles);
                                print_r($this->importAccountRoles($accountroles));
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
                        print_r($this->importRole((array) \json_decode(\implode("\n", $currentBlock))));
                        break;
                    case "accounts":
                        print_r($this->importAccount((array) \json_decode(\implode("\n", $currentBlock))));
                        break;
                    case "mapping":
                        print_r($this->importAccountRoles((array) \json_decode(\implode("\n", $currentBlock))));
                        break;
                }                        
            } else {
                exit("Import type invalid: $importtype");
            }
        }
        switch (strtolower(trim($importtype))) {
            case "roles":
                // run any deletes
                break;
            case "accounts":
                // run any deletes
                break;
        }
    }
}
