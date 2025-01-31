<?php

namespace App\Classes;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // Throw an Exception with the error message and details
    throw new \Exception("$errstr in $errfile on line $errline", $errno);
});

// import the base class
use App\Classes\BaseClass;


// create a class for 5D
class Class11x5 extends BaseClass{


     protected $lottery_id ;
     protected $game_group ;
     protected $name ;

     protected $has_fantan = false;

public function __construct(object $game_type){
    $this->lottery_id    = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}



public function eleven_5(array $draw_numbers,int $index = 0,int $length = 5): array {
        
    $results = [];
    foreach ($draw_numbers as $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($results, [self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR => implode(",", $draw_number),"sum" =>  array_sum(array_slice($draw_number,$index,$length))]);
    }

    return $results;

}// end of eleven_5(): return the wnning number:format ["winning"=>"1,2,3,4,5"]




public function fun (array $draw_numbers){
       $results = [];
    foreach ($draw_numbers as $value) {
        $draw_number = $number_sort = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        sort($number_sort);
        array_push($results, [self::WINNING_NUMBER_STR => $draw_period,self::WINNING_PERIOD_STR => implode(",", $draw_number),"mid" =>  $number_sort[2]]);
    }

    return $results;
}

public function two_sides_2sides(array $draw_results): array
{

    $history_array = [];
    foreach ($draw_results as $draw_result) {
        $draw_period = $draw_result[self::DRAW_PERIOD_STR];
        $draw_number = $draw_result[self::DRAW_NUMBER_STR];

        $sum = array_sum($draw_number);
        $is_big_small = $sum > 30 ? "B" : (($sum === 30)  ? "Tie" : "S");
        $is_odd_even    = $sum % 2 === 0 ? "E" : "O";
        $is_dragon_tiger  = $draw_number[0] > $draw_number[4]  ? "D" : "T";
        $tail_big_small_split =  str_split((string) array_reduce($draw_number, function ($init, $curr) {
            return $init + intval(str_split($curr)[1]);
        }));
        $tail_big_small_len = count($tail_big_small_split);
        $tail_big_small_digit     = $tail_big_small_len === 1 ? ((int)$tail_big_small_split[0]) : ((int)$tail_big_small_split[1]);
        $tail_big_small_result = ($tail_big_small_digit >= 5) ? "B" : "S";

        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(",", $draw_number), 'big_small' => $is_big_small, 'odd_even' => $is_odd_even, 'dragon_tiger' => $is_dragon_tiger, 'tail_big_small' => $tail_big_small_result]);
    }

    return $history_array;
}


public function board_game(Array $draw_numbers){

    $history_array = [];
    foreach($draw_numbers as $draw_obj){
        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $sum         = array_sum($draw_number);
        $dragon_tiger = (intval($draw_number[0]) > intval($draw_number[4])) ? "Dragon" : ( (intval($draw_number[0]) < intval($draw_number[4])) ? "Tiger" : "Tie");
        //TODO: this is the real solution, but using the one below for demo for Mr. Eben.
        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR =>implode(",",$draw_number),'sum' => $sum,"b_s" =>  ($sum >= 31 && $sum <= 40) ? 'Big' : 'Small', 'o_e' => ($sum % 2 == 0)  ? 'Even' : 'Odd','dragon_tiger' =>  $dragon_tiger,'bull_bull' => self::modifiedCalculateBull_11x5($draw_number) ]);
        // array_push($history_array, ["draw_period" => $draw_period,"winning"=>implode(",",$draw_number),'sum' => $sum ]);
    }
  return $history_array;
}

// this will generate the history for std
public function std(array $drawNumbers):array{ 
   
        
        $history = [
            'first_three'           => $this->eleven_5($drawNumbers,0,3),
            'first_two'             => $this->eleven_5($drawNumbers,0,2),
            'any_place'             => $this->eleven_5($drawNumbers,0,3),
            'fixed_place'           => $this->eleven_5($drawNumbers,0,3),
            'pick'                  => $this->eleven_5($drawNumbers,0,3),
            'fun'                   => $this->fun($drawNumbers),
         ];
         return $history;
   
    
 }

public function two_sides_first_group(Array $draw_numbers,int $start_index,int $end_index) : array {
        
        $layout       = array_fill(1,11,0);
        $layout_keys  = array_map(function($key){
            return strlen("{$key}") != 1 ? "{$key}" : "0".$key ;
         },array_keys($layout)); 
        
        $history_array = []; 
        foreach($draw_numbers as $p_key => $item) {
            $draw_number = $item[self::DRAW_NUMBER_STR];
            $draw_period = $item[self::DRAW_PERIOD_STR];
             $slicedArray         = array_slice($draw_number,$start_index,$end_index);
             $keys_in_draw_number = array_map(function($key){
                return strlen($key) == 1 ? $key : "0".$key ;
             },array_intersect($slicedArray, $layout_keys)); 
           
                 foreach ($layout_keys as $key => $value) {
                   $layout[$key + 1]  = in_array($value,$keys_in_draw_number) ? (string)$value 
                                  : (gettype($layout[$key + 1]) === "string" ? 1 : intval($layout[$key + 1]) + 1);
                 }
        array_unshift($history_array,[self::WINNING_PERIOD_STR => $draw_period,self::WINNING_NUMBER_STR=>implode(",",$draw_number),"layout"=>array_combine(["first","second","third","fourth","fifth","sixth","seventh","eighth","ninth","tenth","eleventh"],$layout)]);
        }
        return $history_array;
}

// this will generate the history for two_sides
public function two_sides(array $drawNumbers):array{ 
    return [
        'rapido'           =>  $this->eleven_5($drawNumbers),
        'two_sides'        =>  $this->two_sides_2sides($drawNumbers),
        'pick'             =>  ['pick' => $this->eleven_5($drawNumbers) , "first_2" => $this->two_sides_first_group($drawNumbers, 0, 2),
        "first_3"          =>  $this->two_sides_first_group($drawNumbers, 0, 3)],
        'straight'         =>  ["first_2" => $this->two_sides_first_group($drawNumbers, 0, 2),"first_3" => $this->two_sides_first_group($drawNumbers, 0, 3)],
        ];

}


// this will generate the history for board_games
public function board_games(array $drawNumbers):array{return $this->board_game($drawNumbers); }


}