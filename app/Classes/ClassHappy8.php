<?php

namespace App\Classes;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');


// import the base class
use App\Classes\BaseClass;



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
   
    $tie = 1;
    $odd = 1;
    $even = 1;
    $historyArray = [];
  
    foreach ($drawNumbers as $item) {
    
    $value       = $item[self::DRAW_NUMBER_STR];
    $draw_period = $item[self::DRAW_PERIOD_STR];
    $num_odds = 0;
    $draw_num = count($value) ;
    for ($i=0; $i < $draw_num; $i++) { 
        if (intval($value[$i]) % 2 == 1) {
            $num_odds += 1;
        } 
    }
    // echo $num_odds;
    $win = $num_odds == ($draw_num / 2) ? "Tie" : ( $num_odds > ($draw_num / 2) ? "Odd" : "Even" );

     // Assuming findPattern() is defined with similar logic in PHP
    $mydata = array(
    self::WINNING_PERIOD_STR => $draw_period,
    self::WINNING_NUMBER_STR => implode(',',$value),
    'tie'  =>  ($win  == "Tie"   ? "Tie"  : $tie),
    'odd'  =>  ($win  == "Odd"   ? "Odd"  : $odd),
    'even' =>  ($win  == "Even"  ? "Even" : $even),
    
    );
    array_unshift($historyArray, $mydata);
    $currentPattern = array_values($mydata);
    sort($currentPattern);
    $currentPattern = $currentPattern[4];
    // Update counts
    $odd = ($currentPattern == "Odd")  ? 1 :  ($odd += 1);
    $tie = ($currentPattern == "Tie") ? 1  :  ($tie += 1);
    $even = ($currentPattern == "Even") ? 1 : ($even += 1);
    }
 return $historyArray;  
}



public function b_s_o_e_sum_happy8(Array $drawNumbers) : Array{

    $big = $small = $odd = $even = 1;
    
    $historyArray = [];
    $drawNumbers = array_reverse($drawNumbers);
    foreach ($drawNumbers as $value) {
       
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        $big_results   = ($sum > 810) ? "B" : $big;
        $small_results = ($sum < 810 ) ? "S" : $small;
        $is_tie = ($sum == 810);
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
          $big   = trim($big_small)  === "B"  ? 1 : ($is_tie ? $big   += 1 : 1);
          $small = trim($big_small)  === "B"  ? ($small += 1) :($is_tie ? $small += 1 : 1)  ;
          $odd   = trim($odd_even)   === "O"  ? 1 : ($odd   += 1);
          $even  = trim($odd_even)   === "O"  ? ($even  += 1) : 1;
        }


    return array_reverse($historyArray);
    
}// end of 



public function two_sides_history(Array $draw_numbers){


    $history_array = [];


    foreach($draw_numbers as $value){
       
        $num_odd = 0;
        $num_first = 0;
        $five_elements = "";

        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
         $sum = array_sum($draw_number);

        foreach ($draw_number as $val) {
            if(intval($val) < 41) $num_first += 1;
            if(intval($val) % 2 == 1) $num_odd += 1;
         }

       if($sum >= 210 && $sum <= 695){
        $five_elements = "Gold";
       }elseif($sum >= 696 && $sum <= 763){
        $five_elements = "Wood";
       
       }elseif($sum >= 764 && $sum <= 856){
        $five_elements = "Water";
       
       }
       elseif($sum >= 857 && $sum <= 924){
        $five_elements = "Fire";
       }
       elseif($sum >= 925 && $sum <= 1410){
        $five_elements = "Earth";
       }
        

       $first_last_more_result = $num_first > (count($draw_number)/2) ? "First" : (($num_first < (count($draw_number)/2) ? "Last" : "Tie"));
       $odd_even_more_result = $num_odd > (count($draw_number)/2) ? "O" : (($num_odd < (count($draw_number)/2) ? "E" : "Tie"));
       array_push($history_array,[
       self::WINNING_PERIOD_STR=>$draw_period,
       self::WINNING_NUMBER_STR=> implode(",", $draw_number),
       "sum_chart" => $sum,
       "sum" => $sum == 810  ? "Tie" :($sum > 810 ? "B":"S") ." ". (($sum % 2 == 0) ? "E" : "0"),
       "first_last"=> $first_last_more_result,
       "odd_even" => $odd_even_more_result,
       "five_elements"=> $five_elements]);
    }




    return $history_array;
}

public function ball_no( Array $draw_numbers):array{
    $history_array = [];
    foreach($draw_numbers as $value){
        array_push($history_array,[self::WINNING_PERIOD_STR=>$value[self::DRAW_PERIOD_STR],self::WINNING_NUMBER_STR=>implode(",",$value[self::DRAW_NUMBER_STR])]);   
    }
    return $history_array;
}



// this will generate the history for std
public function std(array $drawNumbers):array{  
return ['pick'=> $this->eleven_5_happy8($drawNumbers),'fun'=> $this->over_under($drawNumbers), 'odd_even'=> $this->odd_even($drawNumbers),'b_s_o_e_sum_happy8'=> $this->b_s_o_e_sum_happy8($drawNumbers),
    ];
 }

// this will generate the history for two_sides
public function two_sides(array $drawNumbers):array{  return ['two_sides' => $this->two_sides_history($drawNumbers), 'ball_no' => $this->ball_no($drawNumbers), ]; }

// this will generate the history for fantan
public function fantan(array $draw_data):array{ 

     // $draw_numbers = $draw_data['draw_number'];
    // $draw_period = $draw_data['period'];
    $final_res = [];
    foreach($draw_data as $key => $data){
    $drawNumber = $data[self::DRAW_NUMBER_STR];
    $draw_period = $data[self::DRAW_PERIOD_STR];
    $DRAW_NUMBERS = array_map('intval', $drawNumber);
    $SUM_OF_DRAW_NUMBERS = array_sum($DRAW_NUMBERS);
   
     //!UNCOMMENT THIS CODE FOR FRONTEND COMPATIBILITY
    // $res['draw_number'] = $drawNumber;
    
   
    $res[self::WINNING_NUMBER_STR] = implode(',',$drawNumber);
    $res[self::WINNING_PERIOD_STR] = $draw_period;

    $splitted_sum  = str_split((string)$SUM_OF_DRAW_NUMBERS);
    $sum_remainder = array_sum($splitted_sum) % 4;
    $sum_remainder = $sum_remainder == 0 ? 4 : $sum_remainder;
    $res['sum']= $SUM_OF_DRAW_NUMBERS;

    $res['big_small'] =  self::bigSmall($SUM_OF_DRAW_NUMBERS, 811, 1410, 210, 809);
    $res['odd_even'] = $SUM_OF_DRAW_NUMBERS % 2 == 0 ? "Even" : "Odd";
    $res['fan'] =  "{$sum_remainder} fan";
    $res['sum_remainder'] =  "({$splitted_sum[0]} + {$splitted_sum[1]} + {$splitted_sum[2]}) % 4 = {$sum_remainder}";
    
    //!UNCOMMENT THIS FOR CONSISTENCY WITH THE FRONTEND.
    // $res['happy8'] = happy8NaturalElement($SUM_OF_DRAW_NUMBERS);
    $res['elements'] = self::happy8NaturalElement($SUM_OF_DRAW_NUMBERS);
    $final_res[] = $res;
 }
        
 return $final_res;

}

// this will generate the history for board_games
public function board_games(array $drawNumbers):array{ 

    $tie = 1;
    $over = 1;
    $under = 1;

    $history_array = [];
    $drawNumbers = array_reverse($drawNumbers);

    
    foreach($drawNumbers as $draw_obj){

        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        sort($draw_number);
        $tenth_value   =  intval($draw_number[9]);
        $eleveth_value =  intval($draw_number[10]);
        $is_tie        = (($tenth_value >= 1  && $tenth_value <= 40) && ($eleveth_value >= 41 && $eleveth_value <= 80) );
        $is_under      = (($tenth_value >= 1  && $tenth_value <= 40) && ($eleveth_value >= 1  && $eleveth_value <= 40) );
        $is_over       = (($tenth_value >= 41 && $tenth_value <= 80) && ($eleveth_value >= 41 && $eleveth_value <= 80) );
        
        // Assuming findPattern() is defined with similar logic in PHP
        $over_under_data = [
            'over' => $is_over ? "Over" : $over,
            'tie'  =>  $is_tie ? "Tie" : $tie,
            'under' => $is_under ? "Under" : $under,
        ];
        
        array_push($history_array, [ self::WINNING_PERIOD_STR => $draw_period,
         self::WINNING_NUMBER_STR=> implode(",",$draw_number), 
         "b_s" =>  $sum >= 210 && $sum <= 809  ? 'Small' : ($sum > 810 && $sum <= 1410 ? 'Big' : ''),
         'o_e' => ($sum % 2 == 0)  ? 'Even' : 'Odd','sum' => $sum,
         'over' => $is_over ? "Over" : $over,
         'tie'  =>  $is_tie ? "Tie" : $tie,
         'under' => $is_under ? "Under" : $under, ]);

        $currentPattern = array_values($over_under_data);
        sort($currentPattern);
        $currentPattern = $currentPattern[2];
       
        // Update counts
       $over   = ($currentPattern == "Over")  ? 1 : ($over += 1);
       $tie    = ($currentPattern == "Tie")   ? 1 : ($tie += 1);
       $under  = ($currentPattern == "Under") ? 1 : ($under += 1);
}
return array_reverse($history_array);



 }











 public static function happy8NaturalElement($number) {
    if (!is_numeric($number)) {
        return "Invalid input. Please enter a number.";
    }

    if ($number >= 925 && $number <= 1410) {
        return "Earth";
    } elseif ($number >= 210 && $number <= 695) {
        return "Gold";
    } elseif ($number >= 696 && $number <= 763) {
        return "Wood";
    } elseif ($number >= 764 && $number <= 856) {
        return "Water";
    } elseif ($number >= 857 && $number <= 924) {
        return "Fire";
    } else {
        return "The number is outside the specified ranges.";
    }
}

}

//Abdul-Rahman bin Sahar