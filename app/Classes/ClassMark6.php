<?php

namespace App\Classes;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');


// import the base class
use App\Classes\BaseClass;
use App\Storage\Logs\AppLogger;
use App\Storage\Logs\LogLevel;


// create a class for 5D
class ClassMark6 extends BaseClass{


    const ZODIAC_NAMES  = ["rat","ox","tiger","rabbit","dragon","snake","horse","goat","monkey","rooster","dog","pig"];
    const FIVE_ELEMENTS = [
    'gold'  => ["01", "02",  "10", "23", "24", "31", "32", "39", "40"],
    'wood'  => ["05", "06", "13", "14", "27", "28", "35", "36", "43", "44"],
    'water' => ["11", "12", "19", "20", "33", "34", "41", "42", "49"],
    'fire'  => ["07", "08","09", "15", "16", "29", "30", "37", "38", "45"],
    'earth' => ["03", "04", "17", "18", "25", "26", "29", "30", "37", "38"]
 ];
 
     const ZODIAC_SKY = ["03", "15", "27", "39", "01", "13", "25", "37", "49", "12", "24", "36", "48", "10", "22", "34", "46", "08", "20", "32", "44", "05", "17", "29", "41"];

     const ZODIAC_GROUND = ["04", "16", "28", "40", "02", "14", "26", "38",  "11", "23", "35", "47",   "09", "21", "33", "45", "07", "19", "31", "43", "06", "18", "30", "42"];

     const ZODIAC_FIRST = ["04", "16", "28", "40", "03", "15", "27", "39", "02", "14", "26", "38", "01", "13", "25", "37", "49",  "12", "24", "36", "48",   "11", "23", "35", "47"];

     const ZODIAC_LAST = ["10", "22", "34", "46", "09", "21", "33", "45", "08", "20", "32", "44", "07", "19", "31", "43", "06", "18", "30", "42",  "05", "17", "29", "41"];

     const ZODIAC_POULTRY = ["04", "16", "28", "40", "10", "22", "34", "46", "09", "21", "33", "45", "07", "19", "31", "43", "06", "18", "30", "42", "05", "17", "29", "41"];

     const ZODIAC_BEAST = ["04", "16", "28", "40", "02", "14", "26", "38", "12", "24", "36", "48", "11", "23", "35", "47", "01", "13", "25", "37", "49", "08", "20", "32", "44"];

     const  ZODIAC_RED_BALLS  =  ["01", "02", "07", "08", "12", "13", "18", "19", "23", "24", "29", "30", "34", "35", "40", "45", "46"];
     const  ZODIAC_BLUE_BALLS =  ["03", "04", "09", "10", "14", "15", "20", "25", "26", "31", "36", "37", "41", "42", "47", "48"];
     const  ZODIAC_GREEN_BALLS = ["05", "06", "11", "16", "17", "21", "22", "27", "28", "32", "33", "38", "39", "43", "44", "49"];

     protected $lottery_id ;
     protected $game_group ;
     protected $name ;



public function __construct(object $game_type){
    $this->lottery_id = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}

public function winning_number_mark6(array $drawNumbers): array {

    $history_array = [];
    //code...
    foreach ($drawNumbers as $draw_number) {
       try {
            $item        = $draw_number['draw_number'];
            $draw_period = $draw_number['period'];
            array_push($history_array, ["draw_period" => $draw_period, "Ball_1" => $item[0], "Ball_2" => $item[1], "Ball_3" => $item[2], "Ball_4" => $item[3], "Ball_5" => $item[4], "Ball_6" => $item[5], "Extra_Ball" => $item[6]]);
        } catch (\Exception $e) {
            AppLogger::error(LogLevel::ERROR,$e);
            array_push($history_array, ["draw_period" => '', "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '']);
        }
    }

    return $history_array;
}

public function sum_of_two_sides_b_s_o_e(array $draw_numbers): array
{

    $history_array = [];

    foreach ($draw_numbers as $draw_number) {

        $item = $draw_number[self::DRAW_NUMBER_STR];
        $draw_period = $draw_number[self::DRAW_PERIOD_STR];
        $result = [];
        $sum = array_sum($item);
        $result["b_s"] = $sum >= 176 ? "B" : ($sum == 175 ? "Tie" : "S");
        $result["b_s_no_tie"] = $sum >= 175 ? "B" : "S";
        $result["o_e"] = $sum >= 175 ? "B" : "S";
        $result[self::WINNING_PERIOD_STR] = $draw_period;
        $result[self::WINNING_NUMBER_STR]     = implode(",", $item);
        array_push($history_array, $result);
    }


    return $history_array;
}


public function extra_no_head_tail_no(array $drawNumbers): array
{

    $historyArray = [];
    //$drawNumbers  = array_reverse($drawNumbers);
    foreach ($drawNumbers as $draw_obj) {
        $item = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $extra_ball = $item[count($item) - 1];
        try {
            $head  =  isset($extra_ball[0]) ? $extra_ball[0] : "";
            $tail  =  isset($extra_ball[1]) ? $extra_ball[1] : "";
            array_push($historyArray, [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $item), "Ball_1" => $item[0], "Ball_2" => $item[1], "Ball_3" => $item[2], "Ball_4" => $item[3], "Ball_5" => $item[4], "Ball_6" => $item[5], "Extra_Ball" => $extra_ball, "head" => $head , "tail" => $tail]);
        } catch (\Exception $e) {
            AppLogger::error(LogLevel::Error,$e);
            array_push($historyArray, ["draw_period" => $draw_period, 'winning' => implode(',', $item), "Ball_1" => "", "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" =>  '', "Extra_Ball" => '', "head" => '', "tail" => '']);
        }
    }

    return $historyArray;
} // end of extra_no_head_tail_no(). return Ball 1 ... extra ball,head(first of extra ball),tail(last of extra ball) 


public function five_elements(array $drawNumber): array
{

    

   
    $result = [];
    foreach ($drawNumber as $draw_number) {
        try {
            //code..
            $value       = $draw_number['draw_number'];
            $draw_period = $draw_number['period'];
            
            $value1 = $value[count($value) - 1];
            
            $res = "";
            if (in_array($value1, self::FIVE_ELEMENTS['gold'])) {
                $res = "Gold";
            } elseif (in_array($value1, self::FIVE_ELEMENTS['wood'])) {
                $res = "Wood";
            } elseif (in_array($value1, self::FIVE_ELEMENTS['water'])) {
                $res = "Water";
            } elseif (in_array($value1, self::FIVE_ELEMENTS['fire'])) {
                $res = "Fire";
            } elseif (in_array($value1, self::FIVE_ELEMENTS['earth'])) {
                $res = "Earth";
            }
            array_push($result, [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => $value[0], "Ball_2" => $value[1], "Ball_3" => $value[2], "Ball_4" => $value[3], "Ball_5" => $value[4], "Ball_6" => $value[5], "Extra_Ball" => $value1, "five_elements" => $res]);
        } catch (\Exception $e) {
            AppLoger::error(LogLevel::ERROR,$e);
            array_push($result, [self::WINNING_PERIOD_STR => $draw_period,  self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => "", "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" =>  '', "Extra_Ball" => '', "five_elements" => '']);
        }
    }

    return $result;
} // end of five_elements(). return categories if the last num is in the those category.



function form_extra_no(array $drawNumbers): array
{

    $result = [];
    $value = "";
    $draw_period = "";
    foreach ($drawNumbers as $drawNumber) {

        try {

            $value       = $drawNumber[self::DRAW_NUMBER_STR];
            $draw_period = $drawNumber[self::DRAW_PERIOD_STR];
            $extra_ball =  $value[count($value) - 1];
            $extra_ball = intval($extra_ball);
            $res = [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => $value[0], "Ball_2" => $value[1], "Ball_3" => $value[2], "Ball_4" => $value[3], "Ball_5" => $value[4], "Ball_6" => $value[5], "Extra_Ball" => "{$extra_ball}"];
            $res["b_s"]  = $extra_ball == 49 ? "Tie" : (($extra_ball >= 1 && $extra_ball <= 24) ? "S" : "B");
            $res["o_e"]  = $extra_ball == 49 ? "Tie" : (($extra_ball % 2 === 1) ?  "O" : "E");
        } catch (\Exception $e) {
            AppLogger::error(LogLevel::Error, $e);
            $res = [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "b_s" => '', "o_e" => ''];
        }
        array_push($result, $res);
    }

    return $result;
} // end of form_extra_no(). return whether the extra-ball is big/small or odd/even.


public function form_sum_of_extra_h_and_t(array $drawNumbers): array
{

    $result = [];
    foreach ($drawNumbers as $drawNumber) {
        try {
            //code...
            $value       = $drawNumber[self::DRAW_NUMBER_STR];
            $draw_period = $drawNumber[self::DRAW_PERIOD_STR];
            $extra_ball = $value[count($value) - 1];
            $sum_extra_ball = array_sum(str_split($extra_ball));
            $b_s = intval($extra_ball) == 49 ? "Tie" : (($sum_extra_ball >= 1 && $sum_extra_ball <= 6) ?  "S" : "B");
            $o_e  = ($sum_extra_ball % 2 === 1) ?  "O" : "E";
            $form = ["b" => "Big", "s" => "Small", "o" => "Odd", "e" => "Even","tie"=> "Tie"];
            $result[] = [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR  => implode(',', $value), "Ball_1" => $value[0], "Ball_2" => $value[1], "Ball_3" => $value[2], "Ball_4" => $value[3], "Ball_5" => $value[4], "Ball_6" => $value[5], "Extra_Ball" => $extra_ball, "sum" => $extra_ball, "form" => $form[strtolower($b_s)] . " " . $form[strtolower($o_e)]];
        } catch (\Exception $e) {
            //throw $th;
            AppLogger::error(LogLevel::ERROR,$e);
            $result[] = [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "sum" =>  '', "form" => ''];
        }
    }

    return $result;
} // end of form_sum_of_extra_h_and_t().  return Ball 1 ... Ball 6 & Ball extra,sum, form,


public function form_extra_tail(array $drawNumbers): array
{

    $result = [];
    foreach ($drawNumbers as $drawNumber) {
        try {
            $value       = $drawNumber[self::DRAW_NUMBER_STR];
            $draw_period = $drawNumber[self::DRAW_PERIOD_STR];
            $extra_ball = $value[count($value) - 1];
            $tail = str_split($extra_ball)[1];
            $b_s  = intval($extra_ball) === 49 ? "Tie" :((intval($tail) >= 0 && intval($tail) <= 4) ? "S" : "B");
            $o_e  = (intval($extra_ball) % 2 === 1) ?  "O" : "E";
            $form = ["b" => "Big", "s" => "Small", "o" => "Odd", "e" => "Even","tie" => "Tie"];

            $result[] = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => $value[0], "Ball_2" => $value[1], "Ball_3" => $value[2], "Ball_4" => $value[3], "Ball_5" => $value[4], "Ball_6" => $value[5], "Extra_Ball" => $extra_ball, "tail" => $tail, "form" => $form[strtolower($b_s)] . " " . $form[strtolower($o_e)]];
        } catch (\Throwable $e) {
            AppLogger::error(LogLevel::Error,$e);
            $result[] = [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $value), "Ball_1" => '', "Ball_2" =>  '', "Ball_3" =>  '', "Ball_4" =>  '', "Ball_5" =>  '', "Ball_6" =>  '', "Extra_Ball" =>  '', "tail" =>   '', "form" => ''];
        }
    }

    return $result;
} // end of form_extra_tail(). return Ball 1 ... Ball 6 & Ball extra,last digit of the extra ball(tail), form,

 // ["Ball_1"=>$item[0], "Ball_2"=>$item[1], "Ball_3"=>$item[2], "Ball_4"=>$item[3], "Ball_5"=>$item[4], "Ball_6"=>$item[5], "Extra_Ball"=>$extra_ball, "form_special_zodiac"=>["S_G" => "S", "F_L" => "F", "P_B" => "P"]];
public function form_extra_zodiac(array $drawNumbers): array
{ 

    $zodiacs = self::generate_zodiac_numbers();
    $historyArray = array();

    foreach ($drawNumbers as $drawNumber) {
        $res = [];
        try {
            //code...
            $item        = $drawNumber[self::DRAW_NUMBER_STR];
            $draw_period = $drawNumber[self::DRAW_PERIOD_STR];
            $extra_ball = $item[count($item) - 1];
            $res = [];
            if ($extra_ball === "49") {
                $res["S_G"] = "Tie";
                $res["F_L"] = "Tie";
                $res["P_B"] = "Tie";
                array_push($historyArray, [self::DRAW_PERIOD_STR => $draw_period, self::DRAW_NUMBER_STR => implode(',', $item), "Ball_1" => $item[0], "Ball_2" => $item[1], "Ball_3" => $item[2], "Ball_4" => $item[3], "Ball_5" => $item[4], "Ball_6" => $item[5], "Extra_Ball" => $extra_ball, "form_special_zodiac" => $res]);

                continue;
            }
            if (in_array($extra_ball,$zodiacs['ox']) || in_array($extra_ball,$zodiacs['rabbit']) || in_array($extra_ball,$zodiacs['dragon']) || in_array($extra_ball,$zodiacs['horse']) || in_array($extra_ball,$zodiacs['monkey']) || in_array($extra_ball,$zodiacs['pig']) ) {
                $res["S_G"] =   "S";
            } elseif (in_array($extra_ball,$zodiacs['rat']) || in_array($extra_ball,$zodiacs['tiger']) || in_array($extra_ball,$zodiacs['snake']) || in_array($extra_ball,$zodiacs['goat']) || in_array($extra_ball,$zodiacs['rooster']) || in_array($extra_ball,$zodiacs['dog'])) {
                $res["S_G"] =   "G";
            }

            if (in_array($extra_ball,$zodiacs['rat']) || in_array($extra_ball,$zodiacs['ox']) || in_array($extra_ball,$zodiacs['tiger']) || in_array($extra_ball,$zodiacs['rabbit']) || in_array($extra_ball,$zodiacs['dragon']) || in_array($extra_ball,$zodiacs['snake'])) {
                $res["F_L"] =   "F";
            } elseif (in_array($extra_ball,$zodiacs['horse']) || in_array($extra_ball,$zodiacs['goat']) || in_array($extra_ball,$zodiacs['monkey']) || in_array($extra_ball,$zodiacs['rooster']) || in_array($extra_ball,$zodiacs['dog']) || in_array($extra_ball,$zodiacs['pig'])) {
                $res["F_L"] =   "L";
            }

            if (in_array($extra_ball,$zodiacs['ox']) || in_array($extra_ball,$zodiacs['horse']) || in_array($extra_ball,$zodiacs['goat']) || in_array($extra_ball,$zodiacs['rooster']) || in_array($extra_ball,$zodiacs['dog']) || in_array($extra_ball,$zodiacs['pig'])) {
                $res["P_B"] =   "P";
            } elseif (in_array($extra_ball,haystack: $zodiacs['rat']) || in_array($extra_ball,$zodiacs['tiger']) || in_array($extra_ball,$zodiacs['dragon']) || in_array($extra_ball,$zodiacs['snake']) || in_array($extra_ball,$zodiacs['rabbit']) || in_array($extra_ball,$zodiacs['monkey'])) {

                $res["P_B"] =   "B";
            }

            $res =  [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => $item[0], "Ball_2" => $item[1], "Ball_3" => $item[2], "Ball_4" => $item[3], "Ball_5" => $item[4], "Ball_6" => $item[5], "Extra_Ball" => $extra_ball, "form_special_zodiac" => $res];
        } catch (\Exception $e) {
            //throw $th;
            AppLogger::error(LogLevel::ERROR, $e);
            $res =  [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(',', $item), "Ball_1" => '', "Ball_2" =>  '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "form_special_zodiac" => ''];
        }

        array_push($historyArray, $res);
    }

    return $historyArray;
}

// ["Ball_1"=>$drawNumber[0], "Ball_2"=>$drawNumber[1], "Ball_3"=>$drawNumber[2], "Ball_4"=>$drawNumber[3], "Ball_5"=>$drawNumber[4], "Ball_6"=> $drawNumber[5], "Extra_Ball"=>$extra_ball,"color" => Red/blue/green, "form" =>Small Even/Big Even/Small Odd/Big Odd/Tie ]
public function color_balls(array $drawNumbers, int $lower_limit = 4): array
{


    $historyArray = [];
    foreach ($drawNumbers as $item) {
        $res = [];

        try {
            //code...
            $drawNumber = $item[self::DRAW_NUMBER_STR];
            $draw_period = $item[self::DRAW_PERIOD_STR];

            $extra_ball = $drawNumber[count($drawNumber) - 1];
            $color = "";
            if (in_array($extra_ball, self::ZODIAC_RED_BALLS)) {
                $color = "Red";
            } elseif (in_array($extra_ball, self::ZODIAC_BLUE_BALLS)) {
                $color = "Blue";
            } elseif (in_array($extra_ball, self::ZODIAC_GREEN_BALLS)) {
                $color = "Green";
            }
            $b_s = intval($extra_ball) == 49 ? "Tie" : ((intval($extra_ball) <= $lower_limit) ? "S" : "B");
            $o_e  = (intval($extra_ball) % 2 === 1) ?  "O" : "E";
            $form = ["b" => "Big", "s" => "Small", "o" => "Odd", "e" => "Even","tie" => "Tie"];
            $res = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $extra_ball, "color" => $color, "form"   => $form[strtolower($b_s)] . " " . $form[strtolower($o_e)]];
        } catch (\Throwable $th) {
            //throw $th;
            $res = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "color" => '', "form" => ''];
        }

        array_push($historyArray, $res);
    }

    return $historyArray;
}


public function one_zodiac_color_balls(array $drawNumbers): array
{

   

    $historyArray = [];
    foreach ($drawNumbers as  $item) {
        $drawNumber = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $extra_ball = $drawNumber[count($drawNumber) - 1];
        $color = "";
        if (in_array($extra_ball, self::ZODIAC_RED_BALLS)) {
            $color = "red";
        } elseif (in_array($extra_ball, self::ZODIAC_BLUE_BALLS)) {
            $color = "blue";
        } elseif (in_array($extra_ball, self::ZODIAC_GREEN_BALLS)) {
            $color = "green";
        }

        array_push($historyArray, ["draw_period" => $draw_period, "Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $extra_ball, "color" => $color]);
    }

    return $historyArray;
}


public function sum_extra_n_ball_no(array $drawNumbers): array
{


    $historyArray = [];

    foreach ($drawNumbers as $key => $draw_number) {
        try {
            $value       = $draw_number[self::DRAW_NUMBER_STR];
            $draw_period = $draw_number[self::DRAW_PERIOD_STR];

            $sum  = array_sum($value);
            $b_s  = $sum  == 175 ? "Tie": (($sum < 175) ? "S" : "B");
            $o_e  = ($sum % 2 === 1) ?  "O" : "E";
            $form = ["b" => "Big", "s" => "Small", "o" => "Odd", "e" => "Even","tie" => "Tie"];

            $historyArray[] = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => $value[0], "Ball_2" => $value[1], "Ball_3" => $value[2], "Ball_4" => $value[3], "Ball_5" => $value[4], "Ball_6" => $value[5], "Extra_Ball" => $value[6], "sum" => $sum, "form" =>  $form[strtolower($b_s)] . " " . $form[strtolower($o_e)]];
        } catch (\Exception $e) {
            AppLogger::error(LogLevel::Error,$e);
            $historyArray[] = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" => '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "sum" => '', "form" => ''];
        }
    }
    return $historyArray;
}


public function two_consec_tail(array $drawNumbers): array
{


    $historyArray = [];

    foreach ($drawNumbers as $item) {
        $res = [];
        try {
            $drawNumber  = $item[self::DRAW_NUMBER_STR];
            $draw_period = $item[self::DRAW_PERIOD_STR];

            $res = self::get_tail($drawNumber);

            $historyArray[] = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $drawNumber[6], "tail" => $res];
        } catch (\Exception $e) {
            AppLogger::error(LogLevel::ERROR,$e);
            $historyArray[] = [self::WINNING_PERIOD_STR => $draw_period, "Ball_1" => '', "Ball_2" => '', "Ball_3" => '', "Ball_4" =>  '', "Ball_5" => '', "Ball_6" => '', "Extra_Ball" => '', "tail" => ''];
        }
    }
    return $historyArray;
}


public function sum_zodiac(Array $drawNumbers) : array {
   

    $historyArray = [];
    $zodiacs = self::generate_zodiac_numbers();
    $draw_period = "";
    try{

    foreach ($drawNumbers as $item) {
        $res = [];
        $drawNumber  = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        foreach ($drawNumber as   $single_draw) {
            foreach ($zodiacs as $key => $value) {
                if(in_array($single_draw,$value)){
                     $res[] = $key;
                }
           }
        }

        $tail = self::get_tail($drawNumber);
        $sum  =  array_sum(array_map("intval", $drawNumber));
        $unique_zodiacs = count(array_unique($res));
        // $historyArray[] = [self::WINNING_NUMBER_STR => $draw_period ,"Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $drawNumber[6],"tail" => $tail,"no" => $unique_zodiacs, "form"=> $unique_zodiacs % 2 === 0 ? "Even" : "Odd"];
        $historyArray[] = ["draw_period" => $draw_period, "Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $drawNumber[6], "no" => $unique_zodiacs,'form' => $unique_zodiacs % 2 === 0 ? "Even" : "Odd", "o_e" => $unique_zodiacs % 2 === 0 ? "E" : "O","tail" => $tail , 'sum_b_s' => self::b_s_with_tie($sum),'sum_b_s_no_tie' => self::b_s_with_tie($sum,174,175),"sum_o_e" => $sum % 2 === 0 ? "E" : "O",];
    }
    return $historyArray;

    }catch(\Exception $e){
        AppLogger::error(LogLevel::Error,$e);
        return [self::WINNING_NUMBER_STR => $draw_period ,"Ball_1" => "", "Ball_2" => "", "Ball_3" => "", "Ball_4" => "", "Ball_5" => "", "Ball_6" => "", "Extra_Ball" => "","no" => "", "form"=> ""];
     }
}


// ["Ball_1"=>$drawNumber[0], "Ball_2"=>$drawNumber[1], "Ball_3"=>$drawNumber[2], "Ball_4"=>$drawNumber[3], "Ball_5"=>$drawNumber[4], "Ball_6"=> $drawNumber[5], "Extra_Ball"=>$extra_ball, "color" => red/blue/green/tie]
public function extra_n_ball_color(Array $drawNumbers) : array{
   
    $history_array = [];
    $color_balls_groups = [
    "Red"   => self::ZODIAC_RED_BALLS,
    "Blue"  => self::ZODIAC_BLUE_BALLS,
    "Green" => self::ZODIAC_GREEN_BALLS
    ];

    $balls_keys = array_keys($color_balls_groups);
    
    try{
    foreach ($drawNumbers as $item) {
        $res = [];
 
        $drawNumber  = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        foreach ($drawNumber as $key =>  $single_draw_number) {
          
          for($i = 0;$i < count($color_balls_groups); $i++) {

                if(in_array($single_draw_number,$color_balls_groups[$balls_keys[$i]])){

                    $res[$balls_keys[$i]] = isset($res[$balls_keys[$i]]) ?
                      (string)(($key === count($drawNumber) - 1) ?
                      intval($res[$balls_keys[$i]]) + 1.5 : intval($res[$balls_keys[$i]]) + 1) : 
                      (string)(($key === count($drawNumber) - 1) ? 1.5 : 1);

                      break;
                }
        }
    }
    asort($res);
    $flipped_array = array_flip($res);
    $max_colored_ball_num = array_pop($res);
    $history_array[] = [self::WINNING_NUMBER_STR => $draw_period,"Ball_1" => $drawNumber[0], "Ball_2" => $drawNumber[1], "Ball_3" => $drawNumber[2], "Ball_4" => $drawNumber[3], "Ball_5" => $drawNumber[4], "Ball_6" => $drawNumber[5], "Extra_Ball" => $drawNumber[6],"Color" => !in_array($max_colored_ball_num,$res) ?  $flipped_array["$max_colored_ball_num"] :"Tie"];
    }

    return $history_array;

    }catch(\Exception $e){
        AppLogger::error(LogLevel::Error,$e);
        return [self::WINNING_NUMBER_STR => $draw_period ,"Ball_1" => "", "Ball_2" => "", "Ball_3" => "", "Ball_4" => "", "Ball_5" => "", "Ball_6" => "", "Extra_Ball" => "","no" => "", "form"=> ""];
    }

}

public function board_game( Array $draw_numbers){

    $history_array = [];
      $draw_period ="";
    try{

    
    foreach($draw_numbers as $item){
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $extra_ball = $draw_number[count($draw_number) - 1];
        
        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period,"Ball_1" => $draw_number[0], "Ball_2" => $draw_number[1], "Ball_3" => $draw_number[2], "Ball_4" => $draw_number[3], "Ball_5" => $draw_number[4], "Ball_6" => $draw_number[5], "Extra_Ball" => $draw_number[6],"b_s" =>  $extra_ball <= 24  ? 'Small' : 'big' , 'o_e' => ($extra_ball % 2 == 0)  ? 'Pair' : 'One','sum' => $extra_ball]);
    }
    return $history_array;
    }catch(\Exception $e){
        AppLogger::error(LogLevel::ERROR, $e);
        return [self::WINNING_PERIOD_STR => $draw_period ,"Ball_1" => "", "Ball_2" => "", "Ball_3" => "", "Ball_4" => "", "Ball_5" => "", "Ball_6" => "", "Extra_Ball" => "","no" => "", "form"=> ""];
    }
 
 }

// Odd_Even Big_Small
public function std(array $drawNumber): array{
    $result = [
       
        'extra_no'              => ["extra_no"     =>    $this->winning_number_mark6(drawNumbers: $drawNumber), "head_tail_no" => $this->extra_no_head_tail_no($drawNumber)],
        'special_zodiac'        => ["combo_zodiac" =>    $this->winning_number_mark6($drawNumber), "special_zodiac" => $this->winning_number_mark6($drawNumber), "five_elements" => $this->five_elements($drawNumber), "form_extra_no" => $this->form_extra_no($drawNumber), "form_sum_of_extra_h_and_t" => $this->form_sum_of_extra_h_and_t($drawNumber), "form_extra_tail" => $this->form_extra_tail($drawNumber), "form_extra_zodiac"     => $this->form_extra_zodiac($drawNumber)],
        'color'                 => $this->color_balls($drawNumber,24),
        'ball_no'               => $this->winning_number_mark6($drawNumber),
        'one_zodiac'            => $this->winning_number_mark6($drawNumber),
        'ball_color'            => $this->winning_number_mark6($drawNumber),
        "extra_n_ball_no"       => ["sum" => $this->sum_extra_n_ball_no($drawNumber), "tail_no" => $this->winning_number_mark6($drawNumber), "mismatch" => $this->winning_number_mark6($drawNumber), "two_consec_tail" => $this->two_consec_tail($drawNumber), "three_consec_tail" => $this->two_consec_tail($drawNumber), "four_consec_tail" => $this->two_consec_tail($drawNumber), "five_consec_tail" => $this->two_consec_tail($drawNumber), 'two_no' =>  $this->winning_number_mark6($drawNumber),  'win_extra_no' =>  $this->winning_number_mark6($drawNumber)],
        'extra_n_ball_zodiac'   => ["one_consec_zodiac" =>  $this->winning_number_mark6($drawNumber), "two_consec_zodiac" =>  $this->winning_number_mark6($drawNumber), "three_consec_zodiac" =>  $this->winning_number_mark6($drawNumber), "four_consec_zodiac" =>  $this->winning_number_mark6($drawNumber), "five_consec_zodiac" =>  $this->winning_number_mark6($drawNumber), "sum_zodiac" => $this->sum_zodiac($drawNumber), "o_e_sum_zodiac" => $this->sum_zodiac($drawNumber)],
        'extra_n_ball_color'    => $this->extra_n_ball_color($drawNumber),
    ];

    return $result;
}

// Odd_Even Big_Small
public function two_sides(array $drawNumber): array
{

     return [
        'conv'                 => $this->winning_number_mark6($drawNumber),
        'extra_no_2_sides'     => ["two_sides" => $this->form_extra_no($drawNumber), "no" => $this->winning_number_mark6($drawNumber), "all_color" => $this->color_balls($drawNumber, 24), "special_zodiac_h_t" => $this->extra_no_head_tail_no($drawNumber), "combo_zodiac" => $this->winning_number_mark6($drawNumber), "five_elements" =>  $this->five_elements($drawNumber)],
        'ball_no_2_sides'      => ["pick_1_ball_no" => $this->winning_number_mark6($drawNumber), "ball_no_1_1" => $this->winning_number_mark6($drawNumber), "one_zodiac_color_balls" => $this->extra_n_ball_color($drawNumber)],
        'specific_no'          => ["fixed_place_ball_1" =>  $this->winning_number_mark6($drawNumber), "fixed_place_ball_2" => $this->winning_number_mark6($drawNumber), "fixed_place_ball_3" => $this->winning_number_mark6($drawNumber), "fixed_place_ball_4" => $this->winning_number_mark6($drawNumber), "fixed_place_ball_5" => $this->winning_number_mark6($drawNumber), "fixed_place_ball_6" => $this->winning_number_mark6($drawNumber)],
        'row_zodiac_row_tail'  => ["two_consec_zodiac" =>   $this->winning_number_mark6($drawNumber), "three_consec_zodiac" => $this->winning_number_mark6($drawNumber), "four_consec_zodiac" => $this->winning_number_mark6($drawNumber), "five_consec_zodiac" => $this->winning_number_mark6($drawNumber), "second_consec_tail_no" => $this->two_consec_tail($drawNumber), "third_consec_tail_no" => $this->two_consec_tail($drawNumber), "fourth_consec_tail_no" => $this->two_consec_tail($drawNumber), "five_consec_tail_no" => $this->two_consec_tail($drawNumber)],
        "row_no"               => ["win_2_3" =>       $this->winning_number_mark6($drawNumber), "win_3_3" => $this->winning_number_mark6($drawNumber), "win_2_2" =>      $this->winning_number_mark6($drawNumber), "two_no" =>  $this->winning_number_mark6($drawNumber), "win_extra_no" => $this->winning_number_mark6($drawNumber), "win_4_4" => $this->winning_number_mark6($drawNumber)],
        "zodiac_and_tail"      => $this->sum_zodiac($drawNumber),
        "sum"                  => $this->sum_zodiac($drawNumber),
        "optional"             => $this->winning_number_mark6($drawNumber),
        "mismatch"             => $this->winning_number_mark6($drawNumber),
    ];
}

// Odd_Even Big_Small
public function board_games(array $drawNumber): array
{
    return  self::board_game($drawNumber);
}

public function fantan(array $draw_data): array {
    $zodiacs = self::generate_zodiac_numbers();
    $final_res = [];
    foreach($draw_data as $data){
    $draw_number = $data['draw_number'];
    $draw_period = $data['period'];
  
    $extra_number = end($draw_number);
    $zodiac = "";
    switch($extra_number){
        case in_array($extra_number,$zodiacs["rat"]):
            $zodiac = "Rat";
        break;
        case in_array($extra_number,$zodiacs["ox"]):
            $zodiac = "Ox";
        break;
        case in_array($extra_number,$zodiacs["tiger"]):
            $zodiac = "Tiger";
        break;
        case in_array($extra_number,$zodiacs["rabbit"]):
            $zodiac = "Rabbit";
        break;
        case in_array($extra_number,$zodiacs["dragon"]):
            $zodiac = "Dragon";
        break;
        case in_array($extra_number,$zodiacs["snake"]):
            $zodiac = "Snake";
        break;
        case in_array($extra_number,$zodiacs["goat"]):
            $zodiac = "Goat";
        break;
        case in_array($extra_number,$zodiacs["monkey"]):
            $zodiac = "Monkey";
        break;
        case in_array($extra_number,$zodiacs["rooster"]):
            $zodiac = "Rooster";
        break;
        case in_array($extra_number,$zodiacs["dog"]):
            $zodiac = "Dog";
        break;
        case in_array($extra_number,$zodiacs["pig"]):
            $zodiac = "Pig";
        break;
    }

    //!UNCOMMENT ON FOR FRONTEND COMPARTIBILITY
    // $res['draw_number'] = $draw_number;
    $res['draw_number'] = implode(',',$draw_number);
    $res['draw_period'] = $draw_period;
    $res['extra_number'] = $extra_number;

    $draw_number      = array_map('intval', $draw_number);
   
    $res['zodiacs'] = $zodiac;
    $res['big_small'] =  self::bigSmall($extra_number, 25, 48, 1, 24);
    $res['odd_even']  =  $extra_number % 2 == 0 ? "Even" : "Odd";
    $final_res[] = $res;
 }
        
 return $final_res;


}


















public static function generate_zodiac_numbers(): array{
     $zodiacs = [];
     for ($i=1; $i < 13; $i++) { 
    # code...
    $zodiacs[self::ZODIAC_NAMES[$i - 1]] = self::generateArray($i);
   }
   return $zodiacs;
}

public static function generateArray($position)
{
    $current_chinese_zodiac = 5;
    $sequenceMappingData = self::generateMapping($current_chinese_zodiac);
    $finalResults = [];
    $maxArrayLoop = $sequenceMappingData[$position] === 1 ? 5 : 4;

    for ($i = 1; $i <= $maxArrayLoop; $i++) {
        $number = 12 * $i - (12 - $sequenceMappingData[$position]);
        $formattedNumber = $number < 10 ? "0$number" : "$number";
        $finalResults[] = $formattedNumber;
    }

    return $finalResults;
}


public static function generateMapping($start)
{
    $sequence = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    $mapping = [];
    $length = count($sequence);
    $distance = 0;
    $index = $start;

    for ($i = 0; $i <= $length; $i++) {
        $mapping[$sequence[$index]] =  $distance;
        $distance++;
        $index = $index === 0 ? $length - 1 : $index - 1;
    }

    return $mapping;
}

public static function get_tail($drawNumber){
    $res = [];
    foreach ($drawNumber as $value) {
        $last_digit = str_split($value)[1];
        $res[] = intval($last_digit);
    }

    sort($res);
    return implode(",", array_unique($res));
}

}