<?php
/*
 * Will handle database flow.
 * Insert, Update, Delete, Select
 * 
 * @copyright Ondi Gusho.  
*/

class Database{
    
    /* Insert method.
    *  Will handle mysql insert by a key value pair array. 
    * 
    * @param string conn
    * @param array data  
    * @return void
    */
    public static function Insert($conn,$data){
        //        Object model
        //        $data =array(
        //        'table' =>'sites',
        //        'records'=> array
        //         (
        //            'campaign_id'=>array('value'=>'abcd','type'=>'s'),
        //            'url'=>array('value'=>'http://flowers.com','type'=>'s'),
        //            'keyword'=>array('value'=>'flowers online','type'=>'s'),
        //            'email'=>array('value'=>'a@a.com','type'=>'s'),
        //        ));
        //Extract table.
        $table = $data['table'];
        //Set variables.
        $check = "";
        $sql = "Insert Into $table(";
        $type = "";
        //Dynamic Build
        foreach ($data['records'] as $key => &$value){
            //Get value string
            $sql .= $key.',';
            $values[] = &$value['value'];
            $type .= $value['type']; 
            $check .= "?,";
        }
        //Extract the las char
        $sql = substr($sql, 0, -1);
        $check = substr($check, 0, -1);
        //Build sql Query.
        $sql .=') Values ('.$check.')'; 
        //Sql 
        /* Prepare statement */
        $stmt = $conn->prepare($sql);
        if($stmt === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        }
        /* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
        // Use call_user_func_array 
        call_user_func_array(array($stmt, "bind_param"), array_merge(array($type), $values));
        /* Execute statement */
        $stmt->execute();
        $stmt->close();
    }
    
    /* Update method.
    *  Will handle mysqli Update by a key value pair array. 
    * 
    * @param string conn
    * @param array data  
    * @return void
    */
    public static function Update($conn,$data){
        //        $dataU =array(
        //        'table' =>'sites',
        //        //Condition by id for update
        //        'id'=>array('value'=>'5','type'=>'i'),
        //        'records'=> array
        //         (
        //            'keyword'=>array('value'=>'flowers online','type'=>'s'),
        //            'email'=>array('value'=>'a@a.com','type'=>'s'),
        //        ));
        //Extract table.
        $table = $data['table'];
        //Set variables.
        $check = "";
        $sql = "Update $table SET";
        $type = "";
        //Dynamic Build
        foreach ($data['records'] as $key => &$value){
            //Get value string
            $sql .= ' '.$key.' = ?,';
            $values[] = &$value['value'];
            $type .= $value['type']; 
            $check .= "?,";
        }
        //Add id at the end of type and values
        $type .= $data['id']['type'];
        $values[] = &$data['id']['value'];
        //Extract the last char
        $sql = substr($sql, 0, -1);
        $check = substr($check, 0, -1);
        //Build sql Query.
        $sql .=' where id = ? ';
        //Sql 
        /* Prepare statement */
        $stmt = $conn->prepare($sql);
        if($stmt === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        }
        /* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
        // Use call_user_func_array 
        call_user_func_array(array($stmt, "bind_param"), array_merge(array($type), $values));
        /* Execute statement */
        $stmt->execute();
        $stmt->close();
    }
    
    /* Select method.
    *  Will handle mysqli Select by a key value pair array. 
    * 
    * @param string conn
    * @param array data  
    * @return void
    */
    public static function Select($conn,$data){
        $result = array();
        //        $dataS =array(
        //        'table' =>'sites',
        //        //Condition by id for update
        //        'records'=> array
        //         (
        //            'keyword','email'
        //         ),
        //         'conditions'=> array
        //         (
        //            'keyword'=>array('value'=>'flowers online','type'=>'s'),
        //            'email'=>array('value'=>'a@a.com','type'=>'s'),
        //         )
        //        );
        //Set result
        $result = array();
        //Extract table.
        $table = $data['table'];
        $sql = 'Select';
        //check if conditions
        if (isset($data['records'])){
            //Dynamic Build. Must be a single element array
            foreach ($data['records'] as $record){
                //Get value string
                $sql .= ' '.$record.',';
            }
            //Extract last ,
            $sql = substr($sql, 0, -1);
            //set table
            $sql .= " from $table";
        }
        else{
            $sql = " * from $table";
        }
        //Check conditions
        if (isset($data['conditions'])){
            //Set variables.
            $check = "";
            $sql .= " where ";
            $type = "";
            //Dynamic Build
            foreach ($data['conditions'] as $key => &$value){
                //Get value string
                $sql .= ' '.$key.' = ? AND';
                $values[] = &$value['value'];
                $type .= $value['type']; 
            }
            //Extract the last  5 char, " AND";
            $sql = substr($sql, 0, -4);
            /* Prepare statement */
            $stmt = $conn->prepare($sql);
            if($stmt === false) {
                trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
            }
            /* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
            // Use call_user_func_array 
            call_user_func_array(array($stmt, "bind_param"), array_merge(array($type), $values));
            /* Execute statement */
        }
        else{
            /* Prepare statement with no parameters*/
            $stmt = $conn->prepare($sql);
            if($stmt === false) {
              trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
            }
        }
        //Execute and get results.
        $stmt->execute();
        if(!empty($stmt)&&isset($stmt)){
            $row = array();
            Database::StmtBindAssoc($stmt, $row);
            // loop through all result rows
            while ($stmt->fetch()) {
                $result[] = $row;
            }
        }
        //return 
        return $result;
    }
    
    /* Deleye method.
    *  Will handle mysqli Delete by a key value pair array. 
    * 
    * @param string conn
    * @param array data  
    * @return void
    */
    public function Delete($conn,$data){
        // $data =array(
        // 'table' =>'sites',
        //        'column'=>'campaign_id',
        //        'data'=> array('value'=>'somecampaignid','type'=>'s'),
        //);
        //Extract table.
        $table = $data['table'];
        $column = $data['column'];
        //Set variables.
        $sql = "Delete from $table where $column = ?";
        //Get value string
        $values = &$data['data']['value'];
        $type = $data['data']['type']; 
        //Sql 
        /* Prepare statement */
        $stmt = $conn->prepare($sql);
        if($stmt === false) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
        }
        /* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
        // Use call_user_func_array 
        //call_user_func_array(array($stmt, "bind_param"), array_merge(array($type), $values));
        $stmt->bind_param($type,$values);
        /* Execute statement */
        $stmt->execute();
        $stmt->close();
    }
    
    /*StmtBindAssoc method.
    * 
    * Used on building Select method results.
    * 
    * @param string stmt
    * @param array out  
    * @return void
    */
    public static function StmtBindAssoc (&$stmt, &$out) {
        $data = mysqli_stmt_result_metadata($stmt);
        $fields = array();
        $out = array();

        $fields[0] = $stmt;
        $count = 1;

        while($field = mysqli_fetch_field($data)) {
            $fields[$count] = &$out[$field->name];
            $count++;
        }   
        call_user_func_array('mysqli_stmt_bind_result', $fields);
    }
}
?>