<?php

namespace App\Classes;



require_once('C:/xampp/htdocs/history/vendor/autoload.php');


// import the base class
use App\Classes\BaseClass;



// create a class for 5D
class Class5D extends BaseClass{


     protected $lottery_id ;
     protected $game_group ;
     protected $name ;



public function __construct(object $game_type){
    $this->lottery_id = $game_type->lottery_id;
    $this->game_group    = $game_type->game_group;
    $this->name          = $game_type->name;
}

public function all5History(array $drawNumbers): array {
$patterns = ['g120' => [1, 1, 1, 1, 1], 'g60' => [2, 1, 1, 1], 'g30' => [2, 2, 1], 'g20' => [3, 1, 1], 'g10' => [3, 2], 'g5' => [4, 1]];
$counts = array_fill_keys(array_keys($patterns), 1);
$historyArray = [];
$drawNumbers  = array_reverse($drawNumbers);
foreach ($drawNumbers as  $item) {
    $mydata = [];
    foreach ($patterns as $patternKey => $pattern) {
        $mydata[$patternKey] = self::findPattern($pattern, $item[self::DRAW_NUMBER_STR], 0, 5) ? $patternKey : $counts[$patternKey];
        $counts[$patternKey] = ($mydata[$patternKey] === $patternKey) ? 1 : ($counts[$patternKey] + 1);
    }
    $mydata[self::WINNING_NUMBER_STR] = implode(",", $item[self::DRAW_NUMBER_STR]);
    $mydata[self::WINNING_PERIOD_STR]  =  $item[self::DRAW_PERIOD_STR];
    array_push($historyArray, $mydata);
}

return array_reverse($historyArray);
} // end of all5History: ["g120"..."g5"]


        
public function all4History(Array $drawNumbers,String $isFirst) : Array{
    $group24 = 1;
    $group12 = 1;
    $group6 = 1;
    $group4 = 1;

    $historyArray = array();
    $drawNumbers = array_reverse($drawNumbers);
    foreach ($drawNumbers as $draw_obj) {
        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $mydata = array(
            'group24' => self::findPattern(array(1, 1, 1, 1), $draw_number, $isFirst == "all4first4" ? 0 : -4, 4) ? "group24" : $group24,
            'group12' => self::findPattern(array(2, 1, 1), $draw_number,    $isFirst == "all4first4" ? 0 : -4, 4) ? "group12" : $group12,
            'group6' =>  self::findPattern(array(2, 2), $draw_number,        $isFirst == "all4first4" ? 0 : -4, 4) ? "group6" : $group6,
            'group4' =>  self::findPattern(array(3, 1), $draw_number,        $isFirst == "all4first4" ? 0 : -4, 4) ? "group4" : $group4,
        );

        $mydata[self::WINNING_NUMBER_STR] = implode(",", $draw_number);
        $mydata[self::WINNING_PERIOD_STR] = $draw_period;
        $draw_number   = array_slice($draw_number, $isFirst == "all4first4" ? 0 : -4, 4);
        $mydata['dup'] = count(array_unique($draw_number)) !== count($draw_number) ? self::findDuplicates($draw_number) : '';

        array_push($historyArray, $mydata);
        $currentPattern = array_values($mydata);
        sort($currentPattern);
        $currentPattern = $currentPattern[5];
        $group24 = $currentPattern == "group24" ? 1 : $group24 += 1;
        $group12 = $currentPattern == "group12" ? 1 : $group12 += 1;
        $group6  = $currentPattern == "group6" ? 1  : $group6 += 1;
        $group4  = $currentPattern == "group4" ? 1  : $group4 += 1;
    }

    return array_reverse($historyArray);
}// end of all4History: ["g120"..."g5"]


public function all3History5d(array $drawNumbers, String $typeOf3): array {

$group3 = 1;
$group6 = 1;
$historyArray = [];
$drawNumbers = array_reverse($drawNumbers);
foreach ($drawNumbers as  $draw_obj) {

    $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
    $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
    $objectKeyPrefix = str_replace("all3", "", $typeOf3);
    $group3Key = $objectKeyPrefix . "group3";
    $group6Key = $objectKeyPrefix . "group6";
    $startingIndex = $typeOf3 === "all3first3" ? 0 : ($typeOf3 === "all3mid3" ? 1 : 2);
    $endIndex      = $typeOf3 === "all3first3" ? 3 : ($typeOf3 === "all3mid3" ? 3 : 3);

    $group3Condition = self::findPattern([2, 1], $draw_number, $startingIndex, $endIndex) ? "group3" : $group3;
    $group6Condition = self::findPattern([1, 1, 1], $draw_number, $startingIndex, $endIndex) ? "group6" : $group6;
    $sum = self::sumPattern($draw_number, $startingIndex, $endIndex);

    $mydata = [
        $objectKeyPrefix . "sum" => self::sumPattern($draw_number, $startingIndex, $endIndex),
        $objectKeyPrefix . "span" => self::spanPattern5d($draw_number,  $startingIndex, $endIndex),
        $group3Key => $group3Condition,
        $group6Key => $group6Condition,
    ];

    $splitted_sum = str_split("$sum");
    $mydata["sum_tails"] = count($splitted_sum) == 1 ? $sum : intval($splitted_sum[1]);
    $mydata[self::WINNING_NUMBER_STR] = implode(",", $draw_number);
    $mydata[self::WINNING_PERIOD_STR] = $draw_period;
    $draw_number   = array_slice($draw_number, $startingIndex, $endIndex);
    $mydata['dup'] = count(array_unique($draw_number)) !== count($draw_number) ? self::findDuplicates($draw_number) : '';
    
    array_push($historyArray, $mydata);
    $currentPattern = array_values($mydata);
    sort($currentPattern);

    $currentPattern = $currentPattern[6];
    $group6 = $currentPattern == "group6" ? 1 : ($group6 += 1);
    $group3 = $currentPattern == "group3" ? 1 : ($group3 += 1);
}

return array_reverse($historyArray);
} // end of all3History5d: ["group6"..."group3"]


    
public function all2History5d(array $drawNumbers, String $typeOfModule): array {

    $historyArray = [];
    $drawNumbers = array_reverse($drawNumbers);
    foreach ($drawNumbers as  $draw_obj) {
        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $objectKeyPrefix = str_replace("all2", "", $typeOfModule);
        $startIndex = $typeOfModule === "all2first2" ? 0 : 3;
        $length = $typeOfModule === "all2first2" ? 2 : 4;
        $sum = self::sumPattern($draw_number, $startIndex, $length);
        $mydata = array(
            $objectKeyPrefix . "sum" => self::sumPattern($draw_number, $startIndex, $length),
            $objectKeyPrefix . "span" => self::spanPattern5d($draw_number, $startIndex, $length)
        );
        $splitted_sum = str_split("$sum");
        $mydata["sum_tails"] = count($splitted_sum) == 1 ? $sum : intval($splitted_sum[1]);
        $mydata[self::WINNING_NUMBER_STR] = implode(",", $draw_number);
        $mydata[self::WINNING_PERIOD_STR] = $draw_period;
        $draw_number   = array_slice($draw_number, $startIndex, $length);
        $mydata['dup'] = count(array_unique($draw_number)) !== count($draw_number) ? self::findDuplicates($draw_number) : '';
        array_push($historyArray, $mydata);
    }
    return array_reverse($historyArray);
    } // end of all2History5d: ["sum"..."span"]

public function bsoeHistory($drawNumbers, $typeOfModule){

    $historyArray = array();

    foreach ($drawNumbers as  $item) {
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];


        $results = "";

        switch ($typeOfModule) {
            case "bsoefirst2":
                $results = self::bigSmallOddEvenPattern($draw_number, 0, 2, 0, 1) ?? "";
                break;
            case "bsoefirst3":
                $results = self::bigSmallOddEvenPattern3($draw_number, 0, 3, 0, 1, 2) ?? "";
                break;
            case "bsoelast2":
                $results = self::bigSmallOddEvenPattern($draw_number, 3, 5, 0, 1) ?? "";
                break;
            case "bsoelast3":
                $results = self::bigSmallOddEvenPattern3($draw_number, 2, 5, 0, 1, 2) ?? "";
                break;
            case "bsoesumofall3":
                $results = array_merge(self::sumAndFindPattern($draw_number, 0, 3, array(14, 13), "first3") ?? "", self::sumAndFindPattern($draw_number, 1, 3, array(14, 13), "mid3") ?? "", self::sumAndFindPattern($draw_number, 2, 5, array(14, 13), "last3") ?? "");
                break;
            case "bsoesumofall5":
                $results = self::sumAndFindPattern1($draw_number, 0, 5, array(23, 22)) ?? "";
                break;
        }

        $mydata[self::WINNING_NUMBER_STR] = implode(",", $draw_number);
        $mydata[self::WINNING_PERIOD_STR] = $draw_period;
        if ($typeOfModule !== "bsoefirst2" && $typeOfModule !== "bsoefirst3" && $typeOfModule !== "bsoelast2" && $typeOfModule !== "bsoelast3" && $typeOfModule !== "bsoesumofall5" && $typeOfModule !== "bsoesumofall3") {
            $mydata[] =  $results;
        } else {
            $mydata  =  array_merge($mydata, $results);
        }


        array_push($historyArray, $mydata);

    }

    return $historyArray;
}


public function dragonTigerHistory(array $drawNumbers): array
{
     $historyArray = [];


    foreach ($drawNumbers as  $item) {

        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        // Assuming dragonTigerTiePattern is a function you have defined in PHP
        $mydata = [
            'onex2' =>   self::dragonTigerTiePattern(0, 1, $draw_number),
            'onex3' =>   self::dragonTigerTiePattern(0, 2, $draw_number),
            'onex4' =>   self::dragonTigerTiePattern(0, 3, $draw_number),
            'onex5' =>   self::dragonTigerTiePattern(0, 4, $draw_number),
            'twox3' =>   self::dragonTigerTiePattern(1, 2, $draw_number),
            'twox4' =>   self::dragonTigerTiePattern(1, 3, $draw_number),
            'twox5' =>   self::dragonTigerTiePattern(1, 4, $draw_number),
            'threex4' => self::dragonTigerTiePattern(2, 3, $draw_number),
            'threex5' => self::dragonTigerTiePattern(2, 4, $draw_number),
            'fourx5' =>  self::dragonTigerTiePattern(3, 4, $draw_number)
        ];

        $mydata[self::WINNING_NUMBER_STR] = implode(",", $draw_number);
        $mydata[self::WINNING_PERIOD_STR] = $draw_period;

        array_push($historyArray, $mydata);
    }

    return $historyArray;
}



public function winning_number5d(array $draw_numbers): array{

    $results = [];
    foreach ($draw_numbers as  $value) {
        $draw_number = $value[self::DRAW_NUMBER_STR];
        $draw_period = $value[self::DRAW_PERIOD_STR];
        array_push($results, [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR => implode(",", $draw_number)]);
    }

    return $results;
}

public function studHistory(array $drawNumbers): array {
    $historyArray = [];

    $highCard     = 1;
    $onePair      = 1;
    $twoPair      = 1;
    $threeofakind = 1;
    $fourofakind  = 1;
    $streak       = 1;
    $gourd        = 1;


    $drawNumbers = array_reverse($drawNumbers);

    foreach ($drawNumbers as  $item) {
        $draw_number = $item['draw_number'];
        $draw_period = $item['period'];

        // Assuming findPattern and findStreakPattern5D are defined in PHP
        $mydata = array(
            'highcard'      => self::findPattern(array(1, 1, 1, 1, 1), $draw_number, 0, 5) && self::findStreakPattern5D($draw_number, 0, 5, 4) == false ? "High Card" : $highCard,
            'onepair'       => self::findPattern(array(2, 1, 1, 1), $draw_number, 0, 5) ? "One Pair" : $onePair,
            'twopair'       => self::findPattern(array(2, 2, 1), $draw_number, 0, 5) ? "Two Pair" : $twoPair,
            'threeofakind'  => self::findPattern(array(3, 1, 1), $draw_number, 0, 5) ? "Three of a Kind" : $threeofakind,
            'fourofakind'   => self::findPattern(array(4, 1), $draw_number, 0, 5) ? "Four of A Kind" : $fourofakind,
            'streak'        => self::findStreakPattern5D($draw_number, 0, 5, 4) ? "Streak" : $streak,
            'gourd'         => self::findPattern(array(3, 2), $draw_number, 0, 5) ? "Gourd" : $gourd
        );

        $currentPattern = array_values($mydata);
        sort($currentPattern);
         $currentPattern = $currentPattern[6];
        $mydata = [];
        $mydata["winning"] = implode(",", $draw_number);
        $mydata["draw_period"] = $draw_period;
        $mydata['stud'] = $currentPattern;
        array_push($historyArray, $mydata);

        // Update counts
        $highCard     =   ($currentPattern == "High Card")  ? 1 : ($highCard += 1);
        $onePair      =  ($currentPattern == "One Pair") ? 1 : ($onePair += 1);
        $twoPair      =  ($currentPattern == "Two Pair") ? 1 : ($twoPair += 1);
        $threeofakind =  ($currentPattern == "Three of a Kind") ? 1 : ($threeofakind += 1);
        $fourofakind  =  ($currentPattern == "Four of A Kind") ? 1 : ($fourofakind += 1);
        $streak       =  ($currentPattern == "Streak")  ? 1 : ($streak += 1);
        $gourd        =  ($currentPattern == "Gourd")  ? 1 : ($gourd += 1);
    }

    return array_reverse($historyArray);
}

public function threeCardsHistory($drawNumbers){

    $historyArray = [];
    foreach ($drawNumbers as  $item) {
        $mydata = [];
        $chunck_result = [];

        $draw_number = $item[self::DRAW_NUMBER_STR ];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        foreach (["first3", "mid3", "last3"] as $draw_chunck_name) {

            // Assuming findPattern and findStreakPattern5D are defined in PHP
            $startIndex =  $draw_chunck_name == "first3" ? 0 : ($draw_chunck_name == "mid3" ? 1 : 2);
            $sliceLength = $draw_chunck_name == "first3" ? 3 : ($draw_chunck_name == "mid3" ? 3 : 5);
            $is_toak        = self::findPattern(array(3), $draw_number, $startIndex, $sliceLength);
            $is_streak      = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 2);
            $is_pair        = self::findPattern(array(2, 1), $draw_number, $startIndex, $sliceLength);
            $is_half_streak = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 1);
            $is_mixed       = !$is_toak && !$is_streak && !$is_pair && !$is_half_streak;

            $mydata = array(
                'toak'       =>  $is_toak        ? "Toak"        : "",
                'halfStreak' =>  $is_half_streak ? "Half Streak" : "",
                'streak'     =>  $is_streak      ? "Straight"    : "",
                'pair'       =>  $is_pair        ? "Pair"        : "",
                'mixed'      =>  $is_mixed       ? "Mixed"       : "",
            );

            $mydata = array_values($mydata);
            sort($mydata);
            $chunck_result[$draw_chunck_name] = $mydata[4];
        }


        $keys = array_keys($chunck_result);

        $final_results = [
            $keys[0] => $chunck_result[$keys[0]],
            $keys[1] => $chunck_result[$keys[1]],
            $keys[2] => $chunck_result[$keys[2]],
            self::WINNING_NUMBER_STR   => implode(",", $draw_number),
            self::WINNING_PERIOD_STR => $draw_period

        ];
      array_push($historyArray, $final_results);
    }

    return $historyArray;
}


public function studHistory_11x5(array $draw_number, $draw_period)
{

    $draw_number = array_map("intval",$draw_number);
    $highCard     = 1;
    $onePair      = 1;
    $twoPair      = 1;
    $threeofakind = 1;
    $fourofakind  = 1;
    $streak       = 1;
    $gourd        = 1;

        // Assuming findPattern and findStreakPattern5D are defined in PHP
        $mydata = array(
            'highcard'      => self::findPattern(array(1, 1, 1, 1, 1), $draw_number, 0, 5) &&  self::findStreakPattern5D($draw_number, 0, 5, 4) == false  ? "High Card" : $highCard,
            'onepair'       => self::findPattern(array(2, 1, 1, 1), $draw_number, 0, 5) ? "One Pair" : $onePair,
            'twopair'       => self::findPattern(array(2, 2, 1), $draw_number, 0, 5) ? "Two Pair" : $twoPair,
            'threeofakind'  => self::findPattern(array(3, 1, 1), $draw_number, 0, 5) ? "Three of a Kind" : $threeofakind,
            'fourofakind'   => self::findPattern(array(4, 1), $draw_number, 0, 5) ? "Four of A Kind" : $fourofakind,
            'streak'        => self::findStreakPattern5D($draw_number, 0, 5, 4) ? "Streak" : $streak,
            'gourd'         => self::findPattern(array(3, 2), $draw_number, 0, 5) ? "Gourd" : $gourd
        );

        $currentPattern = array_values($mydata);
        sort($currentPattern);
        return $currentPattern[6];
}


// Ensure the calculateBull function is defined in PHP.
public function threeCardsHistory_11x5($draw_number)
{


        foreach (["first3", "mid3", "last3"] as $draw_chunck_name) {

            // Assuming findPattern and findStreakPattern5D are defined in PHP
            $startIndex     =  $draw_chunck_name == "first3" ? 0 : ($draw_chunck_name == "mid3" ? 1 : 2);
            $sliceLength    = $draw_chunck_name == "first3" ? 3 : ($draw_chunck_name == "mid3" ? 3 : 5);
            $is_toak        = self::findPattern(array(3), $draw_number, $startIndex, $sliceLength);
            $is_streak      = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 2);
            $is_pair        = self::findPattern(array(2, 1), $draw_number, $startIndex, $sliceLength);
            $is_half_streak = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 1);
            $is_mixed       = !$is_toak && !$is_streak && !$is_pair && !$is_half_streak;

            $mydata = array(
                'toak'       =>  $is_toak        ? "Toak"        : "",
                'halfStreak' =>  $is_half_streak ? "Half Streak" : "",
                'streak'     =>  $is_streak      ? "Straight"    : "",
                'pair'       =>  $is_pair        ? "Pair"        : "",
                'mixed'      =>  $is_mixed       ? "Mixed"       : "",
            );

            $mydata = array_values($mydata);
            sort($mydata);
            $chunck_result[$draw_chunck_name] = $mydata[4];
        }


        $keys = array_keys($chunck_result);

        return [
            $keys[0] => $chunck_result[$keys[0]],
            $keys[1] => $chunck_result[$keys[1]],
            $keys[2] => $chunck_result[$keys[2]],
        ];


}


public function board_game_5D(Array $draw_numbers,$lower_limit = 22){

    $history_array = [];

    foreach($draw_numbers as $draw_obj){
        $draw_number = $draw_obj[self::DRAW_NUMBER_STR];
        $draw_period = $draw_obj[self::DRAW_PERIOD_STR];
        $sum = array_sum($draw_number);
        
       
        //TODO: this is the real solution, but using the one below for demo for Mr. Eben.
        array_push($history_array, [self::WINNING_PERIOD_STR => $draw_period, self::WINNING_NUMBER_STR=>implode(",",$draw_number), 'stud' => $this->studHistory_11x5($draw_number,$draw_period),'three_cards' => $this->threeCardsHistory_11x5($draw_number)]);
        // array_push($history_array, ["draw_period" => $draw_period,"winning"=>implode(",",$draw_number),'sum' => $sum ]);
    }


    return $history_array;

}




public function two_sides_rapido(array $draw_numbers): array
{

    $historyArray = [];


    foreach ($draw_numbers as  $item) {
        $mydata = [];
        $chunck_result = [];
        $draw_number = $item[self::DRAW_NUMBER_STR];
        $draw_period = $item[self::DRAW_PERIOD_STR];

        foreach (["first3", "mid3", "last3"] as $draw_chunck_name) {

            // Assuming findPattern and findStreakPattern5D are defined in PHP
            $startIndex  =  $draw_chunck_name == "first3" ? 0 : ($draw_chunck_name == "mid3" ? 1 : 2);
            $sliceLength = $draw_chunck_name  == "first3" ? 3 : ($draw_chunck_name == "mid3" ? 3 : 5);

            $is_toak        = self::findPattern(array(3), $draw_number, $startIndex, $sliceLength);
            $is_streak      = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 2);
            $is_pair        = self::findPattern(array(2, 1), $draw_number, $startIndex, $sliceLength);
            $is_half_streak = self::findStreakPattern5D($draw_number, $startIndex, $sliceLength, 1);
            $is_mixed       = !$is_toak && !$is_streak && !$is_pair && !$is_half_streak;

            $mydata = array(
                'toak'       => $is_toak        ? "Toak"        : "",
                'halfStreak' => $is_half_streak ? "Half Streak" : "",
                'streak'     => $is_streak      ? "Straight"    : "",
                'pair'       => $is_pair        ? "Pair"        : "",
                'mixed 6'      => $is_mixed       ? "Mixed 6"       : "",
            );
            $mydata = array_values($mydata);
            sort($mydata);
            $chunck_result[$draw_chunck_name] = $mydata[4];
        }
        $b_s_o_e = "";
        $sum     = array_sum($draw_number);
        $b_s_o_e = self::b_s_o_e($sum, 22);
        $keys    = array_keys($chunck_result);

        $final_results = [
            "sum"              => $sum . " " . $b_s_o_e,
            "dragon_tiger_tie" => ["D" => "Dragon", "Tie" => "Tie", "T" => "Tiger"][self::dragonTigerTiePattern(0, 4, $draw_number)],
            $keys[0]           => $chunck_result[$keys[0]],
            $keys[1]           => $chunck_result[$keys[1]],
            $keys[2]           => $chunck_result[$keys[2]],
            "first"            => ['b_s' => $draw_number[0] > 4 ? 'B' : 'S', 'o_e' => $draw_number[0] % 2 == 1 ? 'O' : 'E', 'p_c' => self::checkPrimeOrComposite($draw_number[0])],
            "second"           => ['b_s' => $draw_number[1] > 4 ? 'B' : 'S', 'o_e' => $draw_number[1] % 2 == 1 ? 'O' : 'E', 'p_c' => self::checkPrimeOrComposite($draw_number[1])],
            "third"            => ['b_s' => $draw_number[2] > 4 ? 'B' : 'S', 'o_e' => $draw_number[2] % 2 == 1 ? 'O' : 'E', 'p_c' => self::checkPrimeOrComposite($draw_number[2])],
            "fourth"           => ['b_s' => $draw_number[3] > 4 ? 'B' : 'S', 'o_e' => $draw_number[3] % 2 == 1 ? 'O' : 'E', 'p_c' => self::checkPrimeOrComposite($draw_number[3])],
            "fifth"            => ['b_s' => $draw_number[4] > 4 ? 'B' : 'S', 'o_e' => $draw_number[4] % 2 == 1 ? 'O' : 'E', 'p_c' => self::checkPrimeOrComposite($draw_number[4])],
            self::WINNING_NUMBER_STR    => implode(",", $draw_number),
            self::WINNING_PERIOD_STR  => $draw_period

        ];


        array_push($historyArray, $final_results);
    }

    return $historyArray;
}



public function std(array $drawNumber): array {

        return [
            'all5'                   =>  $this->all5History($drawNumber),
            'all4'                   =>  ["first4" => $this->all4History($drawNumber, "all4first4"), "last4" =>  $this->all4History($drawNumber, "all4last4")],
            'all3'                   =>  ["first3" => $this->all3History5d($drawNumber, "all3first3"), "mid3" => $this->all3History5d($drawNumber, "all3mid3"), "last3" => $this->all3History5d($drawNumber, "all3last3")],
            'all2'                   =>  ["first2" => $this->all2History5d($drawNumber, "all2first2"), "last2" => $this->all2History5d($drawNumber, "all2last2")],
            'fixedplace'             =>  $this->all5History($drawNumber),
            'anyplace'               =>  $this->all5History($drawNumber),
            'bsoe'                   =>  ["first2" => $this->bsoeHistory($drawNumber, "bsoefirst2"), "mid2" => $this->bsoeHistory($drawNumber, "bsoemid2"), "first3" =>  $this->bsoeHistory($drawNumber, "bsoefirst3"), "last2" => $this->bsoeHistory($drawNumber, "bsoelast2"), "last3" =>    $this->bsoeHistory($drawNumber, "bsoelast3"), "bsoesumofall3" => $this->bsoeHistory($drawNumber, "bsoesumofall3"), "sumofall5" => $this->bsoeHistory($drawNumber, "bsoesumofall5")],
            'pick2'                  =>  $this->winning_number5d($drawNumber),
            'fun'                    =>  $this->winning_number5d($drawNumber),
            'pick3'                  =>  $this->winning_number5d($drawNumber),
            'pick4'                  =>  $this->winning_number5d($drawNumber),
            'dragonTiger'            =>  $this->dragonTigerHistory($drawNumber),
            'stud'                   =>  $this->studHistory($drawNumber),
            'threecards'             =>  $this->threeCardsHistory($drawNumber),
            'bulls'                  =>  $this->modifiedCalculateBullHistory5D($drawNumber),
        ];
} // end of render5d. Returns all the history for 5D.


public function  two_sides(array $drawNumber): array
{
    return [
        'rapido'       =>  self::two_sides_rapido($drawNumber),
        'all_kinds'    =>  self::two_sides_rapido($drawNumber),
    ];
} // end of render5d. Returns all the history for 5D.


public function board_games(array $drawNumber): array
{
    return $this->board_game_5D($drawNumber);
} // end of render5d. Returns all the history for 5D.


public function fantan(array $draw_data):array
{

    $final_res = [];

    foreach ($draw_data as $key => $data) {
        $res = [];
        $drawNumber = $data[self::DRAW_NUMBER_STR];
        $draw_period = $data[self::DRAW_PERIOD_STR];
        $DRAW_NUMBERS = array_map('intval', $drawNumber);
        $SUM_OF_DRAW_NUMBERS = array_sum($DRAW_NUMBERS);

        $sum_of_three = array_sum(array_slice($DRAW_NUMBERS, 2));
        $res[self::WINNING_NUMBER_STR] = implode(",",$drawNumber);
        $res[self::WINNING_PERIOD_STR] = $draw_period;

        $res['sum'] = (string)$SUM_OF_DRAW_NUMBERS;
        $res['sum_last_three'] = (string)array_sum(array_slice($drawNumber, -3, 3));
        $res['big_small'] = self::fantanBigSmall($SUM_OF_DRAW_NUMBERS, 23, 45, 0, 22);
        $res['odd_even'] = self::fantanOddEven($SUM_OF_DRAW_NUMBERS);
        $res['only_fantan2'] = self::onlyFantan2($sum_of_three)[0];
        $res['dragon_tiger'] = (intval($drawNumber[0]) > intval($drawNumber[4])) ? "Dragon" : ( (intval($drawNumber[0]) < intval($drawNumber[4])) ? "Tiger" : "Tie");
        $final_res[] = $res;
    }
    return $final_res;
}






















































































































public static function spanPattern5d(array $drawNumbers, int $index, int $slice): int {
    // Slicing the array from index for the length of slice
    $slicedNumbers = array_slice($drawNumbers, $index, $slice);

    // Sorting the sliced array
    sort($slicedNumbers);

    $slicedNumbers = array_map(function ($val) {
        return intval($val);
    }, $slicedNumbers);
    // Getting the max and min values in the sliced array
    $maxValue = max($slicedNumbers);
    $minValue = min($slicedNumbers);

    // Returning the difference between max and min values
    return $maxValue - $minValue;
} // end of spanPattern5d


public static function findDuplicates($numbers)
{
    // Count the occurrences of each number
    $count = array_count_values($numbers);

    // Filter the counts to find duplicates
    $duplicates = array_filter($count, function ($value) {
        return $value > 1;
    });

    // Return the keys of duplicates (which are the duplicate numbers)
    return array_map('strval', array_keys($duplicates));
}

}