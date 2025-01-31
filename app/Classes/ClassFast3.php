<?php

namespace App\Classes;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');


// import the base class
use App\Classes\BaseClass;



// create a class for 5D
class ClassFast3 extends BaseClass{

     const NATURAL_OBJECTS = ["1"=>"Fish","2"=>"Prawn","3"=>"Gourd","4"=>"Cash","5"=>"Crab","6"=>"Rooster"];

     protected $lottery_id ;
     protected $game_group ;
     protected $name ;



public function __construct(object $game_type){
    $this->lottery_id    = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}

public function b_s_o_e_sum(Array $drawNumbers) : array { 
   
    $historyArray = [];

    // print_r($drawNumbers);
    foreach ($drawNumbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        $b_s = ($sum >= 3 && $sum <= 10) ? "S" : "B";
        $o_e =($sum % 2 === 1) ?  "O" : "E" ;

        $historyArray[] =  [self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR =>implode(",",$draw_number),"sum"=> $sum,"big_small"=> $b_s, "odd_even"=> $o_e];
  }

    return $historyArray;
}//end of b_s_o_e_sum(). returns big(11-18)/small(3-10), odd/even, sum


public function sum(Array $drawNumbers):array {
    
    $history_array = [];
    foreach ($drawNumbers as $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($history_array,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR=>implode(",",$draw_number),"sum"=>  array_sum($draw_number)]);
    }
   return $history_array;
} //end of sum(). return draw numbers & sum of the draw numbers
   
public function three_of_a_kind($drawNumbers){

        $three_no = 1;
        $one_pair = 1;
        $three_of_a_kind = 1;
        $three_row = 1;
        
    
        $historyArray = [];
        $drawNumbers = array_reverse($drawNumbers);
        foreach ($drawNumbers as  $item) {
            $draw_number = $item["draw_number"];
            $draw_period = $item["period"];


            // Assuming findPattern() is defined with similar logic in PHP
            // $mydata = array(
            //     'draw_period' => $draw_period,
            //     "winning"=>implode(",",$draw_number), 
            //     'three_no' => count(array_unique($draw_number)) === count($draw_number) ? "three no" : $three_no,
            //     'three_of_a_kind' => (count($draw_number) - count(array_unique($draw_number))) === 2 ? "three of a kind" : $three_of_a_kind,
            //     'three_row'=> three_row( $draw_number) ? "three row" : $three_row,
            //     'one_pair' => (count($draw_number) - count(array_unique($draw_number))) === 1 ? "one pair" : $one_pair,
                
            //  );
            $mydata = array(
                'draw_period' => $draw_period,
                "winning"=>implode(",",$draw_number), 
                'three_no' =>        self::find_pattern_form_lack($draw_number,'three_no') ? "Three No." : $three_no,
                'three_of_a_kind' => self::find_pattern_form_lack($draw_number,'toak') ? "Three of a kind" : $three_of_a_kind,
                'three_row'=>        self::find_pattern_form_lack($draw_number,'3_row') ? "3 Row" : $three_row,
                'one_pair' =>        self::find_pattern_form_lack($draw_number,'pair') ? "One Pair" : $one_pair,
                );
            array_push($historyArray, $mydata);
    
         
            $currentPattern = array_values($mydata);
            //sort($currentPattern);
          //  print_r($currentPattern);
           // $currentPattern = $currentPattern[5];
            
           
            // Update counts
           $three_no        = array_search("Three No.",$currentPattern)  ? 1 : ($three_no += 1);
           $one_pair        = array_search("One Pair",$currentPattern)   ? 1 : ($one_pair += 1);
           $three_of_a_kind = array_search( "Three of a kind",$currentPattern) ? 1 : ($three_of_a_kind += 1);
           $three_row       = array_search( "3 Row",$currentPattern) ? 1 : ($three_row += 1);
        //    $three_no = ($currentPattern == "Three No.")  ? 1 : ($three_no += 1);
        //    $one_pair = ($currentPattern == "One Pair") ? 1 : ($one_pair += 1);
        //    $three_of_a_kind = ($currentPattern == "Three of a kind") ? 1 : ($three_of_a_kind += 1);
        //    $three_row = ($currentPattern == "3 Row") ? 1 : ($three_row += 1);
          
            
        }
        return array_reverse($historyArray);
    
}// end of three_of_a_kind(). returns three of a kind(1 triple),one pair(2 same numbers),three no.(3 different numbers)


public function winning(Array $drawNumbers):array{ 

    $history_array = [];
    foreach ($drawNumbers as $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($history_array,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR=>implode(",",$draw_number)]);
    }
   return $history_array;
}//end of winning(). return ["winning"=>1,2,3];


public function two_sides_all_kinds(Array $draw_numbers): Array {

    $history_array = [];
    foreach ($draw_numbers as $val) {
            $draw_number = $val[self::DRAW_NUMBER_STR];
            $draw_period  = $val[self::DRAW_PERIOD_STR]; 
         
            array_push($history_array,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR=>implode(",",$draw_number),"sum"=>array_sum($draw_number),"b_s"=> array_sum($draw_number) >= 11 ? (count(array_unique($draw_number)) != 1 ? "B" : "T"):  "S" ,"fish_prawn_crab" => self::NATURAL_OBJECTS[$draw_number[0]]." ".  self::NATURAL_OBJECTS[$draw_number[1]]." ". self::NATURAL_OBJECTS[$draw_number[2]]]);
    }

  return $history_array;
  }


// this will generate the history for std
public function std(array $drawNumbers):array{ 

      return [
            'b_s_o_e_sum'     => $this->b_s_o_e_sum($drawNumbers), 
            'sum'             => $this->sum($drawNumbers), 
            'three_of_a_kind' => $this->three_of_a_kind($drawNumbers), 
            'three_no'        => $this->three_of_a_kind($drawNumbers), 
            'one_pair'        => $this->three_of_a_kind($drawNumbers), 
            'two_no'          => $this->three_of_a_kind($drawNumbers), 
            'guess_a_number'  => $this->winning($drawNumbers),
              ];

 }

// this will generate the history for two_sides
public function two_sides(array $drawNumber):array{ 
      
    return ['two_sides_all_kinds' => $this->two_sides_all_kinds($drawNumber),
            'fish_prawn_crab'     => $this->two_sides_all_kinds($drawNumber),
           ];
 }

// this will generate the history for fantan
public function fantan(array $draw_data):array{ 

    
     $final_res = [];
     foreach ($draw_data as $key => $data) {
        $drawNumber = $data[self::DRAW_NUMBER_STR];
        $draw_period = $data[self::DRAW_PERIOD_STR];
      
        $res[self::WINNING_NUMBER_STR] = implode(",",$drawNumber);
        $res[self::WINNING_PERIOD_STR] = $draw_period;

        $drawNumber = array_map('intval', $drawNumber);
        $sum_of_three = array_sum(array_slice($drawNumber, 0, 3));
       
        $res['sum'] = $sum_of_three;
        $res['big_small'] = count(array_unique($drawNumber)) == 1 ? 'Tie' :  self::bigSmall($sum_of_three, 11, 17, 4, 10);

        //TODO:MAKE SURE THAT APART FROM 49, THERE IS NO OTHER SUM THAT IS A TIE
        $res['odd_even']  = count(array_unique($drawNumber)) == 1 ? 'Tie' : self::oddEven($sum_of_three);
        
        $res['natural_elements'] = self::naturalElements($sum_of_three);
        $final_res[] = $res;
    }

    return $final_res; 
 }// this will generate the history for board_games


public function board_games(array $drawNumbers):array{ 

    $history_array = [];
    foreach($drawNumbers as $val){
        $draw_number  = $val[self::DRAW_NUMBER_STR];
        $draw_period  = $val[self::DRAW_PERIOD_STR]; 
        $sum = array_sum($draw_number);
        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR =>implode(",",$draw_number),"b_s" =>  $sum >= 4 && $sum <= 10  ? 'Small' : ($sum < 17 ? 'Big' : ''), 'o_e' => ($sum % 2 == 0)  ? 'Even' : 'Odd','sum' => $sum ]);
    }
    return $history_array;
 }



public static function three_row(Array $drawNumber) : bool{ 
    sort($drawNumber);
    return ($drawNumber[0] + 1 === $drawNumber[1] && $drawNumber[1] + 1  === $drawNumber[2]) ;
  }// end of three_row(). returns whether draw numbers are consecutive.(increments by 1)


 // Function to determine natural elements based on a number range
public static function naturalElements($number) {
    if ($number >= 3 && $number <= 7) {
        return "Wind";
    } elseif ($number >= 8 && $number <= 10) {
        return "Fire";
    } elseif ($number >= 11 && $number <= 13) {
        return "Thunder";
    } elseif ($number >= 14 && $number <= 18) {
        return "Light";
    } else {
        return "Number out of range";
    }
}

public static function find_pattern_form_lack($draw_number,$flag){
    sort($draw_number);
    switch ($flag){
        case "three_no":
            return (count(array_unique($draw_number)) == 3);
        case "3_row":
            return (($draw_number[1] - $draw_number[0]) == 1 && ($draw_number[2] - $draw_number[1]) == 1);
        case "toak":
            return (count(array_unique($draw_number)) == 1);
        case "pair":
            return (count(array_unique($draw_number)) == 2);
    }
   
}
}