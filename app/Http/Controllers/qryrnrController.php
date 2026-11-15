<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class qryrnrController extends Controller
{
    //

        protected $rq_global=null;
        protected $dta_global=null;


    public static function query1($rq){
     $rslt=" :: QUERRY RUNNER - > Query results here ";
     $rslt.=" :: ".json_encode($rq);
     return $rslt;
    }


    
    public static function sql_prepair($rq=null){
     $dta['column_values']=$rq['column_values'];
     $dta['columns']=" ";
     $dta['values']=" ";
     $dta['set_pairs']=" ";
     $dta['extras']=" ";
     $dta['table']=$rq['table'];
     $dta['select']=" ";
     $dta['insert']=" ";
     $dta['update']=" ";
     $dta['delete']=" ";
     $dta['condition']=" ";
     $dta['condition_prep']="";
     $dta['condition_prep_val']=null;

     if($rq!=null){
         $dta['set_pairs']="";
         $dta['columns']="";
         $dta['values']="";
         $counter=0;
         foreach($rq['column_values'] as $k => $vl)
             {
              $dta['columns'].=($counter>0)?",".$k:$k;
              $dta['values'].=($counter>0)?",'".$vl."'":"'".$vl."'";
              $dta['set_pairs'].=($counter>0)?", `".$k."`='".$vl."'":"`".$k."`='".$vl."'";
              $counter++;
             }
         $dta['columns'].="";
         $dta['values'].="";
         $dta['set_pairs'].="";

         if(isset($rq['condition']))
         if($rq['condition']!=null)
         foreach($rq['condition'] as $k => $vl)
             {
              $dta['condition'].=" ".$vl[0]." ".$vl[1]." ".$vl[2]." ".$vl[3];
              $dta['condition_prep'].=" ".$vl[0]." ".$vl[1]." ".$vl[2]." ?";
              $dta['condition_prep_val'][]=$vl[3]."";
             }

         if(isset($rq['extras']))
         if($rq['extras']!=null)
         for( $j=0;$j<count($rq['extras']);$j++)
             {
              $dta['extras'].=" ".$rq['extras'][$j];
             }
         $dta['select'].=" select ".$dta['columns']." from ".$dta['table']." where ".$dta['condition']." ".$dta['extras'];
         $dta['insert'].=" insert into ".$dta['table']." ".$dta['columns']." values ".$dta['values'];
         $dta['update'].=" update ".$dta['table']." set ".$dta['set_pairs']." where ".$dta['condition'];
         $dta['delete'].=" delete from ".$dta['table']." where ".$dta['condition'];
       }
     return $dta;
    }

public static function delete1($rq=null){
//DB::transaction(function () {
     $dta=self::sql_prepair($rq);
          DB::update('delete from '.$dta['table'].' where '.$dta['condition_prep'],$dta['condition_prep_val']);
     $rslt=DB::table($dta['table'])->get();
    var_dump($rslt);
    echo '<br>*****delete from '.$dta['table'].' where '.$dta['condition_prep'].','.implode(' ',$dta['condition_prep_val']);
//}, 5);
return json_encode($rslt);
} 

    
public static function update1($rq=null){
     $dta=self::sql_prepair($rq);
     $dta['set_pairs'].=" ,`updated_at`='".date("Y-m-d h:i:s")."'";
     $affected=DB::update('update '.$dta['table'].' set '.$dta['set_pairs'].' where '.$dta['condition_prep'],$dta['condition_prep_val']);
     $rslt=($affected < 1 ) ? null : DB::select("select * from ".$dta['table']." where ".$dta['condition_prep'], $dta['condition_prep_val']);
//     $rslt=DB::select("select * from ".$dta['table']." where ".$dta['condition_prep'], $dta['condition_prep_val']);
return json_encode($rslt);
} 

public static function insert1($rq=null){
     $dta=self::sql_prepair($rq);
     $dta['column_values']['created_at']=date("Y-m-d h:i:s");
     $data_id=DB::table($dta['table'])->insertGetId($dta['column_values']);
     $dta=DB::table($dta['table'])->find($data_id);
return json_encode($dta);
}

public static function insert1_bulk($params){
    $success=1;
    for($t=0;$t<=count($column_values);$t++)
     $params['column_values'][$t]['created_at']=date("Y-m-d h:i:s");
    $success++; 
     DB::table($params['table'])->insert($params['column_values']);
    $success++;
  return ($params['condition']==null)?$success:DB::select("select * from ".$params['table']." where ".$params['condition']);
}



public static function select1($rq=null){
//DB::transaction(function () {
     $dta=self::sql_prepair($rq);
     $dta['result'] = DB::select("select ".$dta['columns']." from ".$dta['table']." where ".$dta['condition_prep']."".($dta['extras']??""), $dta['condition_prep_val']);
//}, 5);
//echo $dta['condition_prep'];
//     var_dump($dta['condition_prep_val']);
return $dta['result'];
} 



 

public static function sql_select1($rq=null){
     $dta['body']=['status'=>'ok','message'=>'no query received','command'=>'none'];    
     $dta['code']='200';
     $dta=self::sql_prepair($rq);
     
     DB::insert('insert into '.$dta['table'].' (id,name) values (?,?)', [date('js'),'Test user']);

     $dta['result'] = DB::select("select * from ".$dta['table']." where id != :id", ['id' => 1]);
//     $dta['result'] = DB::select("select ".$dta['columns']." from ".$dta['table']." where id = :id", ['id' => 1]);
     return $dta;
    }

//Takes in a double array and returns single array of the condition parameters and the condition pair string.
public static function conditon_formater($condition_arrary){
    
}
   
}

