<?php 
    
/**
 * Recursive copy entire Directory
 *
 * @param string $src
 * @param string $dst
 * @param boolean $overwrite
 */
function recurse_copy($src, $dst, $overwrite = false) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                if(is_file($dst . "/" . $file) && $overwrite) 
                {
                    if($overwrite) {
                        unlink($dst . "/" . $file);
                        copy($src . '/' . $file,$dst . '/' . $file); 
                    }
                }
                else
                    copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}