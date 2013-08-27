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

function loadConfigPage($timestamp, $scriptname){
    
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8;\"/>\n";
        
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\n";
    echo "http://www.w3.org/TR/html4/loose.dtd\">\n";
    echo "<html>\n<head>\n";
        //Link css file
    echo "<link rel=\"stylesheet\" href=\"../stylesheets/configstyle.css\" type=\"text/css\">\n";
        
        //Link required javascript files
    echo "<script type=\"text/javascript\" src=\"../javascript/helper.js\"></script>\n";
    echo "<script type=\"text/javascript\" src=\"../javascript/setElementInVisible.js\"></script>";    
    echo "<form method=\"post\" action=\"".$scriptname."\" onsubmit=\"return validationWrapper()\">";
	//echo "<center>";
	
    //Header
    echo "<center><h1>Eine neue Veranstaltung eintragen</h1></center>";
    
    //container
    echo "<div id=\"configcontainer\">";
    
    //name and link field
    echo "<fieldset id=\"name\" class=\"configelements\" align=\"center\"><legend>Name der Veranstaltung</legend><label>Name:</label> <input type=\"text\" id=\"name_input\" name=\"name\" /><br /><label>Link:</label> <input type=\"text\" name=\"link\" /></fieldset>";
	
    //date field
    echo "<fieldset id=\"date\" 	class=\"configelements\"><legend>Datum</legend>";
    
    echo "<div id=\"dateelements\"><select name=\"startday\" id=\"day_dropdown1\" onchange=\"javascript:startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 1; $index < 32; ++$index){
            echo "<option>".$index."</option>";
    }
    echo "</select>.";  
    
    $months = array("Januar", "Februar", "M&aumlrz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    
    echo "<select  name=\"startmonth\" id=\"month_dropdown1\" onchange=\"javascript:getDropdownValue('day_dropdown1', 'month_dropdown1', 'year_dropdown1'); startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 0; $index < count($months); ++$index){
            echo "<option>".$months[$index]."</option>";
    }
    echo "</select>.";    
    
    $year = date('Y', $timestamp);
    
    echo "<select name=\"startyear\" id=\"year_dropdown1\" onchange=\"javascript:getDropdownValue('day_dropdown1', 'month_dropdown1', 'year_dropdown1'); startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 0; $index < 3; ++$index){
            echo "<option>".($year+$index)."</option>";
    }
    echo "</select></div></fieldset>";
    
    //time field
	echo "<fieldset id=\"time\" class=\"configelements\"><legend>Uhrzeit</legend>";
    
    echo "<div id=\"starttime\"><label>Start:</label> ";
    echo "<select id=\"starthour\" name=\"starthour\" onchange=\"javascript:validateTimeValues('starthour', 'endhour', 'startminute', 'endminute')\" size=\"1\">";
    for($index = 0; $index < 24; ++$index){
            echo "<option>".$index."</option>";
    }
    echo "</select> : ";
    
    echo "<select id=\"startminute\" name=\"startminute\" onchange=\"javascript:validateTimeValues('starthour', 'endhour', 'startminute', 'endminute')\" size=\"1\">";
    for($index = 0; $index < 60; ++$index){
            
            if($index < 10){
                echo "<option>0".$index."</option>";
            }else{
                echo "<option>".$index."</option>";
            }
    }
    echo "</select> Uhr</div><br />";
    
    echo "<div id=\"endtime\"><label>Ende:</label> ";
    echo "<select id=\"endhour\" name=\"endhour\" onchange=\"javascript:validateTimeValues('starthour', 'endhour', 'startminute', 'endminute')\" size=\"1\">";
    for($index = 0; $index < 24; ++$index){
            echo "<option>".$index."</option>";
    }
    echo "</select> : <select id=\"endminute\" name=\"endminute\" onchange=\"javascript:validateTimeValues('starthour', 'endhour', 'startminute', 'endminute')\" size=\"1\">";
    for($index = 0; $index < 60; ++$index){
            
            if($index < 10){
                echo "<option>0".$index."</option>";
            }else{
                echo "<option>".$index."</option>";
            }
    }
    echo "</select> Uhr</div><br /><br />";    
    echo "<label>Ganzt&aumlgig:</label> <input type=\"checkbox\" id=\"wholeday\" name=\"wholeday\" onclick=\"setObjectInActive('wholeday', 'starthour');setObjectInActive('wholeday', 'startminute');setObjectInActive('wholeday', 'endhour');setObjectInActive('wholeday', 'endminute');\" name=\"wholeday[]\" value=\"wholeday\">";
    
    //location field
	echo "</fieldset><fieldset id=\"location\" class=\"configelements\"><legend>Ort</legend><label>Haus:</label> <input type=\"text\" id=\"house_input\" name=\"house\" /><br /><label>Raum:</label> <input type=\"text\" id=\"room_input\" name=\"room\" /></fieldset>";
    
    //kind field
	echo "<fieldset id=\"kind\" class=\"configelements\"><legend>Art des Termins</legend>";
    echo "<label>Einmalige Veranstaltung</label> <input type=\"radio\" checked=\"checked\" id=\"choice1\" onclick=\"javascript:setFieldInvisible('recurrences');\" name=\"recurrence_choice\" value=\"single_event\"><br \>";
    echo "<label>Sich wiederholende Veranstaltung</label> <input type=\"radio\" id=\"choice2\" onclick=\"javascript:setFieldVisible('recurrences');\" name=\"recurrence_choice\" value=\"multiple_event\">";
    
    //hidden recurrences field
    echo "</fieldset><fieldset id=\"recurrences\" class=\"configelements\"><legend>Art der Wiederholungen</legend>";
    echo "<label>Wiederholungen:</label><br \><select id=\"kind_of_recurrence\" name=\"kind_of_recurrence\" onchange=\"javascript:disableRecurrenceField('kind_of_recurrence')\" size=\"1\"><br \>";
    $recurrences = array("t&aumlglich", "w&oumlchentlich", "monatlich", "j&aumlhrlich");
    
    foreach($recurrences as $item){
            echo "<option>".$item."</option>";
    }
    echo "</select><br \><label>Ende der Wiederholungen:</label><br \>";
    
    echo "<select id=\"day_dropdown2\" name=\"endday\" onchange=\"javascript:startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 1; $index < 32; ++$index){
            echo "<option>".$index."</option>";
    }
    
    echo "</select><select id=\"month_dropdown2\" name=\"endmonth\" onchange=\"javascript:getDropdownValue('day_dropdown2', 'month_dropdown2', 'year_dropdown2');startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 0; $index < count($months); ++$index){
            echo "<option>".$months[$index]."</option>";
    }
    echo "</select>.";    
    
    $year = date('Y', $timestamp);
    
    echo "<select id=\"year_dropdown2\" name=\"endyear\" onchange=\"javascript:getDropdownValue('day_dropdown2', 'month_dropdown2', 'year_dropdown2');startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2');\" size=\"1\">";
    for($index = 0; $index < 3; ++$index){
            echo "<option>".($year+$index)."</option>";
    }
    echo "</select>";
    
    echo "</fieldset>";
    echo "<input id=\"submit\" type=\"submit\" value=\"Veranstaltung eintragen\"/>";
	echo "</div>";
	echo "</body>\n</html>";
}

?>
