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
class Class3D extends BaseClass{
    


    
public function all3History(array $drawNumbers): array
{

    $group3 = 1;
    $group6 = 1;

    $historyArray = [];
    $drawNumbers = array_reverse($drawNumbers);
    foreach ($drawNumbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        $group3Key =  "group3";
        $group6Key =  "group6";

        $startingIndex = 0;
        $endIndex      = 3;


        $group3Condition = self::findPattern([2, 1], $draw_number, $startingIndex, $endIndex) ? "group3" : $group3;
        $group6Condition = self::findPattern([1, 1, 1], $draw_number, $startingIndex, $endIndex) ? "group6" : $group6;

        $mydata = [
             self::WINNING_PERIOD_STR => $draw_period,
             self::WINNING_NUMBER_STR => implode(",", $draw_number),
            "sum" => self::sumPattern($draw_number, $startingIndex, $endIndex),
            "span" => self::spanPattern($draw_number,  $startingIndex, $endIndex),
            $group3Key => $group3Condition,
            $group6Key => $group6Condition,
        ];
        
        array_push($historyArray, $mydata);
        $currentPattern = array_values($mydata);
        sort($currentPattern);
        $currentPattern = $currentPattern[5];

        $group6 = $currentPattern == "group6" ? 1 : ($group6 += 1);
        $group3 = $currentPattern == "group3" ? 1 : ($group3 += 1);
    }

    return array_reverse($historyArray);
} // end of all3History. Return the groups[No repitition(group6),pair(group3)]



public function all2History(array $drawNumbers, String $typeOfModule): array
{

    $historyArray = [];

    foreach ($drawNumbers as $item) {

        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        // Assuming sumPattern and spanPattern functions are defined in PHP
        $objectKeyPrefix = str_replace("all2", "", $typeOfModule);
        $startIndex = $typeOfModule === "all2first2" ? 0 : 1;
        $length = $typeOfModule === "all2first2" ? 2 : 3;

        $mydata = array(
            self::WINNING_NUMBER_STR => implode(",", $draw_number),
            self::WINNING_PERIOD_STR => $draw_period,
            $objectKeyPrefix . "sum" => self::sumPattern($draw_number, $startIndex, $length),
            $objectKeyPrefix . "span" =>self::spanPattern($draw_number, $startIndex, $length)
        );

        array_push($historyArray, $mydata);
    }

    return $historyArray;
} // end of all2history. first2:["wining"=>"0,1,3", "sum"=>1, "span"=>1], first2:["wining"=>"0,1,3", "sum"=>1, "span"=>1 ]


public function winning_number(Array $draw_numbers)  : array { 
   
    $results = [];
    foreach ($draw_numbers as $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($results,[self::WINNING_NUMBER_STR=> $draw_period,self::WINNING_PERIOD_STR => implode(",",$draw_number)]); 
    }

    return $results;

}// end of winning_number(): return the wnning number:format ["winning"=>"1,2,3,4,5"]



public function dragonTigerHistory3d(array $drawNumbers): array
{
    $historyArray = [];
    foreach ($drawNumbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        // Assuming dragonTigerTiePattern is a function you have defined in PHP
        $mydata = [
            self::WINNING_NUMBER_STR => implode(",", $draw_number),
            self::WINNING_PERIOD_STR => $draw_period,
            'onex2' => self::dragonTigerTiePattern(0, 1, $draw_number),
            'onex3' => self::dragonTigerTiePattern(0, 2, $draw_number),
            'twox3' => self::dragonTigerTiePattern(1, 2, $draw_number),
        ];
        array_push($historyArray, $mydata);
    }

    return $historyArray;
} // end of dragonTigerHistory3d.

public function conv(array $draw_numbers): array
{


    $history_array = [];

    foreach ($draw_numbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        $history = [
            self::WINNING_PERIOD_STR => $draw_period,
            self::WINNING_NUMBER_STR => implode(",", $draw_number),
            "first"  =>   self::determinePattern($draw_number[0], 4),
            "second" =>   self::determinePattern($draw_number[1], 4),
            "third"  =>   self::determinePattern($draw_number[2], 4),
            "sum"    =>    self::determinePattern(array_sum($draw_number), 13, true),
        ];
        array_push($history_array, $history);
    }

    return  $history_array;
}    


public function sum_of_two_no(array $draw_numbers): array
{

    $history_array = [];

    foreach ($draw_numbers as $item) {
        $value = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        array_push($history_array, [
            self::WINNING_PERIOD_STR => $draw_period,
            self::WINNING_NUMBER_STR => implode(",", $value),
            "sum_fst_snd" => intval($value[0]) + intval($value[1]),
            "sum_fst_thd" => intval($value[0]) + intval($value[2]),
            "sum_snd_thd" => intval($value[1]) + intval($value[2]),
            "sum" => array_sum($value)
        ]);
    }
    return $history_array;
}

public function all3TwoSidesHistory(array $drawNumbers): array
{
    $group3 = 1;
    $group6 = 1;
    $historyArray = [];
    $drawNumbers = array_reverse($drawNumbers);
    foreach ($drawNumbers as $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];
        $group3Key =  "group3";
        $group6Key =  "group6";
        $startingIndex = 0;
        $endIndex      = 3;
        $group3Condition = self::findPattern([2, 1], $draw_number, 0, 3) ? "group3" : $group3;
        $group6Condition = self::findPattern([1, 1, 1], $draw_number, 0, 3) ? "group6" : $group6;
        $mydata = [
            self::WINNING_PERIOD_STR => $draw_period,
            self::WINNING_NUMBER_STR => implode(",", $draw_number),
            "span" => self::spanPattern($draw_number,  $startingIndex, $endIndex),
            $group3Key => $group3Condition,
            $group6Key => $group6Condition,
        ];
        array_push($historyArray, $mydata);
        $currentPattern = array_values($mydata);
        sort($currentPattern);
        $currentPattern = $currentPattern[3];
        $group6 = $currentPattern == "group6" ? 1 : $group6 + 1;
        $group3 = $currentPattern == "group3" ? 1 : $group3 + 1;
    }

    return array_reverse($historyArray);
} //

public function std(array $drawNumber): array
{
    return [
        'all3'                   => $this->all3History($drawNumber),
        'all2'                   => ["first2" => $this->all2History($drawNumber, "all2first2"), "last2" => $this->all2History($drawNumber, "all2last2")],
        'fixedplace'             => $this->winning_number($drawNumber),
        'anyplace'               => $this->winning_number($drawNumber),
        'dragonTiger'            => $this->dragonTigerHistory3d($drawNumber),
    ];
} // end of render method. returns all the history for 3D.


public function two_sides(array $drawNumber): array
{
   return [
        'conv'             => $this->conv($drawNumber),
        'two_sides'        => $this->conv($drawNumber),
        'one_no_combo'     => $this->winning_number($drawNumber),
        'two_no_combo'     => $this->winning_number($drawNumber),
        'three_no_combo'   => $this->winning_number($drawNumber),
        'fixed_place_2_no' => $this->winning_number($drawNumber),
        'fixed_place_3_no' => $this->winning_number($drawNumber),
        'sum_of_2_no'      => $this->sum_of_two_no($drawNumber),
        'group3'           => $this->all3TwoSidesHistory($drawNumber),
        'group6'           => $this->all3TwoSidesHistory($drawNumber),
        'span'             => $this->all3TwoSidesHistory($drawNumber),
    ];
} // end of render method. returns all the history for 3D.

public function board_game(Array $draw_numbers,$lower_limit = 22){

    $history_array = [];

    foreach($draw_numbers as $draw_obj){
        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        
        $dragon_tiger = (intval($draw_number[0]) > intval($draw_number[4])) ? "Dragon" : "Tiger";
        //TODO: this is the real solution, but using the one below for demo for Mr. Eben.
        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period,self::DRAW_NUMBER_STR => implode(",",$draw_number),'sum' => $sum,"b_s" =>  $sum <= $lower_limit ? 'Small' : 'Big', 'o_e' => ($sum % 2 == 0)  ? 'Even' : 'Odd', 'dragon_tiger' => $dragon_tiger, 'stud' => studHistory_11x5($draw_number,$draw_period),'three_cards' => threeCardsHistory_11x5($draw_number)]);
        // array_push($history_array, ["draw_period" => $draw_period,"winning"=>implode(",",$draw_number),'sum' => $sum ]);
    }
return $history_array;

}

public function board_games(array $drawNumber): array {return self::board_game($drawNumber);} // end of render method. returns all the history for 3D.


}