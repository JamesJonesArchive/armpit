<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace USF\IdM;

/**
 * Description of ImportUtilities
 *
 * @author james
 */
trait ImportUtilities {
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
