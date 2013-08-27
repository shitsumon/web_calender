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

include("loadXML.php");

/*Creates a list of events which will take place during the current month.*/
function createMonthView($timestamp, $headline = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag')){

    //Get events for current month
    $database 			= array();
    $database 			= queryEventsWithinMonth(XML_SRC , date('Y', $timestamp), date('n', $timestamp));

    $daysCurrentMonth 	= date('t', $timestamp); //Number of days of the current month
    $daysLastMonth 		= date('t', monthBack($timestamp));
    
    echo "<div class=\"monthview\">";
    //Generates headline with weekdays in it.
    foreach($headline as $key => $value){
        echo "<div class=\"day headline\">".$value."</div>\n";
    }
    
    //1st fill the first cells with remaining days from last month
    $nameOfDay 	    = date('D', mktime(0,0,0, date('m', $timestamp), 1, date('Y', $timestamp)));
    $indexOfWeekDay = array_search($nameOfDay, array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
    
    for($index = $indexOfWeekDay; $index > 0; --$index){
        $diffOfDays = $daysLastMonth - $index;
        echo "<div class=\"day before\">".sprintf("%02d", $diffOfDays + 1)."</div>\n";
    }
    
    $javascriptTags = array();
    
    //Generates a cell for each day in current month
    for( $index = 1; $index <= $daysCurrentMonth; $index++){

        $dayIndex 	 = "day_".$index;
        
        //days for the current month are distinguished into normal days
        //and the current days which is marked as the current day
        if( $index == date('d', $timestamp)){
            
            $javascriptTags[] = $dayIndex;
            
            echo "<div class=\"day current\">".sprintf("%02d", $index)."<div id=\"".$dayIndex."\" class=\"events\">";
            
            foreach($database as $item){
                if($item["day"] == $dayIndex){
                    
                    if( !in_array($dayIndex, $javascriptTags, true) ){
                        $javascriptTags[] = $dayIndex;
                    }
                    
                    echo $item["name"]."<br>".$item["startTime"]."Uhr - ".$item["endTime"]."Uhr<br>"."Raum: ".$item["house"]."/".$item["room"]."#";
                }
            }
        }else{
            echo "<div class=\"day normal\">".sprintf("%02d", $index)."<div id=\"".$dayIndex."\" class=\"events\">";
            
            foreach($database as $item){
                if($item["day"] == $dayIndex){
                    
                    if( !in_array($dayIndex, $javascriptTags, true) ){
                        $javascriptTags[] = $dayIndex;
                    }
                
                    echo $item["name"]."<br>".$item["startTime"]."Uhr - ".$item["endTime"]."Uhr<br>"."Raum: ".$item["house"]."/".$item["room"]."#";
                }
            }
        }
        
        echo "</div></div>\n";
    }

    //Fill rest of month view with days from the coming month
    $nameOfDay 	    = date('D', mktime(0,0,0, date('m', $timestamp), $daysCurrentMonth, date('Y', $timestamp)));
    $daysNextMonth  = 6 - array_search($nameOfDay, array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
    
    for($index2 = 1; $index2 <= $daysNextMonth; $index2++){
        echo "<div class=\"day after\">".sprintf("%02d",$index2)."</div>\n";
    }

    echo "<div id=\"tagContainer\">";
    foreach($javascriptTags as $tag){
        echo $tag."#";
    }
    echo "</div>\n";
    
}

/*Creates a list of events which will take place in the current week.*/
function createWeekView($timestamp){
    
    $month = date('n', $timestamp);
    $day   = date('j', $timestamp);
    $year  = date('Y', $timestamp);
    
    $now  = date('G:i', $timestamp);
    $then = date('G:i', $timestamp + 60);
    
    $highlightSlot = timeSorter($now, $then);
    
    //Get events for current month
    $database = array();
    $database = queryEventsWithinWeek(XML_SRC, $year, $month, $day);
    
    //debugArray($database);
    
    //Check if database array is empty    
    $weekday    = date("D", mktime(0, 0, 0, $month, $day, $year));
    $dayNumbers = array();
    
    switch($weekday)//Determine the day number from which we need to extract information
    {
     case "Mon":
        $dayNumbers = getAdjacentDayNumbers(0, 6, $month, $day);
     break;
     case "Tue":
        $dayNumbers = getAdjacentDayNumbers(1, 5, $month, $day);
     break;
     case "Wed":
        $dayNumbers = getAdjacentDayNumbers(2, 4, $month, $day);
     break;
     case "Thu":
        $dayNumbers = getAdjacentDayNumbers(3, 3, $month, $day);
     break;
     case "Fri":
        $dayNumbers = getAdjacentDayNumbers(4, 2, $month, $day);
     break;
     case "Sat":
        $dayNumbers = getAdjacentDayNumbers(5, 1, $month, $day);
     break;
     case "Sun":
        $dayNumbers = getAdjacentDayNumbers(6, 0, $month, $day);
     break;
     default:
     break;         
    }
    
    //debugArray($dayNumbers);
    
    $firstDay = $dayNumbers[0];
    $lastDay  = end($dayNumbers);
    
    $week     = $firstDay.".".date("n",$timestamp).".".date("Y",$timestamp)." bis ".$lastDay.".".date("n",$timestamp).".".date("Y",$timestamp);
    $headline = array("KW: ". date("W", $timestamp), "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
    
    echo "<div class=\"week\">Woche vom ".$week."</div>\n";
    
    //Generates headline with weekdays in it.
    foreach($headline as $value){
        
        if(strpos($value, "KW:") === 0){
            echo "<div class=\"day headline\" id=\"kw\" >".$value."</div>";
        }else{
            echo "<div class=\"day headline\">".$value."</div>";
        }
    }
    
    echo "\n";
    
    $timeSlotMap = array(
                    0 => "8.15 - 9.45",
                    1 => "10.00 - 11.30",
                    2 => "11.30 - 12.30",
                    3 => "12.30 - 14.00",
                    4 => "14.15 - 15.45",
                    5 => "16.00 - 17.30",
                    6 => "17.30 - open end"
                    );
    
    $javascriptTags = array();
    
    //debugArray($database);
    
    for($timeSlot = 0; $timeSlot < 7; ++$timeSlot){
        
        if($timeSlot == $highlightSlot[0]){
            echo "<div class=\"Timeslot now\">".$timeSlotMap[$timeSlot]."</div>\n";
        }else{
            echo "<div class=\"Timeslot\">".$timeSlotMap[$timeSlot]."</div>\n";
        }
        
        for($daySlot = 0; $daySlot < 7; ++$daySlot ){
                
                $dayString = "day_".$dayNumbers[$daySlot];
                
                echo "<div class=\"Cell\">\n";
                
                $tagWritten = false;
                
                //Search information which has to fit into the corresponding slot
                foreach($database as $event){
                    
                    if($event["day"] == $dayString){
                        
                        $timeSlots = timeSorter($event["startTime"], $event["endTime"]);
                        
                        if(in_array($timeSlot, $timeSlots)){
                            $identifier = $daySlot."_".$timeSlot;
                            
                            if($timeSlot == $highlightSlot[0]){
                                if(!$tagWritten){
                                    echo "<div id=\"".$identifier."\" class=\"events now\">";
                                    $tagWritten = true;
                                }
                            }else{
                                if(!$tagWritten){
                                    echo "<div id=\"".$identifier."\" class=\"events\">";
                                    $tagWritten = true;
                                }
                            }
                            
                            foreach($timeSlots as $slot){
                                if($slot == $timeSlot){
                                    
                                    if( !in_array($identifier, $javascriptTags, true) ){
                                        $javascriptTags[] = $identifier;
                                    }
                                    
                                    echo $event["name"]."<br>".$event["startTime"]."Uhr - ".$event["endTime"]."Uhr<br>"."Raum: ".$event["house"]."/".$event["room"]."#";
                                }
                            }
                        }
                    }
                }
            
                if($tagWritten){
                    echo "</div>\n";
                }
                echo "</div>\n";
        }
        
        echo "\n";
    }

    echo "<div id=\"tagContainer\">";
    foreach($javascriptTags as $tag){
        echo $tag."#";
    }
    echo "</div></div>\n";

}

/*Creates a list of events which will take place on the current day.*/
function createDayView($timestamp){

    $datestring  = date('d', $timestamp).". ".englishMonthToGermanMonth(date('F', $timestamp))." ".date('Y', $timestamp);
    $timeSlotMap = array(
                0 => "8.15 - 9.45",
                1 => "10.00 - 11.30",
                2 => "11.30 - 12.30",
                3 => "12.30 - 14.00",
                4 => "14.15 - 15.45",
                5 => "16.00 - 17.30",
                6 => "17.30 - ..."
                );
                
    $eventTags      = array();
    $locationTags   = array();
    
    $database   = queryEventsWithinDay(XML_SRC , date('Y', $timestamp), date('n', $timestamp), date('j', $timestamp)); //Call to XML file

    $now  = date('G:i', $timestamp);
    $then = date('G:i', $timestamp + 60);
    
    $highlightSlot = timeSorter($now, $then);
    
    echo "<div class=\"agenda day\"><div class=\"headline\">Veranstaltungen</div>".$datestring."</div>";
    echo "<div class=\"agenda body\">";
    echo "<div class=\"subline time\">Uhrzeit</div><div class=\"subline event\">Veranstaltung</div><div class=\"subline location\">Raum</div>\n";
    
    for($timeSlot = 0; $timeSlot < 7; ++$timeSlot){
    
        $idCounter = 0;
    
        if($timeSlot == $highlightSlot[0]){
            echo "<div class=\"cell time now\">".$timeSlotMap[$timeSlot]."</div><div class=\"cell event\">";
        }else{
            echo "<div class=\"cell time\">".$timeSlotMap[$timeSlot]."</div><div class=\"cell event\">";
        }
        
        $tagWritten = false;
        
        if($database !== -1){
        
            foreach($database as $event){
                
                $timeSlots = timeSorter($event["startTime"], $event["endTime"]);
                
                if(in_array($timeSlot, $timeSlots)){
                    $eventIdentifier = "event_".$timeSlot;
                    
                    if($timeSlot == $highlightSlot[0]){
                        if(!$tagWritten){
                        echo "<div id=\"".$eventIdentifier."\" class=\"events now\">";
                        $tagWritten = true;
                    }
                    }else{
                        if(!$tagWritten){
                        echo "<div id=\"".$eventIdentifier."\" class=\"events\">";
                        $tagWritten = true;
                        }
                    }
                
                    foreach($timeSlots as $slot){
                        if($slot == $timeSlot){
                            ++$idCounter;
                            
                            if( !in_array($eventIdentifier, $eventTags, true) ){
                                $eventTags[] = $eventIdentifier;
                            }
                            
                            echo $event["name"]."|".$idCounter."#";
                        }
                    }
                }
            }
        }
        
        if($tagWritten){
            echo "</div>";
        }
        
        $idCounter = 0;
        
        echo "</div><div class=\"cell location\">";
        
        $tagWritten = false;
        
        if($database !== -1){
        
            foreach($database as $event){
                $timeSlots = timeSorter($event["startTime"], $event["endTime"]);
                
                if(in_array($timeSlot, $timeSlots)){
                    $locationIdentifier = "location_".$timeSlot;
                    
                    if($timeSlot == $highlightSlot[0]){
                        if(!$tagWritten){
                        echo "<div id=\"".$locationIdentifier."\" class=\"events now\">";
                        $tagWritten = true;
                    }
                    }else{
                        if(!$tagWritten){
                        echo "<div id=\"".$locationIdentifier."\" class=\"events\">";
                        $tagWritten = true;
                        }
                    }
                    
                    foreach($timeSlots as $slot){
                        if($slot == $timeSlot){
                            ++$idCounter;
                            
                            if( !in_array($locationIdentifier, $locationTags, true) ){
                                $locationTags[] = $locationIdentifier;
                            }
                            
                            echo $event["house"]."/".$event["room"]."|".$idCounter."#";
                        }
                    }
                }
            }
        }
    
        if($tagWritten){
            echo "</div>";
        }
        echo "</div>\n";
    }

    echo "<div id=\"eventContainer\">";
    foreach($eventTags as $tag){
        echo $tag."#";
    }

    if(empty($eventTags)){
        echo "#";
    }

    echo "</div>";

    echo "<div id=\"locationContainer\">";
    foreach($locationTags as $tag){
        echo $tag."#";
    }

    if(empty($locationTags)){
            echo "#";
    }
    
    echo "</div>";
    echo "</div>";

}
?>