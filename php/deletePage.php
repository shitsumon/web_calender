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
    
    function loadDeletePage($timestamp, $scriptname){
        
        echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8;\"/>\n";
        
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\n";
        echo "http://www.w3.org/TR/html4/loose.dtd\">\n";
        
        echo "<html>\n<head>\n";
        //Link css file
        echo "<link rel=\"stylesheet\" href=\"../stylesheets/editstyle.css\" type=\"text/css\">\n";
        
        //Link required javascript files
        echo "<script type=\"text/javascript\" src=\"../javascript/helper.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"../javascript/updateEventToDelete.js\"></script>\n";
    
        echo "</head>\n<body onload=\"javascript:gYears=Array();getAvailableEvents('../xml/data.xml');updateYearSelectBox();\" >\n";
        
        //Headline
        echo "<center><h1 id=\"headEditChoice\">Eine bestehende Veranstaltung löschen</h1></center>\n";
    
        //container
        echo "<div id=\"editcontainer\">\n";
        echo "<form method=\"post\" action=\"".$scriptname."\" onsubmit=\"\">"; 
        //year field
        echo "<fieldset id=\"year_fieldset\" class=\"configelements\" align=\"center\"><legend class=\"editchoice\">Jahr</legend><select name=\"year\" id=\"yearSelect\" onchange=\"updateMonthSelectBox();\" size=\"1\">";
        echo "<option>Jahr</option>";
        echo "</select></fieldset\n>";
        
        //month field
        echo "<fieldset id=\"month_fieldset\" class=\"configelements\" align=\"center\"><legend class=\"editchoice\">Monat</legend><select name=\"month\" id=\"monthSelect\" onchange=\"javascript:updateDaySelectBox();\" size=\"1\">"; 
        echo "<option>Monat</option>";
        echo "</select></fieldset>\n";
        
        //day field
        echo "<fieldset id=\"day_fieldset\" class=\"configelements\" align=\"center\"><legend class=\"editchoice\">Tag</legend><select name=\"day\" id=\"daySelect\" onchange=\"javascript:updateEventSelectBox();\" size=\"1\">";
        echo "<option>Tag</option>";
        echo "</select></fieldset>\n";
        
        //event field
        echo "<fieldset id=\"day_fieldset\" class=\"configelements\" align=\"center\"><legend class=\"editchoice\">Veranstaltung</legend><select name=\"event\" id=\"eventSelect\" onchange=\"javascript:fillEditForm();\" size=\"1\">";
        echo "<option>Veranstaltung</option>";
        echo "</select></fieldset>\n";
        echo "<input id=\"submit\" type=\"submit\" value=\"Veranstaltung löschen\"/>";
        echo "</div>\n"; 
        echo "<input type=\"hidden\" id=\"jsData\" name=\"jsData\"/>";
        echo "</body>\n</html>";
    }
?>