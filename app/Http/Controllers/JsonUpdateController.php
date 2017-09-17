<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Schema;

class JsonUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        // 
        return view('jsonupdate');
    }

    private $table_inf = array();
    private $end_table = "PRIMARY KEY (`primary_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
    private $pre_insert = "INSERT INTO ";
    private $middle_insert = ") VALUES (";
    private $prifix = "tbl_";

    public function import1()
    {
        $json = "";
        $json = file_get_contents('https://cdn.sportfeeds.io/api/team/?language=en&country=us&team_id=92');
           
        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        //$total_create_table = $this->create_tbl_createquery();
        $total_insert_table = $this->create_tbl_insert();

        //echo $total_insert_table;exit;
        //DB::connection()->getPdo()->exec( $total_create_table );
        //echo"<br/ ><h1>Import success!</h1>";
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    public function import21()
    {
        $json = "";
        
        $json = file_get_contents('https://cdn.sportfeeds.io/api/matches/?language=en&country=us&start_date=2017-01-09T22:00:00&end_date=2017-01-10T22:00:00&add_playing=0');
   
        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        $total_insert_table = $this->create_tbl_insert();
       
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    public function import22()
    {
        $json = "";
        
        $json = file_get_contents('https://cdn.sportfeeds.io/api/matches/?language=en&country=us&start_date=2016-02-16T22:00:00&end_date=2016-02-17T22:00:00&add_playing=0');
   
        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        $total_insert_table = $this->create_tbl_insert();
        
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    public function import3()
    {
        $json = "";
        
        $json = file_get_contents('https://cdn.sportfeeds.io/api/match/?language=en&country=us&match_id=2328333');

        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        $total_insert_table = $this->create_tbl_insert();
        
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    public function import4()
    {
        $json = "";

        $json = file_get_contents('https://cdn.sportfeeds.io/api/team/?language=en&country=us&team_id=92');
 
        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        $total_insert_table = $this->create_tbl_insert();
        
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    public function import5()
    {
        $json = "";

        $json = file_get_contents('https://cdn.sportfeeds.io/api/competition?language=en&country=us&competition_id=87');
   
        $json_data = json_decode($json, true);
        
        $this->test($json_data, "soccerway", "", 0);
        
        $total_insert_table = $this->create_tbl_insert();
        
        DB::connection()->getPdo()->exec( $total_insert_table );

    }

    function test($data, $table_name, $prt_name, $prt_pkid)
    {
        //$table_name = $this->prifix.$table_name;
        if(!isset($this->table_inf[$table_name]))
        {
            $this->table_inf[$table_name] =  array();
            $this->table_inf[$table_name]['create_inf'] = array();
            $this->table_inf[$table_name]['data_inf'] = array();
        }
        $pk_id = count($this->table_inf[$table_name]['data_inf'])+1;
        $key_back = 1;
        if($pk_id != 1)
        {
            if(count($this->table_inf[$table_name]['data_inf'][$pk_id-1]) == 0)
            {
                $pk_id = $pk_id - 1;
                $key_back = 0;
            }
        }
        if($key_back == 1)
        {
            $this->table_inf[$table_name]['data_inf'][$pk_id] = array();
            if($prt_name != "")
            {
                if(!isset($this->table_inf[$prt_name]['data_inf'][$prt_pkid][$table_name]))
                    $this->table_inf[$prt_name]['data_inf'][$prt_pkid][$table_name] = array();
                array_push($this->table_inf[$prt_name]['data_inf'][$prt_pkid][$table_name], $pk_id);
            }
        }

        $f_index = 0;
        $record_data = array();

        foreach($data as $key => $value)
        {
            $f_index ++;
            $flag = 1;
            if(is_array($value))
            {
                 if((count($value) == 1) && (isset($value[0])) && (!is_array($value[0])))
                 {
                     $value = $value[0];
                 }else{
                    if(!is_numeric($key))
                    {
                        if(!isset($this->table_inf[$table_name]['create_inf'][$key]))
                        {
                            $this->table_inf[$table_name]['create_inf'][$key] = "varchar";
                            $this->table_inf[$table_name]['data_inf'][$pk_id][$key] = array();
                        }
                    }
                    if(is_numeric($key)){
                        $this->test($value, $table_name,$prt_name, $prt_pkid);
                    }
                    else
                        $this->test($value, $key, $table_name, $pk_id);
                    $flag = 0;
                } 
            }
            if($flag == 1)
            {
                if(!isset($this->table_inf[$table_name]['create_inf'][$key]))
                {
                    $this->table_inf[$table_name]['create_inf'][$key] = "none";
                    $this->table_inf[$table_name]['data_inf'][$pk_id][$key] = "";
                }
                if(is_numeric($value) && ($this->table_inf[$table_name]['create_inf'][$key] == "none"))
                    $this->table_inf[$table_name]['create_inf'][$key] = "int";
                else
                    $this->table_inf[$table_name]['create_inf'][$key] = "varchar";
                $this->table_inf[$table_name]['data_inf'][$pk_id][$key] = $value;
            }
        }
    }

    function create_tbl_createquery()
    {
        $create_query = "";
        foreach ($this->table_inf as $tbl_name => $records) {
            $create_tbl_query = "DROP TABLE IF EXISTS `".$tbl_name."`; CREATE TABLE `".$tbl_name."` (`primary_id` int(11) NOT NULL AUTO_INCREMENT,";
            foreach ($records['create_inf'] as $field_name => $field_type) {
                if($field_type == "int")
                {
                    $create_tbl_query .= "`".$field_name."` int(11) DEFAULT NULL,";
                }else
                    $create_tbl_query .= "`".$field_name."` varchar(512) DEFAULT NULL,";
            }
            $create_query .= $create_tbl_query.$this->end_table;
        }
        return $create_query;
    }

    function create_tbl_insert()
    {
        $flag = 1;
        while($flag == 1)
        {
            $flag = 0;
            foreach ($this->table_inf as $tbl_name => $records) 
            {
                foreach ($records['data_inf'] as $lv1 => $lv1_records)
                {
                    foreach ($records['data_inf'] as $lv2 => $lv2_records)
                    {
                        if(($lv1 != $lv2) && ($lv1_records == $lv2_records))
                        {
                            $flag = 1;
                            foreach ($this->table_inf as $opti_name => $opti_records) 
                            {
                                if(isset($opti_records['create_inf'][$tbl_name]))
                                {
                                    foreach ($opti_records['data_inf'] as $opti_id => $opti_value)
                                    {
                                        if(isset($this->table_inf[$opti_name]['data_inf'][$opti_id][$tbl_name]))
                                        {
                                            if(is_array($this->table_inf[$opti_name]['data_inf'][$opti_id][$tbl_name]))
                                            {
                                                $filed_vlist = $this->table_inf[$opti_name]['data_inf'][$opti_id][$tbl_name];
                                                foreach ($filed_vlist as $del_id => $del_value)
                                                {
                                                    if($del_value == $lv2)
                                                        $this->table_inf[$opti_name]['data_inf'][$opti_id][$tbl_name][$del_id] = $lv1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            unset($this->table_inf[$tbl_name]['data_inf'][$lv2]);
                            goto loop1;
                        }
                    }
                }
            }
            loop1:
        }
        $inset_qeury = "";
        foreach ($this->table_inf as $tbl_name => $records) 
        {
            //$tbl_name = $this->prifix.$tbl_name;
            if(("cache" != $tbl_name) && ("team" != $tbl_name) && ("player" != $tbl_name) && ("team_A" != $tbl_name) && ("team_B" != $tbl_name) && ("player_off" != $tbl_name) && ("player_on" != $tbl_name))
            {
                $this->table_inf[$tbl_name]['count'] = DB::table($this->prifix.$tbl_name)->max('primary_id');
            }
        }

        foreach ($this->table_inf as $tbl_name => $records) 
        {
            //$tbl_name = $this->prifix.$tbl_name;
            if(("cache" != $tbl_name) && ("team" != $tbl_name) && ("team_A" != $tbl_name) && ("team_B" != $tbl_name) && ("player" != $tbl_name) && ("player_off" != $tbl_name) && ("player_on" != $tbl_name))
            {
                foreach ($records['data_inf'] as $pk_id => $record_value) 
                {
                    $insert_fields = "`primary_id`";
                    $insert_value = $pk_id + $this->table_inf[$tbl_name]['count'];
                    foreach ($record_value as $field_name => $field_value) {
                        if(("cache" != $field_name) && ("team" != $field_name) && ("team_A" != $field_name) && ("team_B" != $field_name) && ("player" != $field_name) && ("player_off" != $field_name) && ("player_on" != $field_name))
                        {
                            if(!is_array($field_value))
                            {
                                $insert_fields .= ", `".$field_name."`";
                                $field_value = str_replace("'", "\'", $field_value);
                                $insert_value .= ", '".$field_value."'";
                            }else{
                                foreach ($field_value as $f_key => $f_value) {
                                    $field_value[$f_key] += $this->table_inf[$field_name]['count'];
                                }
                                $insert_fields .= ", `".$field_name."`";
                                $tmp_field_value = implode("_", $field_value);
                                $insert_value .= ", '".$tmp_field_value."'";
                            }
                        }else{
                            if(is_array($field_value))
                            {
                                foreach ($field_value as $f_key => $f_value) {
                                    if(isset($this->table_inf[$field_name]['data_inf'][$f_value]['id']))
                                        $field_value[$f_key] = $this->table_inf[$field_name]['data_inf'][$f_value]['id'];
                                    else
                                        $field_value[$f_key] = NULL;//-1;
                                }
                                $insert_fields .= ", `".$field_name."`";
                                $tmp_field_value = implode("_", $field_value);
                                $insert_value .= ", '".$tmp_field_value."'";
                            }else{
                                $insert_fields .= ", `".$field_name."`";
                                $field_value = str_replace("'", "\'", $field_value);
                                $insert_value .= ", '".$field_value."'";
                            }

                            
                        }
                    }
                    $inset_qeury .= $this->pre_insert."`".$this->prifix.$tbl_name."` (".$insert_fields.$this->middle_insert.$insert_value.");";
                //}
                }
            }else if("cache" != $tbl_name)
            {
                $db_tbl_name = $tbl_name;
                if(("team_A" == $tbl_name) || ("team_B" == $tbl_name))
                    $db_tbl_name = "team";
                else if(("player_off" == $tbl_name) || ("player_on" == $tbl_name))
                    $db_tbl_name = "player";
                foreach ($records['data_inf'] as $pk_id => $record_value) 
                {
                    foreach ($record_value as $field_name => $field_V)
                    {
                        if(is_array($field_V))
                        {
                            $record_value[$field_name] = implode("_", $field_V);
                        }
                    }

                    $process_flg = 1;
                    if(isset($record_value['id']))
                    {
                        $user = DB::table($this->prifix.$db_tbl_name)->where('id', $record_value['id'])->first();
                        if(count($user) > 0)
                        {
                            $id = $record_value['id'];
                            unset($record_value['id']);
                            DB::table($this->prifix.$db_tbl_name)->where('id', $id)->update($record_value);
                            $process_flg = 0;
                        }
                    }
                    if($process_flg == 1)
                        DB::table($this->prifix.$db_tbl_name)->insert($record_value);                    
                }
            }
        }
        return $inset_qeury;
    }
}
