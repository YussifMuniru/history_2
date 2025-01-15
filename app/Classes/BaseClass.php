<?php

namespace App\Classes;

use App\Utils\Utils;
use App\Config\RedisClient;
use App\Config\Database;

class BaseClass extends Utils{

    // game type properties
    protected static $standard = "standard";
    protected static $two_sides = "two_sides";
    protected static $fantan = "fantan";
    protected static $board_games = "board_games";

public function sumPattern(Array $drawNumbers, int $index,int $slice) : int {
    // Slicing the array from index for the length of slice
    $slicedArray = array_slice($drawNumbers, $index, $slice);

    // Calculating the sum of the sliced array
    $sum = array_sum($slicedArray);

    return $sum;
} // end of sumPattern. Sum the array chunk.


public function findPattern(Array $pattern,Array $drawNumbers,int $index, int $slice) : bool{
   $count = array_count_values(array_slice($drawNumbers, $index, $slice));
   sort($count); sort($pattern);
    return $count == $pattern;
}// end of findPattern.


public static function bigSmallOddEvenPattern(array $drawNumbers, int $start, int $slice, int $index1, int $index2): array
{
    // Slice the drawNumbers array
    $numbers = array_slice($drawNumbers, $start, $slice);
    // Directly access the array elements
    $num1 = $numbers[$index1];
    $num2 = $numbers[$index2];

    // Determine the pattern for the first number
    $first2 = $num1 < 5 ? ($num1 % 2 === 0 ? "S E" : "S O") : ($num1 > 4 ? ($num1 % 2 === 0 ? "B E" : "B O") : "not found");

    // Determine the pattern for the second number
    $last2 = $num2 < 5 ? ($num2 % 2 === 0 ? "S E" : "S O") : ($num2 > 4 ? ($num2 % 2 === 0 ? "B E" : "B O") : "not found");

    // Return the concatenated result
    return ["num1" => $first2, "num2" =>  $last2];
} // end of bigSmallOddEvenPattern


public static function bigSmallOddEvenPattern3($drawNumbers, $start, int $slice, int $index1, int $index2, int $index3): array
{
    // Slice the drawNumbers array
    $numbers = array_slice($drawNumbers, $start, $slice);

    // Calculate the sum of the sliced array
    $sum = array_sum($numbers);

    // Ensure indices are integers
    $index1 = intval($index1);
    $index2 = intval($index2);
    $index3 = intval($index3);

    // Define a helper function to determine the pattern


    // Determine the pattern for each number
    $first2 = self::determinePattern5d($numbers[$index1]);
    $last2  = self::determinePattern5d($numbers[$index2]);
    $last3  = self::determinePattern5d($numbers[$index3]);

    // Return the concatenated result
    return ["sum" => $sum, "num1" => $first2, "num2" => $last2, "num3"  => $last3];
} // end of bigSmallOddEvenPattern3



public static function sumAndFindPattern($drawNumbers, $index, $slice, $range, $prefix)
{
    // Slice the array from the specified index with the specified length
    $numbers = array_slice($drawNumbers, $index, $slice);

    // Calculate the sum of the sliced array
    $sum = array_sum($numbers);

    // Determine the pattern based on the sum and range
    $pattern = "";
    if ($sum < $range[0]) {
        $pattern = $sum % 2 === 0 ? "Small Even" : "Small Odd";
    } elseif ($sum > $range[1]) {
        $pattern = $sum % 2 === 0 ? "Big Even" : "Big Odd";
    } else {
        $pattern = "not found";
    }
    //  echo "Sum {$sum}, form =>$pattern";
    // Return the sum and pattern as a string
    return ["{$prefix}sum" => $sum, "{$prefix}form" => $pattern];
}


public static function sumAndFindPattern1($drawNumber, $index, $slice, $range){
    // Slice the array from the specified index with the specified length
    $numbers = array_slice($drawNumber, $index, $slice);

    // Calculate the sum of the sliced array
    $sum = array_sum($numbers);

    // Determine the pattern based on the sum and range
    $pattern = "";
    if ($sum < $range[0]) {
        $pattern = $sum % 2 === 0 ? "S E" : "S O";
    } elseif ($sum > $range[1]) {
        $pattern = $sum % 2 === 0 ? "B E" : "B O";
    } else {
        $pattern = "not found";
    }

    $pattern_parts = explode(" ", $pattern);
    // Return the sum and pattern as a string
    return ["sum" => $sum, "b_s" =>  $pattern_parts[0], "o_e" => $pattern_parts[1]];
}
public static function dragonTigerTiePattern(int $idx1,int $idx2,Array $drawNumbers) : string{
    $v1 = $drawNumbers[$idx1];
    $v2 = $drawNumbers[$idx2];

    if ($v1 > $v2) {
      
        return "D";
    } elseif ($v1 === $v2) {
       
        return "Tie";
    } else {
       
        return "T";
    }
}// end of dragonTigerTiePattern. returns the dragon tiger tie relationship btn the numbers

public static function findStreakPattern5D($drawNumbers, $index, $slice, $streakCount)
{
    // Slice the array from the specified index with the specified length
    $slicedArray = array_slice($drawNumbers, $index, $slice);
    $count = 0;
    $n = count($slicedArray);
    // Sort the array
    sort($slicedArray);
    // Check for edge case with numbers 0 and 9 at the ends
    if (($slicedArray[0] == 0 && $slicedArray[$n - 1] == 9) || ($slicedArray[0] == 9 && $slicedArray[$n - 1] == 0)) {
        $count = +1;
    }
   // Check for sequential streaks
    for ($i = 0; $i < ($n - 1); $i++) {
        $current_num = intval($slicedArray[$i]);
        // if($current_num == 0 || $current_num == 9) continue;
        if ($current_num == intval($slicedArray[$i + 1]) - 1) {
        $count += 1;
        }
    }
   // Check if the count matches the streak count
    return $count === $streakCount;
}

public static function modifiedCalculateBullHistory5D(array $drawNumbers): array{
    $bullBig   = 0;
    $bullSmall = 0;
    $bullEven  = 0;
    $bullOdd   = 0;

    $drawNumbers = array_reverse($drawNumbers);
    $historyArray = [];

    foreach ($drawNumbers as  $item) {

        $draw_number = $item['draw_number'];
        $draw_period = $item['period'];

        // Assuming calculateBull is a function you have defined in PHP
        $bullResult = self::modifiedCalculateBull($item['draw_number']);
        // $historyArray[] = $bullResult;
        $bullResultArray = explode(" ", $bullResult);
        $parsedNumber = intval(trim($bullResultArray[1]));
        $is_num = gettype($parsedNumber) === "integer";

        $bullBig   =   ($is_num && $parsedNumber > 5) || $bullResult === "Bull Bull" ? "Bull Big" : (gettype($bullBig) === "string" ? 1 : $bullBig += 1);
        $bullSmall =   ($is_num && $parsedNumber <= 5) && $parsedNumber > 0           ? "Bull Small"  : (gettype($bullSmall) === "string" ? 1 :  $bullSmall += 1);
        $bullOdd   =   ($is_num && $parsedNumber % 2 === 1) && $parsedNumber > 0      ? "Bull Odd" : (gettype($bullOdd) === "string" ? 1 : $bullOdd += 1);
        $bullEven  =   ($is_num && $parsedNumber % 2 === 0)  ? "Bull Even" : (gettype($bullEven) === "string" ? 1 :  $bullEven += 1);


        $mydata = [
            'bull_bull'  =>  $bullResult,
            'bull_big'   =>  $bullBig,
            'bull_small' =>  $bullSmall,
            'bull_odd'   =>  $bullOdd,
            'bull_even'  =>  $bullEven,
        ];

        $mydata["winning"] = implode(",", $draw_number);
        $mydata["draw_period"] = $draw_period;

        array_push($historyArray, $mydata);
    }

    // return $historyArray;
    return array_reverse($historyArray);
}

public static function modifiedCalculateBull($digits)
{
   
    if (count($digits) < 5) {
        return "No Bull"; // Need at least 5 digits
    }

    // Try all combinations of three digits
    for ($i = 0; $i < (count($digits) - 2); $i++) {
        for ($j = $i + 1; $j < (count($digits) - 1); $j++) {
            for ($k = $j + 1; $k < count($digits); $k++) {
                if (self::isSumTen([$digits[$i], $digits[$j], $digits[$k]])) {
                    // Sum the other two digits
                    $remainingDigits = array_filter(
                        $digits,
                        function ($index) use ($i, $j, $k) {
                            return $index !== $i && $index !== $j && $index !== $k;
                        },
                        ARRAY_FILTER_USE_KEY
                    );
                    $remainingSum = array_sum($remainingDigits);
                    $lastDigit = $remainingSum % 10;

                    // Check last digit of remaining sum
                    if ($lastDigit === 0) {
                        return "Bull Bull";
                    } else {
                       return "Bull " . $lastDigit;
                    }
                }
            }
        }
    }
 
    return "No Bull";
    // If no valid combination was found, return "No Bull"
    // return "No Bull";
}

public static function checkPrimeOrComposite($number)
{

    // Check if the number is less than 2.
    if ($number == 1 || $number == 0) return $number === 1 ? "P" : "C";
    
    // Check from 2 to the square root of the number.
    for ($i = 2; $i <= sqrt($number); $i++) {
        if ($number % $i == 0) {
            return "C";
        }
    }
    // If no divisors were found, it's prime.
    return "P";
}

/**
 * Determines if a number is big or small.
 * @param num - The number to check.
 * @param bigMin - The minimum value for a big number.
 * @param bigMax - The maximum value for a big number.
 * @param smallMin - The minimum value for a small number.
 * @param smallMax - The maximum value for a small number.
 * @returns A string "Big", "Small", or "Invalid input".
 */
public static function fantanBigSmall(int $num, int $bigMin,int $bigMax,int $smallMin,int $smallMax): string {
    if ($num >= $bigMin && $num <= $bigMax) {
        return "Big";
    } else if ($num >= $smallMin && $num <= $smallMax) {
        return "Small";
    } else if ($num === 810) {//happy8
        return "Tie";
    }  else if ($num === 49) {//mark6
        return "Tie";
    }else {
        return "Invalid input";
    }
}

/**
* Determines if a number is odd or even.
* @param num - The number to check.
* @returns A string "Odd" or "Even".
*/
public static function fantanOddEven(int $num): string {
    if($num == 49) return "Tie";
    if ($num % 2 === 0) {
        return "Even";
    } else {
        return "Odd";
    }
}

public static function onlyFantan2(int $num): array {
    $remainder = $num % 4;
    if ($remainder === 0) {
        return [
            "4 Fan",
        ];
    } else if ($remainder === 1) {
        return [
            "1 Fan",
        ];
    } else if ($remainder === 2) {
        return [
            "2 Fan",
        ];
    } else if ($remainder === 3) {
        return [
            "3 Fan",
        ];
    } else {
        return ["Invalid remainder"];
    }
}

public static function determinePattern5d(int $num, $lower_limit = 4): String {
    if ($num <= $lower_limit) {
        return $num % 2 === 0 ? "S E" : "S O";
    } elseif ($num > $lower_limit) {
        return $num % 2 === 0 ? "B E" : "B O";
    }
    return " ";
} // end of determinePattern5d

// Check if the sum of the digits is a multiple of 10.
public static function isSumTen($digits)
{
  return ((array_sum($digits) % 10 === 0) || (array_sum($digits) === 0));
}

public static function spanPattern(Array $drawNumbers, int $index, int $slice) : int  {
    
    // Slicing the array from index for the length of slice
    $slicedNumbers = array_slice($drawNumbers, $index, $slice);
   
    // Sorting the sliced array
    sort($slicedNumbers);

    
    // Getting the max and min values in the sliced array
   $maxValue = max($slicedNumbers);
   $minValue = min($slicedNumbers);

    // Returning the difference between max and min values
    return $maxValue - $minValue;

}// end of spanPattern. Returns the difference btn the max and min values of the draw number


// Big Small Odd Even in the format ( B O, S E, etc)
public static function b_s_o_e(int $num, $lower_limit = 4): String
{
    if ($num <= $lower_limit) {
        return $num % 2 === 0 ? "S E" : "S O";
    } elseif ($num >= $lower_limit) {
        return $num % 2 === 0 ? "B E" : "B O";
    }
    return " ";
} // end of b_so_e


public static function determinePattern(int $num, $small_category, $check_prime = false): String
{

    $num = intval($num);
    $pattern = "";
    if ($num <= $small_category) {
       $pattern .= $num % 2 === 0 ? "S E" : "S O";
    } else if ($num > $small_category) {
     $pattern .= $num % 2 === 0 ? "B E" : "B O";
    }

    if ($check_prime) return $pattern;

    if (self::isPrime($num)) {
        $pattern .= " P";
    } else {
        $pattern .= " C";
    }


    return $pattern;
} // end of determinePatter


public static function isPrime($number) {
    
    if ($number == 0) return false;

    if ($number <= 3) {
        return true; // 2 and 3 are prime numbers
    }

    // Check from 2 to sqrt(number) for any divisors
    $sqrt = sqrt($number);
    for ($i = 2; $i <= $sqrt; $i++) {
        if ($number % $i == 0) {
            return false; // Number is divisible by some number other than 1 and itself
        }
    }

    // If we find no divisors, it's a prime number
    return true;
}


// this will generate the history for std
public function std(array $draw_data):array{ return []; }

// this will generate the history for two_sides
public function two_sides(array $draw_data):array{ return []; }

// this will generate the history for fantan
public function fantan(array $draw_data):array{ return []; }

// this will generate the history for board_games
public function board_games(array $draw_data):array{ return []; }

// this generate's the history
public function generate(int $lottery_id= 0 ,int $lottery_model = 0):array{
     // generate data for Class5D chart
   try{
   if ($lottery_id > 0) {
        $db_results = Database::fetch_draw_numbers($lottery_id);
        $draw_data = $db_results['data'];

        // save the latest draw period to Redis
        $latest_draw_period = isset($draw_data[0]) ? "{$draw_data[0][self::DRAW_PERIOD_STR]}" : "";
        $redis = new RedisClient();
        $redis->updateLatestDrawPeriod($lottery_id,$latest_draw_period);
       
        if ($lottery_model == 1) {
            return [self::$standard => $this->std($draw_data), self::$two_sides => $this->two_sides($draw_data)];
        } else if($lottery_model == 2){
            return [self::$fantan => $this->fantan($draw_data)];
        } else {
            return [self::$board_games => $this->board_games($draw_data)];
        }   
    } else {
        return  ['status' => false];
    }
      
 }catch(\Exception $e){
    AppLogger ::error(LogLevel ::ERROR, $e);
}
}



}