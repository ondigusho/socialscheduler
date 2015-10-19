<?php
/* Custom LoadAll class .
 * 
 * Will automatically include all classes under /Lib directory.
 * Used on bootstrap.php
 * 
 * Copyright Ondi Gusho
 */

class LoadAll{
    
    public static function load(){
        $pathDir = substr(APPLIBS, 0, -1);
        $AllFiles = LoadAll::getFiles($pathDir);
        foreach ($AllFiles as $file => $FolderPath){
            $folder = LoadAll::setPath($FolderPath);
            $pieces = explode(".", $file);
            $ClassName = $pieces[0];
            App::uses($ClassName, $folder);
        }
    }
    
    public static function getFiles($dir){
        if($dh = opendir($dir)) {
            $files = Array();
            $inner_files = Array();
            while($file = readdir($dh)) {
                if($file != "." && $file != ".." && $file[0] != '.') {
                    if(is_dir($dir . "/" . $file)) {
                        $inner_files = LoadAll::getFiles($dir . "/" . $file);
                        if(is_array($inner_files)) $files = array_merge($files, $inner_files); 
                    } else {
                        $files[$file] = $dir;
                    }
                }
            }   
            closedir($dh);
            return $files;
        }
    }
    
    public static function setPath($p){
        $pieces = explode("/Lib", $p);
        return "Lib".$pieces[1];
    }
}
?>