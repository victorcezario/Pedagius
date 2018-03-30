<?php
class DBPracas extends CI_Model {

        public function __construct()
        {
                parent::__construct();
                // Your own constructor code
                //$this->db = $this->load->database('db');
        }

        public function init(){
                $query = $this->db->query('SELECT * FROM pedagio WHERE LAT = ""');
                $key = 'AIzaSyBRZ5R_9jVlX9jUmsUd__71K3hzroBcIpk';
                //echo $this->table->generate($query);
                foreach ($query->result_array() as $row)
                {
                     $address = 'Pedagio+'.str_replace(' ', '+', $row['RODOVIA'].',+KM+'.round($row['KM']).',+'.$row['PRACA']);
                     $address = str_replace('(Bloqueio)','',$address);
                     $address = str_replace('N/S','',$address);
                     $address = str_replace('L/O','',$address);
                     $address = str_replace('O/L','',$address);
                     $return = $this->SearchMapsGeocode($key,$address);
                     print_r($return);
                        if($return != false){
                              $this->db->set($return);
                              $this->db->where('ID', $row['ID']);
                              $this->db->update('pedagio');
                                echo $this->db->last_query();  
                        }else{
                                $this->db->set('UPDT','NA');
                              $this->db->where('ID', $row['ID']);
                              $this->db->update('pedagio');
                        }
                        
                }
        }

        public function SearchMapsGeocode($key,$address){
                $address = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$key.'';
                $json = file_get_contents($address);
                $json = json_decode($json, true);
                //echo '<pre>';
                if($json['status'] == 'OK'){
                   $arr = array(
                        'LAT' => $json['results'][0]['geometry']['location']['lat'],
                        'LNG' => $json['results'][0]['geometry']['location']['lng'],
                        'UPDT' => 'OK',
                        'ESTADO' => $json['results'][0]['formatted_address']
                   );
                   $array = $json['results'][0]['geometry']['location'];
           }else{
                return false;
           }
                
                //print_r($array);
                return $arr;

        }

        public function calculate(){
                $json = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=%22Londrina,PR%22&destination=%22Curitiba,PR%22');
                require_once "MathUtil.php";
                require_once "PolyUtil.php";
                require_once "SphericalUtil.php";
                require_once "Polyline.php";
        }
}