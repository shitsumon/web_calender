<?php
/*
* Copyright (c) 2012 Michael Flau <michael@flau.net>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*Global values*/
define("JUMPBACK_URL", "Location: http://hermes.et.hs-wismar.de/~bmt08055/calender/index.html");
define("XML_SRC", "../xml/data.xml");
define("RAND_MIN", 1024);
define("RAND_MAX", 2048);

/*
    Returns a random hex value
    between RAND_MIN and RAND_MAX
*/
function randHex(){
 return dechex(rand(RAND_MIN, RAND_MAX));   
}

/*
    Helper function to display an array in a formatted way.
    
    Input Arguments:
    $myArray - Array structure to be displayed
    
    Return value:
    none
*/
function debugArray($myArray){
    print("<pre>");
    print_r($myArray);
    print("</pre>");
}

/*
    Helper function which maps the 
    numerical month to a corresponding
    string.
    
    Input Arguments:
    $nMonth - Numerical Month
    
    Return value:
    String which corresponds to to numerical month representation
*/
function monthMapper($nMonth){
    
        switch($nMonth){
        case 1: 
            return "january";
            break;
        case 2: 
            return "february";
            break;
        case 3: 
            return "march";
            break;
        case 4: 
            return "april";
            break;
        case 5: 
            return "may";
            break;
        case 6: 
            return "june";
            break;
        case 7: 
            return "july";
            break;
        case 8: 
            return "august";
            break;
        case 9: 
            return "september";
            break;
        case 10: 
            return "october";
            break;
        case 11: 
            return "november";
            break;
        case 12: 
            return "december";
            break;
        default:
            print("Given numeric value is causing exception!");
            return -1;
            break;
        }
}

/*
string2NumberMonthMapper()
Helper function which maps a given month string in english or german
to a corresponding month number between 1 and 12.

Input arguments:
    nMonth - string representation of month
    
Return value:
    Integer value - represents month as a number
*/
function string2NumberMonthMapper($nMonth){

        switch($nMonth){
        case "january":
        case "januar":
            return 1;
            break;
        case "february":
        case "februar":
            return 2;
            break;
        case "march":
        case "märz":
            return 3;
            break;
        case "april":
            return 4;
            break;
        case "may":
        case "mai":
            return 5;
            break;
        case "june":
        case "juni":
            return 6;
            break;
        case "july":
        case "juli":
            return 7;
            break;
        case "august": 
            return 8;
            break;
        case "september": 
            return 9;
            break;
        case "october":
        case "oktober":
            return 10;
            break;
        case "november": 
            return 11;
            break;
        case "december":
        case "dezember":
            return 12;
            break;
        default:
            print("Given string is causing exception!");
            return -1;
            break;
        }
}

function englishMonthToGermanMonth($monthstring){

    switch(strtolower($monthstring)){
    case "january"   : return "Januar";     break;
    case "february"  : return "Februar";    break;
    case "march"     : return "M&aumlrz";  break;
    case "april"     : return "April";      break;
    case "may"       : return "Mai";        break;
    case "june"      : return "Juni";       break;
    case "july"      : return "Juli";       break;
    case "august"    : return "August";     break;
    case "september" : return "September";  break;
    case "october"   : return "Oktober";    break;
    case "november"  : return "November";   break;
    case "december"  : return "Dezember";   break;
    default: return "There is no such english month!"; break;
    }
}

function germanMonthToEnglishMonth($monthstring){

    switch(strtolower($monthstring)){
    case "januar"    : return "january";     break;
    case "februar"   : return "february";    break;
    case "märz"      : return "march";       break;
    case "april"     : return "april";      break;
    case "mai"       : return "may";        break;
    case "juni"      : return "june";       break;
    case "juli"      : return "july";       break;
    case "august"    : return "august";     break;
    case "september" : return "september";  break;
    case "oktober"   : return "october";    break;
    case "november"  : return "november";   break;
    case "dezember"  : return "december";   break;
    default: return "There is no such german month!"; break;
    }
}

/*
    Determines the 7 adjacent day numbers from current day number,format with respect
    to the position of the current day within the week. 
    
    Input Arguments:
    $daysBack    - number of days to go back from current day
    $daysForward - number of days to go forward from current day
    $month       - the current month number
    $day         - the current day number
    
    Return value:
    $dayNumbers  - contains the day numbers of the current day and its 6 adjacents
*/
function getAdjacentDayNumbers($daysBack, $daysForward, $month, $day){
    
    $timestamp      = time();
    $daysOfMonth    = date("t", mktime(0,0,0, $month, $day, date("Y", $timestamp)));
    $dayNumbers     = array();
    $lowerDayLimit  = 7;
    $upperDayLimit  = 21;
    
    //If adjacent 7 days go back one month
    if($day < $lowerDayLimit && ($day - $daysBack) <= 0){
        
        $daysOfLastMonth = date("t", mktime(0,0,0, $month - 1 , $day, date("Y", $timestamp)));
        
        $diff = -($day - $daysBack);
        
        for(; $diff >= 0; --$diff){
            $dayNumbers[] = $daysOfLastMonth - $diff;
            --$daysBack;
        }
    }    
    
    //Go $daysBack
    for(; $daysBack > 0; --$daysBack){
        $dayNumbers[] = $day - $daysBack;
    }
    
    //Set in current day
    $dayNumbers[] = $day;
    
    //Get the value from $daysForward, which we can change
    //without losing the original value
    $workingParamDaysForward = $daysForward;
    
    //Go $daysForward
    for($index = 0; $index < $daysForward; ++$index){

        if(($index + $day) >= $daysOfMonth){
                $workingParamDaysForward -= $index;
                break;
        }
        
        $dayNumbers[] = $day + ($index + 1);
    }

    //If adjacent 7 days lie in the next month as well
    if($day > $upperDayLimit && ($day + $daysForward) > $daysOfMonth){
        
         for($index = 0; $index < $workingParamDaysForward; ++$index){
            $dayNumbers[] = $index + 1;
        }
    }
    
    return $dayNumbers;
}

/*
    Takes a UNIX timestamp and calculates the date one month prior to it.
    
    Input arguments:
    $timestamp - source timestamp
    
    Return value:
    Timestamp based on source timestamp one month past.
*/
function monthBack($timestamp){
    return mktime(0,0,0, date("m", $timestamp) - 1 , date("d", $timestamp), date("Y", $timestamp));
}

/*
    Takes a UNIX timestamp and calculates the date one month ahead to it.
    
    Input arguments:
    $timestamp - source timestamp
    
    Return value:
    Timestamp based on source timestamp one month ahead.
*/
function monthForward($timestamp){
    return mktime(0,0,0, date("m", $timestamp) + 1 , date("d", $timestamp), date("Y", $timestamp));
}

/*
    Takes a UNIX timestamp and calculates the date one year prior to it.
    
    Input arguments:
    $timestamp - source timestamp
    
    Return value:
    Timestamp based on source timestamp one year past.
*/
function yearForward($timestamp){
    return mktime(0,0,0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp) + 1);
}

/*
    Takes a UNIX timestamp and calculates the date one year ahead to it.
    
    Input arguments:
    $timestamp - source timestamp
    
    Return value:
    Timestamp based on source timestamp one year ahead.
*/    
function yearBack($timestamp){
    return mktime(0,0,0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp) - 1);
}

/*
    This function checks every Event for it's
    current state. An event can be in one of three
    deterministic time states:
    a) The event has not begun yet
    b) The event is taking place at the moment
    c) The event is already over
    Based on the start- and endtimes every event
    must have, this function returns a value which
    is used to sort an event to it's actual state.
    
    Input Arguments:
    $starttime  - time when the event starts
    $endtime    - time when teh event ends
    
    Return values:
    0 - see a) above
    1 - see b) above
    2 - see c) above
*/
function timeChecker($starttime, $endtime){
    $currentHour   = date('G', time());
    $currentMinute = date('i', time());
    
    list($startHour, $startMinute) = explode(':', $starttime);
    list($endHour, $endMinute)     = explode(':', $endtime);
    
    $iCurrentHour   = (int)$currentHour;
    $iCurrentMinute = (int)$currentMinute;
    $iStartHour   	= (int)$startHour;
    $iStartMinute 	= (int)$startMinute;
    $iEndHour   	= (int)$endHour;
    $iEndMinute 	= (int)$endMinute;
    
    if($iCurrentHour < $iStartHour && $iCurrentMinute < $iStartMinute){
        return 0;
    }else if($iCurrentHour < $iStartHour && $iCurrentMinute == $iStartMinute){
        return 0;
    }else if($iCurrentHour < $iStartHour && $iCurrentMinute > $iStartMinute){
        return 0;
    }else if($iCurrentHour == $iStartHour && $iCurrentMinute < $iStartMinute){
        return 0;
    }else if($iCurrentHour == $iStartHour && $iCurrentMinute == $iStartMinute){
        return 1;
    }else if($iCurrentHour == $iStartHour && $iCurrentMinute > $iStartMinute){
        
        if($iCurrentHour < $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute > $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute < $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute == $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }
        
    }else if($iCurrentHour > $iStartHour && $iCurrentMinute < $iStartMinute){
    
        if($iCurrentHour < $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute > $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute < $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute == $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }
        
    }else if($iCurrentHour > $iStartHour && $iCurrentMinute == $iStartMinute){

            if($iCurrentHour < $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute > $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute < $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute == $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }
        
    }else if($iCurrentHour > $iStartHour && $iCurrentMinute > $iStartMinute){

        if($iCurrentHour < $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour < $iEndHour && $iCurrentMinute > $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute < $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute == $iEndMinute){
            return 1;
        }else if($iCurrentHour == $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute < $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute == $iEndMinute){
            return 2;
        }else if($iCurrentHour > $iEndHour && $iCurrentMinute > $iEndMinute){
            return 2;
        }
    
    }
}

/*
    Function sorts incoming events
    to their corresponding time slots
*/
function timeSorter($starttime, $endtime){
    
    list($startHour, $startMinute) = explode(':', $starttime);
    list($endHour, $endMinute)     = explode(':', $endtime);
        
    $iStartTime     = ((int)$startHour * 60) + (int)$startMinute;
    $iEndTime       = ((int)$endHour   * 60) + (int)$endMinute;
    $iEventDuration = $iEndTime - $iStartTime;
    
    $startTimes = array(495, 600, 690, 750, 855, 960, 1440);  //Last one is a dummy value 24H * 60min = 1440
    $endTimes   = array(585, 690, 750, 840, 945, 1050, 1440); //Last one is a dummy value 24H * 60min = 1440
    
    $startTimeSlot  = 0;
    $endTimeSlot    = 0;
    
    //get start timeslot
    for($index = 0; $index < 7; ++$index){
            
        if($iStartTime >= $startTimes[$index] && $iStartTime < $startTimes[$index + 1]){
            $startTimeSlot = $index;
            
            if($iStartTime >= 1050) ++$startTimeSlot;
            
            break;
        }
    }

    //get end timeslot
    for($index = 0; $index < 7; ++$index){
            
        if($iEndTime <= $endTimes[$index]){
            $endTimeSlot = $index;
            break;
        }
    }

    $timeArray = array($startTimeSlot);

    if($endTimeSlot === $startTimeSlot){
        return $timeArray;
    }
    
    //Check for intermediate timeslots to fill
    if(($endTimeSlot - $startTimeSlot) > 1){
            $diff = $endTimeSlot - $startTimeSlot;
            
            for($index = 1; $index < $diff; ++$index){
                $timeArray[] = $startTimeSlot + $index;
            }
    }
    
    $timeArray[] = $endTimeSlot;

    return $timeArray;
}


/*debugArray(timeSorter("8:15", "9:45"));
debugArray(timeSorter("9:12", "9:45"));
debugArray(timeSorter("10:22", "11:33"));
debugArray(timeSorter("15:46", "17:23"));
debugArray(timeSorter("14:15", "15:45"));
*/

/*
    getNumberOfDaysInMonth()

    Based on a given date, the function calculates the
    number of days within the dates month.Back
    
    Input values:
    
    $month - must be string which represents a number 
             between 1 and 12 or a number between 1 and 12
    $year  - must be a string which represents a number with 4 digits
             or a an actual number with 4 digits
             
    Return value:
    
    Number of days of the given month as a numeric value between 28 and 31
*/
function getNumberOfDaysInMonth($month, $year){
    
        $timestamp = strtotime($year."-".$month."-1");
        $count = date('t', $timestamp);
        return intVal($count);
}

?>
