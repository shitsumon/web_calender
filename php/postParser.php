<?php
    /*
    * Copyright (c) 2012/2013 Michael Flau <michael@flau.net>
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
        
    function parsePostData($post){
        //debugArray($post);
        
        //extract eventdata from $post array
        $eventdata = array("name"       => $post["name"],
                           "link"       => $post["link"],
                           "house"      => $post["house"],
                           "room"      =>  $post["room"]);
        
        //Check if event got a fixed timeframe or if it is an event which runs the whole day
        if(array_key_exists ("wholeday", $post)){
            
                $eventdata["startTime"] = "8:15";
                $eventdata["endTime"]   = "17:30";
        }else{
                $eventdata["startTime"] = $post["starthour"].":".$post["startminute"];
                $eventdata["endTime"]   = $post["endhour"].":".$post["endminute"];
        }
        
        //extract time information to create the XML node at the right place
        $startYear  = intVal($post["startyear"]);
        $startMonth = germanMonthToEnglishMonth($post["startmonth"]);
        $startDay   = intVal($post["startday"]);
        
        //Start different calls depending on the recurrence state of
        //the event to write into the XML file
        if($post["recurrence_choice"] == "single_event"){
            
            addCalenderEvent(XML_SRC, $startYear, $startMonth, $startDay, $eventdata);
            header(JUMPBACK_URL);
            
        }else if($post["recurrence_choice"] == "multiple_event"){
            
            //echo ($startMonth);
            
            //combine startdate info
            $startDate = array('startDay' => $startDay,
                               'startMonth'=> string2NumberMonthMapper(strToLower($startMonth)),
                               'startYear' => $startYear);
            
            //extract enddate info
            //echo($post["endyear"]);
            $endYear = intVal($post["endyear"]);
            
            if(is_int($post["endmonth"])){
                $endMonth = $post["endmonth"];
            }else{
                //echo($post["endmonth"]);
                $endMonth = string2NumberMonthMapper(strToLower($post["endmonth"]));
            }
        
            $endDay = intVal($post["endday"]);
            
            //combine enddate info
            $endDate =   array('endDay' => $endDay,
                               'endMonth'=> $endMonth,
                               'endYear' => $endYear);
            
            $kind_of_recurrence = 0;
            
            switch($post["kind_of_recurrence"]){
            case "täglich":
                addRecurringDailyEvents($startDate, $endDate, $eventdata, 1);
                header(JUMPBACK_URL);
            break;
            case "wöchentlich":
                
                if(($endDate['endDay']    - $startDate['startDay'])    >= 7 ||
                   ($endDate['endMonth']  - $startDate['startMonth'])  >= 1 ||
                   ($endDate['endYear']   - $startDate['startYear'])   >= 1){
                    addRecurringDailyEvents($startDate, $endDate, $eventdata, 7);
                    header(JUMPBACK_URL);
                }else{
                    
                    echo("Events are not even one week apart from each other!<br/>");
                }
            
            break;
            case "monatlich":
                
                if(($endDate['endMonth']  - $startDate['startMonth'])  >= 1 ||
                   ($endDate['endYear']   - $startDate['startYear'])   >= 1){
                       
                    addRecurringMonthlyEvents($startDate, $endDate, $eventdata);
                    header(JUMPBACK_URL);
                }else{
                    
                    echo("Events are not even one month apart from each other!<br/>");
                }
            break;
            case "jährlich":
                
                if(($endDate['endYear']   - $startDate['startYear'])   >= 1){
                       
                    addRecurringYearlyEvents($startDate, $endDate, $eventdata);
                    header(JUMPBACK_URL);
                }else{
                    
                    echo("Events are not even one year apart from each other!<br/>");
                }
            break;
            default:
                echo("bad recurrence option!");
            break;                
            }
        }
    }
?>