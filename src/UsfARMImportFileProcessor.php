<?php

namespace USF\IdM;

/**
 * Description of UsfARMImportFileProcessor
 *
 * @author james
 */
class UsfARMImportFileProcessor extends \USF\IdM\UsfARMapi
{
    /**
     * Parses import files into ARM for roles, accounts and mapping
     *
     * @param type $importfile
     * @param type $importtype
     */
    public function parseFileByType($importfile, $importtype, $type = null) {
        $importtype = \strtolower(\trim($importtype));
        $handle = \fopen($importfile, 'r');
        $currentBlock = [];
        \PHP_Timer::start();
        if ($importtype == 'accounts') {
            \PHP_Timer::start();
            $this->buildAccountComparison($type);
            $time = \PHP_Timer::stop();
            echo "Account comparison complete. ". \PHP_Timer::secondsToTimeString($time)."\n";
        } elseif ($importtype == "roles") {
            \PHP_Timer::start();
            $this->buildRoleComparison($type);
            $time = \PHP_Timer::stop();
            echo "Role comparison complete. ". \PHP_Timer::secondsToTimeString($time)."\n";
        } elseif ($importtype == "mapping") {
            \PHP_Timer::start();
            $this->buildMappingComparison($type);
            $time = \PHP_Timer::stop();
            echo "Mapping comparison complete. ". \PHP_Timer::secondsToTimeString($time)."\n";
        }

        $roles = $accounts = $mapping = $accounts_removed = $roles_removed = 0;

        while (!feof($handle)) {
            $line = fgets($handle);
            if (\trim($line) === '') {
                if (!empty($currentBlock)) {
                    if (in_array($importtype, ['roles','accounts','mapping'])) {
                        // Ensures the Indexes exist on the collections
                        $this->ensureIndexes();
                        switch ($importtype) {
                            case "roles":
                                $resp = $this->importRole(\json_decode(\implode("\n", $currentBlock), true));
                                if (! $resp->isSuccess()) {
                                    echo $resp->encode()."\n";
                                } else {
                                    $roles++;
                                }
                                break;
                            case "accounts":
                                $resp = $this->importAccount(\json_decode(\implode("\n", $currentBlock), true));
                                if (! $resp->isSuccess()) {
                                    echo $resp->encode()."\n";
                                } else {
                                    $accounts++;
                                    if (($accounts % 1000) === 0) echo "$accounts \n";
                                }
                                break;
                            case "mapping":
                                $resp = $this->importAccountRoles(\json_decode(\implode("\n", $currentBlock), true));
                                if (! $resp->isSuccess()) {
                                    echo $resp->encode()."\n";
                                    print_r($currentBlock);
                                    echo "========\n";
                                } else {
                                    $mapping++;
                                    if (($mapping % 1000) === 0) echo "$mapping \n";
                                }
                                break;
                        }
                    } else {
                        throw new Exception("Import type invalid: $importtype", 1);
                    }
                    unset($currentBlock);
                }
            } else {
                $currentBlock[] = $line;
            }
        }
        fclose($handle);
        //if is anything left
        if (!empty($currentBlock)) {
            if (in_array(strtolower(trim($importtype)), ['roles','accounts','mapping'])) {
                switch ($importtype) {
                    case "roles":
                        $resp = $this->importRole(\json_decode(\implode("\n", $currentBlock), true));
                        if (! $resp->isSuccess()) {
                            echo $resp->encode()."\n";
                        } else {
                            $roles++;
                        }
                        break;
                    case "accounts":
                        $resp = $this->importAccount(\json_decode(\implode("\n", $currentBlock), true));
                        if (! $resp->isSuccess()) {
                            echo $resp->encode()."\n";
                        } else {
                            $accounts++;
                            if (($accounts % 1000) === 0) echo "$accounts \n";
                        }
                        break;
                    case "mapping":
                        $resp = $this->importAccountRoles(\json_decode(\implode("\n", $currentBlock), true));
                        if (! $resp->isSuccess()) {
                            echo $resp->encode()."\n";
                            print_r($currentBlock);
                            echo "========\n";
                        } else {
                            $mapping++;
                            if (($mapping % 1000) === 0) echo "$mapping \n";
                        }
                        break;
                }
            } else {
                throw new Exception("Import type invalid: $importtype", 1);
            }
        }

        if ($roles > 0) echo "Imported $roles roles.\n";
        if ($accounts > 0) echo "Imported $accounts accounts.\n";
        if ($mapping > 0) echo "Imported $mapping account<=>role mappings.\n";
        if($importtype == 'accounts') {
            foreach ($this->current_accounts as $atype => $hrefs) {
                foreach ($hrefs as $href) {
                    $resp = $this->removeAccount($href);
                    if (! $resp->isSuccess()) {
                        echo $resp->encode()."\n";
                    } else {
                        $accounts_removed++;
                        if (($accounts_removed % 1000) === 0) echo "$accounts_removed \n";
                    }                    
                }
            }
            if ($accounts_removed > 0) echo "Removed $accounts_removed accounts missing from feed.\n";
        } elseif($importtype == "roles") {
            foreach ($this->current_roles as $rtype => $hrefs) {
                foreach ($hrefs as $href) {
                    $resp = $this->removeRole($href);
                    if (! $resp->isSuccess()) {
                        echo $resp->encode()."\n";
                    } else {
                        $roles_removed++;
                        if (($roles_removed % 1000) === 0) echo "$roles_removed \n";
                    }                    
                }
            }
            if ($roles_removed > 0) echo "Removed $roles_removed roles missing from feed.\n";
        }        
        $time = \PHP_Timer::stop();
        echo \PHP_Timer::resourceUsage()."\n";
    }
}
