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

    include("helper.php");
  
	/*
		Function prototype for a XML-query. The query works just for
		a specific type of XML-document. The hierachy of the XML doc-
		ument is as follows:
		
		<calendar>
		 <year_2012>
			 <january>
			 </january>
			 <february>
				 <day_1>
					 <event>
						 <name>CONTENT</name>
						 <link>CONTENT</link>
					 </event>
					 .
					 .
					 .
				 </day_1>
				 .
				 .
				 .
			 </february>
			 .
			 .
			 .
		 </year_2012>
		 <year_2013>
		 </year_2013>
		 </calendar>
		 
		 The query adresses always all event in a particular month.
	 */
	function queryEventsWithinMonth($filename, $year, $month){

		$xmlObject 	  = new SimpleXMLElement($filename, null, true);
		$db 		  = array();
		$addFlag 	  = false;
		$currentMonth = $xmlObject->{"year_".$year}->{monthMapper($month)};

		foreach($currentMonth->children() as $day){

			foreach($day->children() as $event){
				$tmp = array();
                $tmp["day"]         = $day->getName();
				$tmp["name"] 		= $event->name;
				$tmp["startTime"] 	= $event->startTime;
				$tmp["endTime"] 	= $event->endTime;
				$tmp["house"] 		= $event->house;
				$tmp["room"] 		= $event->room;
				$tmp["link"]        = ($event->link == "") ? "###" : $event->link;
                
                $db[] = $tmp;
			}
		}
		
		return $db;
	}
    
    //debugArray(queryEventsWithinMonth("data.xml", 2012, 10));
    
	/*
	  Function which queries events in a seven days radius from the current
	  date. Since the date can be one of seven Weekdays, the days to query
	  are determined dynamicaly by this function call. It returns an array
	  which contains all events which will take place or have already taken
	  place within the time radius.
      
      Input arguments:
      $filename - the file in which the data is stored we need to extract
      $year     - the current year
      $month    - the current month
      $day      - the current day
      
      Return value:
      $db       - contains all events of the specified time radius in an array structure      
	*/
	function queryEventsWithinWeek($filename, $year, $month, $day){
        
        //determine which day of the week it is in order to fetch
        //the other 6 days which belong to the current week
        
        $timestamp  = time();
        $weekday    = date("D", mktime(0,0,0, $month, $day, date("Y", $timestamp)));
        //echo ($weekday);
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
        
		$xmlObject 	= new SimpleXMLElement($filename, null, true);
		$db 		= array();
        
        $lastMonth  = 0;
        $nextMonth  = 0;
        
        if(monthMapper($month) == "january"){ //If current month is january, it's necessary to extract december from last year
                
                $lastMonth = $xmlObject->{"year_".($year - 1)}->{monthMapper($month + 11)};
            
                $nextMonth = $xmlObject->{"year_".$year}->{monthMapper($month + 1)};
        }else if(monthMapper($month) == "december"){ //If current month is december, it's necessary to extract january from next year
                
                $lastMonth = $xmlObject->{"year_".$year}->{monthMapper($month - 1)};
                
                $nextMonth = $xmlObject->{"year_".($year + 1)}->{monthMapper($month - 11)};
        }else{ //If the adjacent months are within the same year we simply take them from same year node as the current month
                
                $lastMonth = $xmlObject->{"year_".$year}->{monthMapper($month - 1)};
                $nextMonth = $xmlObject->{"year_".$year}->{monthMapper($month + 1)};
        }
    
        $thisMonth = $xmlObject->{"year_".$year}->{monthMapper($month)};
        
        foreach($dayNumbers as $dayItem){
        
            $currentDay;
            $strDayItem = "day_".$dayItem;
                    
            //check from which month node we get our information for the actual $dayItem
            if(($dayItem > $day) && (array_search($dayItem, $dayNumbers) < array_search($day, $dayNumbers))){ //Current $dayItem is in last months node
                
                if(!isset($lastMonth->$strDayItem)){
                    continue;
                }
                
                $currentDay = $lastMonth->$strDayItem;
                
            }else if(($dayItem < $day) && (array_search($dayItem, $dayNumbers) > array_search($day, $dayNumbers))){ //Current $dayItem is in next months node
            
                if(!isset($nextMonth->$strDayItem)){
                    continue;
                }  
                
                $currentDay = $nextMonth->$strDayItem;
                
            }else{ //Current $dayItem is in this months node
                
                if(!isset($thisMonth->$strDayItem)){
                    continue;
                }  
                
                $currentDay = $thisMonth->$strDayItem;
            }
               
            foreach($currentDay->children() as $event){
                
                $tmp        		= array();
                
                $tmp["day"]         = $currentDay->getName();
                $tmp["name"]        = $event->name;
                $tmp["startTime"]   = $event->startTime;
                $tmp["endTime"]     = $event->endTime;
                $tmp["house"]       = $event->house;
                $tmp["room"]        = $event->room;
                $tmp["link"]        = ($event->link == "") ? "###" : $event->link;
                
                $db[] = $tmp;
            }
        }
        
        //debugArray($db);
        
        return $db;
    }

    //debugArray(queryEventsWithinWeek("data.xml", 2012, 11, 14));

	/*
		Function prototype for a XML-query. The query works just for
		a specific type of XML-document. The hierachy of the XML doc-
		ument is as follows:
		 
		 <year_2012>
			 <january>
			 </january>
			 <february>
				 <day_1>
					<event>
						<name>CONTENT</name>
						<startTime>CONTENT</startTime>
						<endTime>CONTENT</endTime>
						<house>CONTENT</house>
						<room>CONTENT</room>
						<link>CONTENT</link>
					</event>
					 .
					 .
					 .
				 </day_1>
				 .
				 .
				 .
			 </february>
			 .
			 .
			 .
		 </year_2012>
		 
		 The query adresses always a particular day (current day).
	 */	
	function queryEventsWithinDay($filename, $year, $month, $day){
	
		$xmlObject 	= new SimpleXMLElement($filename, null, true);
		$db 		= array();
		$tmp		= array();
		$addFlag 	= false;
        
		if(!isset($xmlObject->{"year_".$year}->{monthMapper($month)}->{"day_".$day})){
			return -1;
		}
		
		$currentday = $xmlObject->{"year_".$year}->{monthMapper($month)}->{"day_".$day};
		
		foreach($currentday->children() as $event){
            
			$tmp["name"]        = $event->name;
			$tmp["startTime"]   = $event->startTime;
			$tmp["endTime"]     = $event->endTime;
			$tmp["house"]       = $event->house;
			$tmp["room"]        = $event->room;
            $tmp["link"]        = ($event->link == "") ? "###" : $event->link;
			
			$db[] = $tmp;
		}
		
		return $db;		
	}
	
    //debugArray(queryEventsWithinDay("data.xml", 2012, 10, 25));
    
    /*addRecurringDailyEvents()*/
    function addRecurringDailyEvents($startDate, $endDate, $eventData, $dayPeriod){
        
        $period = "";
        
        if($dayPeriod == 1){
            $period = "daily#";
        }else if($dayPeriod == 7){
            $period = "weekly#";
        }
        
        $recurrenceString = randHex()."#".$period.$startDate['startDay']."|".$startDate['startMonth']."|".$startDate['startYear']."#".
                                        $endDate['endDay']."|".$endDate['endMonth']."|".$endDate['endYear'];
        
        addCalenderEvent(XML_SRC, $startDate['startYear'], $startDate['startMonth'], $startDate['startDay'], $eventData, $recurrenceString);
        
        $MAX_MONTH = 12;
        
        $currentDay   = $dayCounter   = $startDate['startDay'];
        $currentMonth = $monthCounter = $startDate['startMonth'];
        $currentYear  = $yearCounter  = $startDate['startYear'];
        
        //echo($currentDay."-".$currentMonth."-".$currentYear."<br/>");
        //echo($endDate['endDay']."-".$endDate['endMonth']."-".$endDate['endYear']."<br/>");
        
        do{
        
            if( ($dayCounter + $dayPeriod) > getNumberOfDaysInMonth($monthCounter, $yearCounter) ){
                
                $dayCounter = ($dayCounter + $dayPeriod) - getNumberOfDaysInMonth($monthCounter, $yearCounter);
                ++$monthCounter;
                
                if( $monthCounter > $MAX_MONTH ){
                    
                    $monthCounter = 1;
                    ++$yearCounter;
                    
                    $currentDay   = $dayCounter;
                    $currentMonth = $monthCounter;
                    $currentYear  = $yearCounter;
                }else{
                    $currentDay   = $dayCounter;
                    $currentMonth = $monthCounter;
                }
            
            }else{
                
                $dayCounter += $dayPeriod;
                $currentDay = $dayCounter;
            }
        
            addCalenderEvent(XML_SRC, $currentYear, $currentMonth, $currentDay, $eventData, $recurrenceString);
            //printf("%d-%s-%d<br/>", $currentDay, monthMapper($currentMonth), $currentYear);
            
        }while(($currentDay + $dayPeriod) < $endDate['endDay'] ||
               $currentMonth != $endDate['endMonth'] ||
               $currentYear  != $endDate['endYear']);
    }
    
    /*addRecurringMonthlyEvents*/
    function addRecurringMonthlyEvents($startDate, $endDate, $eventData){
        
        $recurrenceString = randHex()."#"."monthly#".$startDate['startDay']."|".$startDate['startMonth']."|".$startDate['startYear']."#".
                                        $endDate['endDay']."|".$endDate['endMonth']."|".$endDate['endYear'];
        
        //Add initial event
        addCalenderEvent(XML_SRC, $startDate['startYear'], $startDate['startMonth'], $startDate['startDay'], $eventData, $recurrenceString);
        
        //Add following events
        $MAX_MONTH = 12;
        
        $currentDay   = $startDate['startDay'];
        $currentMonth = $monthCounter = $startDate['startMonth'];
        $currentYear  = $yearCounter  = $startDate['startYear'];
        
        do{
            
            if( ($monthCounter + 1) > $MAX_MONTH ){
                
                $monthCounter = 1;
                ++$yearCounter;
                $currentYear  = $yearCounter;
            }else{
                
                ++$monthCounter;
            }
        
            $maxDays      = getNumberOfDaysInMonth($monthCounter, $yearCounter);
            $currentDay   = $startDate['startDay'] < $maxDays ? $currentDay : $maxDays;
            $currentMonth = $monthCounter;
        
            addCalenderEvent(XML_SRC, $currentYear, $currentMonth, $currentDay, $eventData, $recurrenceString);
            //printf("%d-%d-%d<br/>", $currentDay, monthMapper($currentMonth), $currentYear);
        
        }while($currentMonth != $endDate['endMonth'] ||
               $currentYear  != $endDate['endYear']);
    }
    
    /*addRecurringYearlyEvents()*/
    function addRecurringYearlyEvents($startDate, $endDate, $eventData){
        
        $recurrenceString = randHex()."#"."yearly#".$startDate['startDay']."|".$startDate['startMonth']."|".$startDate['startYear']."#".
                                        $endDate['endDay']."|".$endDate['endMonth']."|".$endDate['endYear'];
        //Add initial event
        addCalenderEvent(XML_SRC, $startDate['startYear'], $startDate['startMonth'], $startDate['startDay'], $eventData, $recurrenceString);
        
        //Add following events
        $currentDay   = $startDate['startDay'];
        $currentMonth = $startDate['startMonth'];
        $currentYear  = $yearCounter  = $startDate['startYear'];
        
        do{
            ++$yearCounter;
            $maxDays      = getNumberOfDaysInMonth($currentMonth, $yearCounter);
            $currentDay   = $startDate['startDay'] < $maxDays ? $currentDay : $maxDays;
            $currentYear  = $yearCounter;
            
            addCalenderEvent(XML_SRC, $currentYear, $currentMonth, $currentDay, $eventData, $recurrenceString);
            //printf("%d-%d-%d<br/>", $currentDay, monthMapper($currentMonth), $currentYear);
        }while( $currentYear != $endDate['endYear'] );
    }
    
	/*
		This functions adds an event to an existing XML-File.
		For that it requires the filename of the XML-file,
		the year, month and the day in which the event will take place.
		Furthermore the actual information about the event
		(name, statTime, endTime, house, room, link[optional]) are required.

		the event data is saved in an associated array structure:

		$eventdata = array
		(
		name => "Some name",
		startTime => "Some Time",
		endTime => "Some other Time",
		room => "Roomnumber",
		house => "Housenumber or Housename"
		)

	*/    
	function addCalenderEvent($filename, $year, $month, $day, $eventdata, $recurrence = NULL){
  
        if($recurrence == NULL){
            $recurrence = randHex()."#"."single";
        }
  
        //Check if $month is already a string, if not convert it to one
        if(is_int($month)){
                
                $month = monthMapper($month);
        }
  
		//Initialize variables
		$xmlObject = new SimpleXMLElement($filename, null, true);

        //form strings to required layout
		$year = "year_".$year;
		$day  = "day_".$day;

        //check if year node already exists, if not, create a year
        //node for that particular year
        if(! isset($xmlObject->$year)){
            
            $xmlObject->addChild($year);
            
            $monthArray = array("january",
                                "february",
                                "march",
                                "april",
                                "may",
                                "june",
                                "july",
                                "august",
                                "september", 
                                "october",
                                "november",
                                "december");
                                
            foreach($monthArray as $monthTag){
                    $xmlObject->$year->addChild($monthTag);
            }
        }

        //check if event is already in xml file
        if(isset($xmlObject->$year->$month->$day)){
            
            //make it by skimming through each event which takes
            //place on that particular day. If there is an event which matches
            //all critera the function is aborted.
            foreach($xmlObject->$year->$month->$day->children() as $event){
            
                if( $event->name        == $eventdata["name"] &&
                    $event->startTime   == $eventdata["startTime"] &&
                    $event->endTime     == $eventdata["endTime"] &&
                    $event->house       == $eventdata["house"] &&
                    $event->room        == $eventdata["room"]){
                        return;
                }
            }
        }
    
        //echo($year."<br \>");
        //echo($month."<br \>");
        //echo($day."<br \>");
    
		//Check if passed day is already created
        if(!isset($xmlObject->$year->$month->$day)){
        
            $xmlObject->$year->$month->addChild($day);
        }
    
		//get current day which was searched for
        $currentday = $xmlObject->$year->$month->$day;

		//create an event on that particular day
		$event = $currentday->addChild("event");
        
        //assign recurrence type
        $event['recurrence'] = $recurrence;
		//Fill event with data from array structure
		$event->addChild("name"		 , $eventdata["name"]);
		$event->addChild("startTime" , $eventdata["startTime"]);
		$event->addChild("endTime"	 , $eventdata["endTime"]);
		$event->addChild("house"	 , $eventdata["house"]);
		$event->addChild("room"		 , $eventdata["room"]);
		$event->addChild("link"		 , $eventdata["link"]);
		
		//Save changes to disk
		$xmlObject->asXML($filename);
	}
	
	/*
		This function deletes a specific calender event in an existing
		XML-file. For that it requires the filename of the XML-file,
		and the ID of the event.
	*/
	function deleteCalenderEvent($filename, $id){
		$xmlObject = new SimpleXMLElement($filename, null, true);
        
        foreach($xmlObject->children() as $year){
            foreach($year->children() as $month){
                foreach($month->children() as $day){
                    
                        if($day->count() > 0){
                            foreach($day->children() as $event){                                
                                if(strcasecmp($event['recurrence'],$id) == 0){
                                    $dom=dom_import_simplexml($event);
                                    $dom->parentNode->removeChild($dom);   
                                }
                            }
                        }else{
                            continue;
                        }
                }
            }
        }
        
        $xmlObject->asXML($filename);
    }
    
	/*
		This function creates the prototype an XML-file which can
		be interpreted by the calender plugin. It simply creates
		month-tags for the year stated.
	*/
	function createNewXMLFile($filename, $year){
	
		if( file_exists($filename)){
			echo "File already exists, please choose a different filename!";
			return;
		}
	
		$timestamp	  = "<!--This file was automatically created at ".date('d.m.Y G:i:s', time())."-->\n";
		$header 	  = "<?xml version='1.0' encoding=\"UTF-8\" standalone=\"yes\"?>";
		$rootOpenBracket  = "<calendar>";
		$rootCloseBracket = "</calendar>";
		$yearOpenTag  = "<year_".$year.">";
		$yearCloseTag = "</year_".$year.">";
		$body		  = "<january></january>\n
				     <february></february>\n
				     <march></march>\n
				     <april></april>\n
				     <may></may>\n
				     <june></june>\n
				     <july></july>\n
				     <august></august>\n
				     <september></september>\n
				     <october></october>\n
				     <november></november>\n
				     <december></december>";
						 
		$prototype = $timestamp.$header."\n".$rootOpenBracket.$yearOpenTag."\n".$body."\n".$yearCloseTag.$rootCloseBracket;
		
		/*$fileHandle = fopen($filename, 'w');
		
		if(!$fileHandle){
			echo "Bad handle returned!\n";
			return;
		}
		
		fwrite($fileHandle, $prototype);
		fclose($fileHandle);*/
		echo($prototype);
    }
    //createNewXMLFile("test.xml", 2012);
?>
