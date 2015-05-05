<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace USF\IdM;

use USF\IdM\UsfARMapi;
/**
 * Description of UsfParser
 *
 * @author james
 */
class UsfParser {

    //put your code here
    public function parseRoles($importfile) {
        $usfARMapi = new UsfARMapi();
        $handle = fopen($importfile, 'r');
        $currentBlock = array();
        echo "I RAN";
        while (!feof($handle)) {
            $line = fgets($handle);
            if (trim($line) == '') {
                if ($currentBlock) {
                    $role = (array) \json_decode(\implode("\n", $currentBlock));
                    print_r($role);
                    $currentrole = $usfARMapi->getRoleByTypeAndName($role['account_type'],$role['name']);
                    print_r($currentrole);
                    if($currentrole->isSuccess()) {
                        // Update existing role
                        $modifiedrole = $usfARMapi->modifyRoleByTypeAndName($role['account_type'],$role['name'],$role);
                        echo "Updated role\n";
                        print_r($modifiedrole);
                    } else {
                        // Create new role
                        $createdrole = $usfARMapi->createRoleByType($role);
                        echo "Added role\n";
                        print_r($createdrole);
                    }                    
                    $currentBlock = array();
                }
            } else {
                $currentBlock[] = $line;
            }
        }
        fclose($handle);
        //if is anything left
        if ($currentBlock) {
            $role = (array) \json_decode(\implode("\n", $currentBlock),true);
            print_r($role);
            $currentrole = $usfARMapi->getRoleByTypeAndName($role['account_type'],$role['name']);
            print_r($currentrole);
            if($currentrole->isSuccess()) {
                // Update existing role
                $modifiedrole = $usfARMapi->modifyRoleByTypeAndName($role['account_type'],$role['name'],$role);
                echo "Updated role\n";
                print_r($modifiedrole);
            } else {
                // Create new role
                $createdrole = $usfARMapi->createRoleByType($role);
                echo "Added role\n";
                print_r($createdrole);
            }                    
        }
    }
    
    public function parseAccounts($importfile) {
        $usfARMapi = new UsfARMapi();
        $handle = fopen($importfile, 'r');
        $currentBlock = array();
        echo "I RAN";
        while (!feof($handle)) {
            $line = fgets($handle);
            if (trim($line) == '') {
                if ($currentBlock) {
                    $account = (array) \json_decode(\implode("\n", $currentBlock),true);
                    print_r($account);
                    $currentaccount = $usfARMapi->getAccountByTypeAndIdentifier($account['account_type'],$account['account_identifier']);
                    print_r($currentaccount);
                    if($currentaccount->isSuccess()) {
                        // Update existing account
                        $modifiedaccount = $usfARMapi->modifyAccountByTypeAndIdentifier($account['account_type'],$account['account_identifier'],$account);
                        echo "Updated account\n";
                        print_r($modifiedaccount);
                    } else {
                        // Create new account
                        $createdaccount = $usfARMapi->createAccountByType($account['account_type'], $account);                        
                        echo "Added Account\n";
                        print_r($createdaccount);
                    }
                    $currentBlock = array();
                }
            } else {
                $currentBlock[] = $line;
            }
        }
        fclose($handle);
        //if is anything left
        if ($currentBlock) {
            $account = (array) \json_decode(\implode("\n", $currentBlock),true);
            print_r($account);
            $currentaccount = $usfARMapi->getAccountByTypeAndIdentifier($account['account_type'],$account['account_identifier']);
            print_r($currentaccount);
            if($currentaccount->isSuccess()) {
                // Update existing account
                $modifiedaccount = $usfARMapi->modifyAccountByTypeAndIdentifier($account['account_type'],$account['account_identifier'],$account);
                echo "Updated account\n";
                print_r($modifiedaccount);
            } else {
                // Create new account
                $createdaccount = $usfARMapi->createAccountByType($account['account_type'], $account);                        
                echo "Added Account\n";
                print_r($createdaccount);
            }
        }        
    }
    
    public function parseAccountRoles($importfile) {
        $usfARMapi = new UsfARMapi();
        $handle = fopen($importfile, 'r');
        $currentBlock = array();
        echo "I RAN";
        while (!feof($handle)) {
            $line = fgets($handle);
            if (trim($line) == '') {
                if ($currentBlock) {
                    $accountroles = (array) \json_decode(\implode("\n", $currentBlock),true);
                    print_r($accountroles);
                    $currentaccount = $usfARMapi->getAccountByTypeAndIdentifier($accountroles['account_type'],$accountroles['account_identifier']);
                    print_r($currentaccount);
                    if($currentaccount->isSuccess()) {
                        // Update existing account
                        echo "Updating roles in account\n";
                        $modifiedaccount = $usfARMapi->modifyRolesForAccountByTypeAndIdentifier($accountroles['account_type'],$accountroles['account_identifier'],[
                            'account_type' => $accountroles['account_type'],
                            'account_identifier' => $accountroles['account_identifier'],
                            'role_list' => $accountroles['account_roles']
                        ]);       
                        print_r($modifiedaccount);
                    } else {
                        echo "Account not found!\n";
                    }                    
                    $currentBlock = array();
                }
            } else {
                $currentBlock[] = $line;
            }
        }
        fclose($handle);
        //if is anything left
        if ($currentBlock) {
            $accountroles = (array) \json_decode(\implode("\n", $currentBlock),true);
            print_r($accountroles);
            $currentaccount = $usfARMapi->getAccountByTypeAndIdentifier($accountroles['account_type'],$accountroles['account_identifier']);
            print_r($currentaccount);
            if($currentaccount->isSuccess()) {
                // Update existing account
                echo "Updating roles in account\n";
                $modifiedaccount = $usfARMapi->modifyRolesForAccountByTypeAndIdentifier($accountroles['account_type'],$accountroles['account_identifier'],[
                    'account_type' => $accountroles['account_type'],
                    'account_identifier' => $accountroles['account_identifier'],
                    'role_list' => $accountroles['account_roles']
                ]);
                print_r($modifiedaccount);
            } else {
                echo "Account not found!\n";
            }             
        }                
    }

}
