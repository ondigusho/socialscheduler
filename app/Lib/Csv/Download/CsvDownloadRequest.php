<?php 
// Set time limit for execution to 0. 
set_time_limit(0);

/**
 * Handles all download requests for CSV files.
 *
 * @copyright  Ondi Gusho
 */
class CsvDownloadRequest{
    
    /**
     * CSV Downloader . 
     * Get line by line and print out as CSV on client side .
     *  
     * 
     * @param  string  Query object.
    */
    public static function build($csv,$columns,$filename){
        //Set empty string.
        $string ='';
        //headers
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename='.basename($filename.".csv"));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        //file
        $fh = @fopen( 'php://output', 'w' );
        //column names
        fputcsv($fh, $columns);
        //data
        $row = array();
        foreach ($csv as $object){
            foreach ($object as $key => $value){
                $row[] = $value;
            }
            //insert each row
            fputcsv($fh,$row);
            //reset row
            $row = array();
        }
        // Clean buffer
        ob_flush();
        flush();
        exit;
    }
} // End Query_Builder