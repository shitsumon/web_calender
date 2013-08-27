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

/**
 *Struct for an event
 */
function sEvent(name, startTime, endTime, house, room, link, recurrence){
    this.name = name;
    this.startTime = startTime;
    this.endTime = endTime;
    this.house = house;
    this.room = room;
    this.link = link;
    this.recurrence = recurrence;
}

/**
 *Struct for an event 
 *+ it's dates values
 */
function sChoosenEvent(){
    this.year  = "";
    this.month = "";
    this.day = "";
    
    this.event = new sEvent();
}

/**
 *Struct for a day
 */
function sDay(name){
    this.events = new Array();
    this.name = name;
}

/**
 *Struct for a month
 */
function sMonth(name){
    this.days = new Array();
    this.name = name;
}

/**
 *Struct for a year
 */
function sYear(name){
    this.months = new Array();
    this.name = name;
}

var gChoosenEvent = new sChoosenEvent(); //holds all information for edit form

var gYears = Array(); //global event structure

var gYearValue  = ""; //stores selected year value
var gMonthValue = ""; //stores selected month value
var gDayValue   = ""; //stores selected day value
var gEventValue = ""; //stores selected event value

/**
 *Helper function to extract month array
 */
function getMonthArrayByName(name, array){
    var newArray;
    for(index = 0; index < array.length; ++index){
        if(array[index].name === name){
            newArray = array[index].months;
            break;
        }
    }
    return newArray;
}

/**
 *Helper function to extract day array
 */
function getDayArrayByName(name, array){
    var newArray;
    for(index = 0; index < array.length; ++index){
        if(array[index].name === name){
            newArray = array[index].days;
            break;
        }
    }
    return newArray;
}

/**
 *Helper function to extract event array
 */
function getEventArrayByName(name, array){
    var newArray;
    for(index = 0; index < array.length; ++index){
        if(array[index].name === name){
            newArray = array[index].events;
            break;
        }
    }
    return newArray;
}

/**
 *Helper function to clean array from
 *'#text' elements.
 */
function cleanArray(arrayToClean){
    var cleanedArray  = Array();
    for(index = 0; index < arrayToClean.length; ++index){
        if(arrayToClean[index].name !== "#text"){
            cleanedArray.push(arrayToClean[index]);
        }
    }
    return cleanedArray;
}

/**
 *Helper which disables or enables a
 *select box.
 */
function setSelectBoxInActive(id, value, identifier){        
    if(value === identifier){
        document.getElementById(id).disabled = true;
        return true;
    }else{
        document.getElementById(id).disabled = false;
        return false;
    }
}

/**
 *Helper function which sets values 
 *of passed select box object.
 */
function setSelectBoxValues(id, defaultValue, inputArray, rplValue, appValue, trltFlag){
 
    rplValue = typeof(rplValue) === 'undefined' ? "" : rplValue;
    appValue = typeof(appValue) === 'undefined' ? "" : appValue;
    trltFlag = typeof(trltFlag) === 'undefined' ? false : trltFlag;
 
    var object = document.getElementById(id);
    object.length = inputArray.length + 1;
    object.options[0] = new Option(defaultValue, defaultValue, true, true);
    
    for(index = 1; index <= inputArray.length; ++index){
        
        var selectBoxValue = inputArray[index - 1].name;
        
        if(rplValue !== ""){
            selectBoxValue = selectBoxValue.replace(rplValue, "");
        }
        
        if(appValue !== ""){
            selectBoxValue = selectBoxValue + appValue;
        }
        
        if(trltFlag){
            selectBoxValue = englishToGermanMonth(selectBoxValue);
        }
        
        object.options[index] = new Option(selectBoxValue, selectBoxValue, true, true);
    }

    object.value = defaultValue;
}

/**
 *Function to retrieve XML content
 */
function getXMLDocument(filename){
    
    var XMLObject = createXMLHTTPObject();
    
    XMLObject.open("GET", filename, false);
    XMLObject.send();
    
    if(XMLObject.status === 200){
        //alert("Returning XML!");
        return XMLObject.responseXML;
    }else if(XMLObject.status === 404){
        alert("XML could not be found!");
    }
}


/**
 *Helper to create XMLHTTP Object,
 *in an platform independent way.
 */
function createXMLHTTPObject(){
    
    //Firefox, IE7, Chrome, Opera, Safari
    if(window.XMLHttpRequest){
         //alert("Returning xmlhttprequest");
         return new XMLHttpRequest();
    }else{//IE5, IE6
         //alert("Returning activexrequest");
         return new ActiveXObject("Microsoft.XMLHTTP");
    }
}

/**
 *Function to extract all
 *events listed in XML file.
 */
function getAvailableEvents(filename){
    var doc = getXMLDocument(filename);
    var root = doc.getElementsByTagName('calendar').item(0);
    
    if(root !== null){
            if(root.hasChildNodes()){
                    var years = root.childNodes; //year nodes
                    
                    for(index = 0; index < years.length; ++index){
                        
                        if(years[index].nodeName.indexOf('year_') > -1){//if node is a real year node
                            
                        var months = years[index].childNodes; //month nodes of current year
                        
                        for(index2 = 0; index2 < months.length; ++index2){
                                
                                if(months[index2].nodeName !== '#text'){
                                        var month = months[index2];
                                       
                                        if(month.childNodes.length <= 1){continue;}
                                        
                                        var hasYear = false;
                                        
                                        for(searchIndex = 0; searchIndex < gYears.length; ++searchIndex){
                                                if(gYears[searchIndex].name == years[index].nodeName){
                                                        hasYear = true;
                                                }
                                        }
                                        
                                        if(!hasYear){
                                            gYears.push(new sYear(years[index].nodeName)); //Add year to global array                                           
                                        }
                                    
                                        monthHasNoChildren = true;
                                        for(searchIndex = 0; searchIndex < month.childNodes.length; ++searchIndex){
                                                
                                                if(month.childNodes[searchIndex].hasChildNodes()){
                                                    monthHasNoChildren = false;
                                                    break;
                                                }
                                        }
                                        
                                        if(monthHasNoChildren){continue;}                                    
                                    
                                        for(index3 = 0; index3 < gYears.length; ++index3){
                                            
                                            if(gYears[index3].name === years[index].nodeName){
                                                
                                                gYears[index3].months.push(new sMonth(month.nodeName));
                                                
                                                for(index4 = 0; index4 < month.childNodes.length; ++index4){
                                                
                                                    var day = month.childNodes[index4];
                                                    
                                                    for(index5 = 0; index5 < gYears[index3].months.length; ++index5){
                                                            
                                                            if(day.hasChildNodes()){
                                                            
                                                            if(gYears[index3].months[index5].name === month.nodeName){
                                                                
                                                                gYears[index3].months[index5].days.push(new sDay(day.nodeName));
                                                            }
                                                            
                                                            for(index6 = 0; index6 < day.childNodes.length; ++index6){
                                                            
                                                                var event = day.childNodes[index6];
                                                                var name, st, et, house, room, link, recurrence;
                                                                for(index8 = 0; index8 < event.childNodes.length; ++index8){
                                                                    
                                                                    switch(event.childNodes[index8].nodeName){                                                                                    
                                                                        case "name":
                                                                            name = event.childNodes[index8].textContent;
                                                                        break;
                                                                        case "startTime":
                                                                            st = event.childNodes[index8].textContent;
                                                                        break;
                                                                        case "endTime":
                                                                            et = event.childNodes[index8].textContent;
                                                                        break;
                                                                        case "house":
                                                                            house = event.childNodes[index8].textContent;
                                                                        break;
                                                                        case "room":
                                                                            room = event.childNodes[index8].textContent;
                                                                        break;
                                                                        case "link":                                                                                       
                                                                            if(event.childNodes[index8].firstChild !== null){
                                                                                link = event.childNodes[index8].textContent;
                                                                            }                                                                                        
                                                                        break;
                                                                        default:
                                                                        alert("Unknown Node: " + event.childNodes[index8].nodeName);
                                                                        break;
                                                                    }
                                                                }
                                                                
                                                                    recurrence = event.attributes[0].textContent;

                                                                    var dontWrite = false;

                                                                    for(index7 = 0; index7 < gYears[index3].months[index5].days.length; ++index7){
                                                                        if(gYears[index3].months[index5].days[index7].name === day.nodeName){                                                                            
                                                                            for(test = 0; test < gYears[index3].months[index5].days[index7].events.length; ++test){
                                                                                    
                                                                                    if(gYears[index3].months[index5].days[index7].events[test].recurrence=== recurrence){
                                                                                       dontWrite = true;
                                                                                    }
                                                                            }                                                                            
                                                                        }
                                                                    }
                                                            
                                                                    if(dontWrite){
                                                                        continue;
                                                                    }else{
                                                                        for(index7 = 0; index7 < gYears[index3].months[index5].days.length; ++index7){
                                                                            if(gYears[index3].months[index5].days[index7].name === day.nodeName){
                                                                                gYears[index3].months[index5].days[index7].events.push(new sEvent(name, st, et, house, room, link, recurrence));
                                                                                break;
                                                                            }
                                                                        }                                                                        
                                                                    }
                                                                
                                                                    link = name = st = et = house = room = link = recurrence = "";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                   }
                            }                            
                        }
                    }
                    
            }else{
                alert("root has no nodes");
            }
    }
}

/**
 *Updates the year select box
 *with the years, in which
 *events take place.
 */
function updateYearSelectBox(){
    
    setSelectBoxValues('yearSelect', "Jahr", gYears, "year_", "", false);
}

/**
 *Updates the month select box
 *with the months, in which
 *events take place.
 */
function updateMonthSelectBox(){
    
    var yearSelectBoxValue = document.getElementById('yearSelect').value;
    
    if(setSelectBoxInActive( 'monthSelect', yearSelectBoxValue, "Jahr" )){return;}
        
    var yearValue   = gYearValue = "year_" + yearSelectBoxValue;
    var months      = getMonthArrayByName(yearValue, gYears);

    setSelectBoxValues('monthSelect', "Monat", months, "", "", true);
}

/**
 *Updates the day select box
 *with the days, in which
 *events take place.
 */
function updateDaySelectBox(){
    
    monthValue = document.getElementById('monthSelect').value;
    
    if( setSelectBoxInActive('daySelect', monthValue, "Monat") ){return;}
    
    var monthSelectBoxValue = gMonthValue = germanToEnglishMonth(monthValue);
    var months              = getMonthArrayByName(gYearValue, gYears);
    var days                = getDayArrayByName(monthSelectBoxValue, months);
    var cleanedDays         = cleanArray(days);
   
    setSelectBoxValues('daySelect', "Tag", cleanedDays, "day_", ".", false);
}

/**
 *Updates the event select box
 *with the events, in which
 *events take place.
 */
function updateEventSelectBox(){
    
    var daySelectBoxValue = document.getElementById('daySelect').value;
    
    if( setSelectBoxInActive('eventSelect', daySelectBoxValue, "Tag") ){return;}
    
    gDayValue = "day_" + daySelectBoxValue.replace(".","");
    
    var months        = getMonthArrayByName(gYearValue, gYears);
    var days          = getDayArrayByName(gMonthValue, months);
    var events        = getEventArrayByName(gDayValue, days);
    var cleanedEvents = cleanArray(events);
    
    setSelectBoxValues('eventSelect', "Veranstaltung", cleanedEvents, "", "", false);
}

function fillEditForm(){
    
    var eventSelectBoxValue = document.getElementById('eventSelect').value;
    
    if(eventSelectBoxValue === "Veranstaltung"){return;}
    
    gChoosenEvent.year  = gYearValue;
    gChoosenEvent.month = gMonthValue;
    gChoosenEvent.day   = gDayValue;
    
    var months          = getMonthArrayByName(gYearValue, gYears);
    var days            = getDayArrayByName(gMonthValue, months);
    var events          = getEventArrayByName(gDayValue, days);
    var cleanedEvents   = cleanArray(events);
    
    for(index = 0; index < cleanedEvents.length; ++index){
        
        if(eventSelectBoxValue === cleanedEvents[index].name){
                
            gChoosenEvent.event = cleanedEvents[index];
            break;
        }
    }

    passValuesToFormObjects();
}

function passValuesToFormObjects(){

        document.getElementById('name_input').value = gChoosenEvent.event.name;
        
        if(typeof(gChoosenEvent.event.link) !== 'undefined'){
            document.getElementById('link_input').value = gChoosenEvent.event.link;
        }

        var startTime = gChoosenEvent.event.startTime;
        var endTime = gChoosenEvent.event.endTime;        
        
        if(startTime === "8:15" && endTime === "17:30"){
            document.getElementById('wholeday').checked = true;
            setObjectInActive('wholeday','starthour');
            setObjectInActive('wholeday','endhour');
            setObjectInActive('wholeday','startminute');
            setObjectInActive('wholeday','endminute');
        }else{
            document.getElementById('wholeday').checked = false;
            setObjectInActive('wholeday','starthour');
            setObjectInActive('wholeday','endhour');
            setObjectInActive('wholeday','startminute');
            setObjectInActive('wholeday','endminute');
            
            var startTimeArray  = startTime.split(":");
            var endTimeArray    = endTime.split(":");
            
            document.getElementById('starthour').value      = startTimeArray[0];
            document.getElementById('startminute').value    = startTimeArray[1];
            
            document.getElementById('endhour').value    = endTimeArray[0];
            document.getElementById('endminute').value  = endTimeArray[1];
        }
        
        document.getElementById('house_input').value    = gChoosenEvent.event.house;
        document.getElementById('room_input').value     = gChoosenEvent.event.room;

        var recuObject;
        if(gChoosenEvent.event.recurrence.indexOf("single") === -1){
            recuObject = parseRecurrenceValue(gChoosenEvent.event.recurrence);
            
            document.getElementById('day_dropdown1').value   = recuObject.startDay;
            document.getElementById('month_dropdown1').value = intToMonth(recuObject.startMonth, 1);
            document.getElementById('year_dropdown1').value  = recuObject.startYear;
            
            document.getElementById('day_dropdown2').value   = recuObject.endDay;
            document.getElementById('month_dropdown2').value = intToMonth(recuObject.endMonth, 1);
            document.getElementById('year_dropdown2').value  = recuObject.endYear;
            
            switch(recuObject.recurrence){
             case 'daily':
             document.getElementById('kind_of_recurrence').value = "täglich";
             disableRecurrenceField('kind_of_recurrence');
             break;
             case 'weekly':
             document.getElementById('kind_of_recurrence').value = "wöchentlich";
             disableRecurrenceField('kind_of_recurrence');
             break;
             case 'monthly':
             document.getElementById('kind_of_recurrence').value = "monatlich";
             disableRecurrenceField('kind_of_recurrence');
             break;
             case 'yearly':
             document.getElementById('kind_of_recurrence').value = "jährlich";
             disableRecurrenceField('kind_of_recurrence');
             break;
             default:
             break;
            }
            
            document.getElementById('choice2').checked = true;
            setFieldVisible('recurrences');
        }else{
            document.getElementById('day_dropdown1').value   = gChoosenEvent.day.replace("day_","");
            document.getElementById('month_dropdown1').value = englishToGermanMonth(gChoosenEvent.month);
            document.getElementById('year_dropdown1').value  = gChoosenEvent.day.replace("year_","");
            
            document.getElementById('choice1').checked = true;
            setFieldInvisible('recurrences');
        }
        
        document.getElementById('jsData').value = gChoosenEvent.event.recurrence;
        
        showObject('configcontainer');
        showObject('headEditChoosen');
        hideObject('editcontainer');
        hideObject('headEditChoice');
}

function parseRecurrenceValue(rawString){
    
    var firstSplit  = rawString.split("#");
    
    var startDate   = firstSplit[2].split("|");
    var endDate     = firstSplit[3].split("|");
    
    return { recurrence:firstSplit[1], 
             startDay:startDate[0],
             startMonth:startDate[1],
             startYear:startDate[2],
             endDay:endDate[0],
             endMonth:endDate[1],
             endYear:endDate[2]};
}