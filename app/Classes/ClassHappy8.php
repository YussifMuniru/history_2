<?php

namespace App\Classes;



require_once("../../vendor/autoload.php");


// import the base class
use App\Classes\BaseClass;
// import the database
use App\Config\Database;
// import the redis cache
use App\Config\RedisClient;
// import the logger service
use App\Logger\AppLogger;
use App\Logger\LogLevel;


// create a class for 5D
class ClassHappy8 extends BaseClass{


     protected $lottery_id ;
     protected $game_group ;
     protected $name ;



public function __construct(object $game_type){
    $this->lottery_id    = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}


public function eleven_5_happy8(Array $draw_numbers)  : Array { 
   
    $results = [];
    foreach ($draw_numbers as $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($results,[self::WINNING_PERIOD_STR=>$draw_period,self::WINNING_NUMBER_STR => implode(",",$draw_number)]); 
    }
    return $results;
}// return the wnning number:format ["winning"=>"1,2,3,4,5"]



public function over_under(Array $drawNumbers) : Array{
   
   
    $tie = $over = $under = 1;

    $historyArray = [];
    $drawNumbers = array_reverse($drawNumbers);

    foreach ($drawNumbers as $draw_number) {
        $value = $draw_number[self::DRAW_NUMBER_STR];
        $draw_period = $draw_number[self::DRAW_PERIOD_STR];
       
        sort($value);
       
        $tenth_value = intval($value[9]);
        $eleveth_value = intval($value[10]);
        $is_tie = (($tenth_value >= 1 && $tenth_value <= 40) && ($eleveth_value >= 41 && $eleveth_value <= 80) );
        $is_over = (($tenth_value >= 1 && $tenth_value <= 40) && ($eleveth_value >= 1 && $eleveth_value <= 40) );
        $is_under = (($tenth_value >= 41 && $tenth_value <= 80) && ($eleveth_value >= 41 && $eleveth_value <= 80) );
         
        // Assuming findPattern() is defined with similar logic in PHP
        $mydata = array(
            self::WINNING_NUMBER_STR => implode(',',$value),
            self::WINNING_PERIOD_STR => $draw_period,
            'over' => $is_over ? "Over" : $over,
            'tie'  =>  $is_tie ? "Tie" : $tie,
            'under' => $is_under ? "Under" : $under,
            
          );
        
        array_push($historyArray, $mydata);
        $currentPattern = array_values($mydata);
        sort($currentPattern);
       // print_r($currentPattern);
        $currentPattern = $currentPattern[4];
       
        // Update counts
       $over  = ($currentPattern == "Over")  ? 1 : ($over += 1);
       $tie   = ($currentPattern == "Tie") ? 1 : ($tie += 1);
       $under = ($currentPattern == "Under") ? 1 : ($under += 1);
       
    }
    return array_reverse($historyArray);
}// end of over_under(). return the max category,either over or under 



public function odd_even(Array $drawNumbers) : Array{
   
    $tie =  $odd = $even = 1;
    $historyArray = [];
    foreach ($drawNumbers as $item) {
    $value       = $item[self::DRAW_NUMBER_STR];
    $draw_period = $item[self::DRAW_PERIOD_STR];
    $num_odds = 0;
    for ($i=0; $i < 20; $i++) { 
        if ($value[$i] % 2 == 1) {
            $num_odds += 1;
        if($num_odds >= 10) break ;
        } 
    }
     // Assuming findPattern() is defined with similar logic in PHP
    $mydata = array(
    self::WINNING_PERIOD_STR => $draw_period,
    self::WINNING_NUMBER_STR => implode(',',$value),
    'odd'  => $num_odds > 10 ? "Odd" : $odd,
    'tie'  =>  $num_odds == 10 ? "Tie" : $tie,
    'even' => $even < 10? "Even" : $even,
    
    );
    array_unshift($historyArray, $mydata);
    $currentPattern = array_values($mydata);
    sort($currentPattern);
    $currentPattern = $currentPattern[2];
    // Update counts
    $odd = ($currentPattern == "Edd")  ? 1 : ($odd += 1);
    $tie = ($currentPattern == "Tie") ? 1 : ($tie += 1);
    $even = ($currentPattern == "Even") ? 1 : ($even += 1);
    }
 return $historyArray;  
}



public function b_s_o_e_sum_happy8(Array $drawNumbers) : Array{

    $big = $small = $odd = $even = 1;
    
    $historyArray = [];
    foreach ($drawNumbers as $value) {
       
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        $big_results   = ($sum >= 810) ? "B" : $big;
        $small_results = ($sum < 810 ) ? "S" : $small;
        $odd_results   = ($sum % 2 === 1)  ? "O" : $odd;
        $even_results = ($sum % 2 === 0) ? "E" : $even;
        $mydata = [
            self::WINNING_NUMBER_STR      =>     implode(',',$draw_number),
            self::WINNING_PERIOD_STR      =>     $draw_period,
            'sum'                         =>     $sum ,
            'big_small'                   =>     "{$big_results} {$small_results}",
            'odd_even'                    =>     "{$odd_results} {$even_results}"
        ];
        array_push($historyArray, $mydata);
        $big_small = explode(" ",$mydata['big_small'])[0];
        $odd_even = explode(" ",$mydata['odd_even'])[0];
        
          // Update counts
          $big   = trim($big_small)  === "B"  ? 1 : ($big   += 1);
          $small = trim($big_small)  === "B"  ? ($small += 1) : 1 ;
          $odd   = trim($odd_even)   === "O"  ? 1 : ($odd   += 1);
          $even  = trim($odd_even)   === "O"  ? ($even  += 1) : 1;
        }
    return $historyArray;
    
}// end of 


// this will generate the history for std
public function std(array $drawNumbers):array{  
return ['pick'=> $this->eleven_5_happy8($drawNumbers),'fun'=> $this->over_under($drawNumbers), 'odd_even'=> $this->odd_even($drawNumbers),'b_s_o_e_sum_happy8'=> $this->b_s_o_e_sum_happy8($drawNumbers),
    ];
 }

// this will generate the history for two_sides
public function two_sides(array $draw_data):array{ return []; }

// this will generate the history for fantan
public function fantan(array $draw_data):array{ return []; }

// this will generate the history for board_games
public function board_games(array $draw_data):array{ return []; }




}