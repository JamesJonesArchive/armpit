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
    public function parseFileByType($importfile,$importtype,$type) {
        $importtype = \strtolower(\trim($importtype));
        $handle = \fopen($importfile, 'r');
        $currentBlock = [];
        if($importtype == 'accounts') {
            $this->buildAccountComparison($type);
        } elseif($importtype == "roles") {
            $this->buildRoleComparison($type);
        }
        while (!feof($handle)) {
            $line = fgets($handle);
            if (\trim($line) === '') {                
                if (!empty($currentBlock)) {
                    if(in_array($importtype, ['roles','accounts','mapping'])) {
                        switch ($importtype) {
                            case "roles":
                                $resp = $this->importRole(\json_decode(\implode("\n", $currentBlock),true));
                                if($resp->isSuccess()) {
                                    echo $resp->encode()."\n";
                                    $this->removeHrefFromTracking($resp->getData()['role_data']['href']);
                                }
                                echo $resp->encode()."\n";
                                break;
                            case "accounts":
                                $resp = $this->importAccount(\json_decode(\implode("\n", $currentBlock),true));
                                if($resp->isSuccess()) {
                                    $this->removeHrefFromTracking($resp->getData()['href']);
                                }
                                echo $resp->encode()."\n";
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
                        $resp = $this->importRole(\json_decode(\implode("\n", $currentBlock),true));
                        if($resp->isSuccess()) {
                            $this->removeHrefFromTracking($resp->getData()['role_data']['href']);
                        }
                        echo $resp->encode()."\n";
                        break;
                    case "accounts":
                        $resp = $this->importAccount(\json_decode(\implode("\n", $currentBlock),true));
                        if($resp->isSuccess()) {
                            $this->removeHrefFromTracking($resp->getData()['href']);
                        }
                        echo $resp->encode()."\n";
                        break;
                    case "mapping":
                        echo $this->importAccountRoles(\json_decode(\implode("\n", $currentBlock),true))->encode()."\n";
                        break;
                }                        
            } else {
                exit("Import type invalid: $importtype");
            }
        }
        if($importtype == 'accounts') {
            foreach($this->getTrackingHrefList()->getData()['hrefs'] as $href) {
                $resp = $this->removeAccount($href);
                echo $resp->encode()."\n";
                $this->removeHrefFromTracking($href);
            }
        } elseif($importtype == "roles") {
            foreach($this->getTrackingHrefList()->getData()['hrefs'] as $href) {
                $resp = $this->removeRole($href);
                echo $resp->encode()."\n";
                $this->removeHrefFromTracking($href);
            }
        }
        return "IMPORT COMPLETED!";
    }
}
