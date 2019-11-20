<?php
namespace DynamicContentForElementor;
/**
 * Description of DCE_Trait_Plugin
 *
 */
trait DCE_Trait_FileSystem {
    
    

    public static function dir_to_array($dir, $hidden = false, $files = true) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::dir_to_array($dir . DIRECTORY_SEPARATOR . $value, $hidden, $files);
                } else {
                    if ($files) {
                        if (substr($value, 0, 1) != '.') { // hidden file
                            $result[] = $value;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public static function is_empty_dir($dirname) {
        if (!is_dir($dirname))
            return false;
        foreach (scandir($dirname) as $file) {
            if (!in_array($file, array('.', '..', '.svn', '.git')))
                return false;
        }
        return true;
    }

    /**
     * Function for including files
     *
     * @since 0.5.0
     */
    public static function file_include($file) {
        $path = DCE_PATH . $file;
        //echo $path;
        if (file_exists($path)) {
            include_once( $path );
        }
    }
    
}
