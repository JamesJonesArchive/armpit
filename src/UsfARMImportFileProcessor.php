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
    public function parseFileByType($importfile,$importtype,$track = false,$type = null) {
        $handle = fopen($importfile, 'r');
        $currentBlock = [];
        if($track) {
            switch (strtolower(trim($importtype))) {
                case "roles":
                    strtolower(trim($importtype))."\n";
                    $this->buildRoleComparison();
                    break;
                case "accounts":
                    $this->buildAccountComparison();
                    break;
                default:
                    echo "NO MATCH ON ".strtolower(trim($importtype))."\n";
                    break;
            }            
        }
        while (!feof($handle)) {
            $line = fgets($handle);
            if (\trim($line) === '') {                
                if (!empty($currentBlock)) {
                    if(in_array(strtolower(trim($importtype)), ['roles','accounts','mapping'])) {
                        switch (strtolower(trim($importtype))) {
                            case "roles":
                                $this->handleImportRole((array) \json_decode(\implode("\n", $currentBlock),true),$track);
                                break;
                            case "accounts":
                                $this->handleImportAccount((array) \json_decode(\implode("\n", $currentBlock),true),$track);
                                break;
                            case "mapping":
                                print_r($this->importAccountRoles((array) \json_decode(\implode("\n", $currentBlock),true)));
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
                        $this->handleImportAccount((array) \json_decode(\implode("\n", $currentBlock),true),$track);
                        break;
                    case "mapping":
                        print_r($this->importAccountRoles((array) \json_decode(\implode("\n", $currentBlock),true)));
                        break;
                }                        
            } else {
                exit("Import type invalid: $importtype");
            }
        }
        if($track) {
            switch (strtolower(trim($importtype))) {
                case "roles":
                    // run any deletes
                    break;
                case "accounts":
                    // run any deletes
                    foreach ($this->getTrackingHrefList()->getData()['hrefs'] as $href) {
                        $deleteresp = $this->removeAccount($href);
                        if(!$deleteresp->isSuccess()) {
                            $this->logImportErrors('accounts',[ 'href' => $href ],$deleteresp->getData());
                        } else {
                            $this->logImportErrors('accounts',$deleteresp->getData()['account'],$deleteresp->getData());
                            $trackingresp = $this->removeAccountFromTracking($href);
                            if(!$trackingresp->isSuccess()) {
                                $this->logImportErrors('accounts',[ 'href' => $href ],$trackingresp->getData());
                            }
                        }
                    }
                    $this->getARMtracking()->drop();
                    break;
            }
        }
        return "IMPORT COMPLETED!";
    }
    /**
     * 
     * @param array $account
     */
    public function handleImportAccount($account,$track = false) {
        print_r($account);
        $resp = $this->importAccount($account);
        if($resp->isSuccess()) {
            // $resp->getData()['href'];
            print_r($resp->getData());
            if($track) {
                // We don't care if this is successful as it may be new and not exist
                $trackingresp = $this->removeAccountFromTracking($resp->getData()['href']);                                    
            }
        } elseif ($track) {
            $this->logImportErrors('accounts',$account,$resp->getData());
            // Remove url from tracking since it failed but "exists" so shouldn't be deleted
            $href = "/accounts/{$account['account_type']}/{$account['account_identifier']}";
            $trackingresp = $this->removeAccountFromTracking($href);
            if(!$trackingresp->isSuccess()) {
                $this->logImportErrors('accounts',$account,$trackingresp->getData());
            }
        }
    }
    /**
     * 
     * @param array $role
     */
    public function handleImportRole($role,$track = false) {
        print_r($role);
        $resp = $this->importRole($role);
        if($resp->isSuccess()) {
            // $resp->getData()['href'];
            print_r($resp->getData());
            if($track) {
                // We don't care if this is successful as it may be new and not exist
                $trackingresp = $this->removeAccountFromTracking($resp->getData()['href']);                                    
            }
        } elseif ($track) {
            $this->logImportErrors('roles',$role,$resp->getData());
            // Remove url from tracking since it failed but "exists" so shouldn't be deleted
            $href = \USF\IdM\UsfARMapi::formatRoleName("/roles/{$role['account_type']}/{$role['name']}");
            $trackingresp = $this->removeAccountFromTracking($href);
            if(!$trackingresp->isSuccess()) {
                $this->logImportErrors('roles',$role,$trackingresp->getData());
            }
        }
    }
}
