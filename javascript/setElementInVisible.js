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

/*Discloses a hidden DOM element*/
function setFieldVisible(ID){
    document.getElementById(ID).style.display = 'inline';
    document.getElementById(ID).style.visibility = 'visible';
}

/*Conceals a visible DOM element*/
function setFieldInvisible(ID){
    document.getElementById(ID).style.display = 'none';
    document.getElementById(ID).style.visibility = 'hidden';
}

function strToIntMonthMapper(monthValue){
    switch(monthValue){
    case 'Januar':
        return 1;
    break;
    case 'Februar':
        return 2;
    break;
    case 'März':
        return 3;
    break;
    case 'April':
        return 4;
    break;
    case 'Mai':
        return 5;
    break;
    case 'Juni':
        return 6;
    break;
    case 'Juli':
        return 7;
    break;    
    case 'August':
        return 8;
    break;
    case 'September':
        return 9;
    break;
    case 'Oktober':
        return 10;
    break;
    case 'November':
        return 11;
    break;
    case 'Dezember':
        return 12;
    break;
    default:
        alert("Month value is no month");
        return -1;
    break;
    }
}

/* Sets dynamically the number of days for a month, when the year or month is choosen*/
function getDropdownValue(dayID, monthID, yearID){
    var monthValue = document.getElementById(monthID).value;
    var month = strToIntMonthMapper(monthValue);
    
    if(month === -1){
        return;
    }
    
    tmpy = document.getElementById(yearID).value;
    var year  = new Date().getFullYear();
    
    if(tmpy > year){
        var diff = tmpy - year;
        year += diff;
    }
    
    var month = new Date(year, month).getMonth();
    var days  = new Date(year, month, 1, -1).getDate();    
    
    var daySelect    = document.getElementById(dayID);
    var dayValue     = daySelect.value;
    daySelect.length = days;
    
    for(var index = 0; index < days; ++index){
        
        daySelect.options[index] = new Option(index + 1, index + 1, true, true);
    }
    
    daySelect.value = dayValue;
    //month.selectedIndex = 0;
}

/*(De)activates an object (greys it out)*/
function setObjectInActive(checkID, objectID){
    
    if(document.getElementById(checkID).checked){
            document.getElementById(objectID).disabled = true;
    }else{
            document.getElementById(objectID).disabled = false;
    }
}

var gInputArray;

/*Checks all text input objects, if they have some value and are not empty*/
function validateInputElements(inputIDArray, alertFlag){
    
    alertFlag = typeof alertFlag === 'undefined' ? false : alertFlag;
    
    if(gInputArray !== undefined && gInputArray.length > 0){
        for(var index = 0; index < gInputArray.length; ++index){
            
            document.getElementById(gInputArray[index]).style.border='';
        }
    }

    gInputArray = inputIDArray;
    
    for(var index = 0; index < inputIDArray.length; ++index){
            var currentInputObject = document.getElementById(inputIDArray[index]);
            
            if(currentInputObject.value == ""){
                    currentInputObject.style.border='1px solid red';
                    alertFlag = true;
            }
    }

    if(alertFlag){
            alert("Es fehlen Informationen in den Textfeldern, oder Angaben sind fehlerhaft.\nBitte überprüfen sie die eingetragenen Werte!");
            return false;
    }else{
        return true;
    }
}

/*Checks start- and endtime values for correctness*/
function validateTimeValues(startHourID, endHourID, startMinuteID, endMinuteID, alertFlag){
    
    alertFlag = typeof alertFlag !== 'undefined' ? alertFlag : false;
    
    document.getElementById(endMinuteID).style.border = '';
    document.getElementById(endHourID).style.border = '';
    
    var startHour = parseInt(document.getElementById(startHourID).value);
    var endHour   = parseInt(document.getElementById(endHourID).value);
    
    if(endHour < startHour){
        
        document.getElementById(endHourID).style.border = '1px solid red';
        document.getElementById(endMinuteID).style.border = '1px solid red';
        
        if(alertFlag){
            alert("Endzeit des Events liegt vor dessen Startzeit!");
        }        
        
        return false;
    }else if(endHour == startHour){
        
            var startMinute = parseInt(document.getElementById(startMinuteID).value);
            var endMinute   = parseInt(document.getElementById(endMinuteID).value);
            
            if(endMinute < startMinute){
                document.getElementById(endHourID).style.border   = '1px solid red';
                document.getElementById(endMinuteID).style.border = '1px solid red';
                
                if(alertFlag){
                    alert("Endzeit des Events liegt vor dessen Startzeit!");
                }
                
                return false;
            }
    }

    return true;
}

/*Validates start and enddates of an event for their right temporal succession*/
function startEndDateValidation(startYearID, startMonthID, startDayID, endYearID, endMonthID, endDayID, radio1ID, radio2ID, alertFlag){
    
    document.getElementById(endYearID).style.border = '';
    document.getElementById(startYearID).style.border = '';
    document.getElementById(endMonthID).style.border = '';
    document.getElementById(startMonthID).style.border = '';
    document.getElementById(endDayID).style.border = '';
    document.getElementById(startDayID).style.border = '';
    
    if(!document.getElementById(radio2ID).checked){
            return true;
    }
    
    alertFlag = alertFlag !== 'undefined' ? alertFlag : false;
    
    var startDay    = parseInt(document.getElementById(startDayID).value);
    var endDay      = parseInt(document.getElementById(endDayID).value);
    
    var startMonth  = strToIntMonthMapper(document.getElementById(startMonthID).value);
    var endMonth    = strToIntMonthMapper(document.getElementById(endMonthID).value);
    
    var startYear   = parseInt(document.getElementById(startYearID).value);
    var endYear     = parseInt(document.getElementById(endYearID).value);
 
    if(endYear < startYear){
        
        document.getElementById(endDayID).style.border      = '1px solid red';
        document.getElementById(endMonthID).style.border    = '1px solid red';
        document.getElementById(endYearID).style.border     = '1px solid red';
        
        if(alertFlag){
            alert("Enddatum des Events liegt vor dem Startdatum!");
        }
    
        return false;
    }else if(endYear == startYear){
        
        if(endMonth < startMonth){
            
            document.getElementById(endDayID).style.border      = '1px solid red';
            document.getElementById(endMonthID).style.border    = '1px solid red';
            document.getElementById(endYearID).style.border     = '1px solid red';
            
            if(alertFlag){
                alert("Enddatum des Events liegt vor dem Startdatum!");
            }
        
            return false;            
        }else if(endMonth == startMonth){
            
            if(endDay < startDay){
                
                document.getElementById(endDayID).style.border      = '1px solid red';
                document.getElementById(endMonthID).style.border    = '1px solid red';
                document.getElementById(endYearID).style.border     = '1px solid red';
                
                if(alertFlag){
                    alert("Enddatum des Events liegt vor dem Startdatum!");
                }
        
                return false;            
            }else if(endDay == startDay){
                //This would be a single event
                document.getElementById(radio2ID).checked = false;
                document.getElementById(radio1ID).checked = true;
                setFieldInvisible('recurrences', radio1ID);
            }
        }
    }
 
    return true;
}

/*wraps all validation functions to call them conviniently when submitting form*/
function validationWrapper(){
        
    if( !validateInputElements(Array('name_input', 'house_input', 'room_input'), false) ){
            return false;
    }
    
    if( !validateTimeValues('starthour', 'endhour', 'startminute', 'endminute', true) ){
            return false;
    }

    if( !startEndDateValidation('year_dropdown1', 'month_dropdown1', 'day_dropdown1', 'year_dropdown2', 'month_dropdown2', 'day_dropdown2', 'choice1', 'choice2', true)){
            return false;
    }

    return true;
}

function disableRecurrenceField(ID){
        var recurrenceValue = document.getElementById(ID).value;
        
        if(recurrenceValue === 'täglich'){
            
    }
    
    switch(recurrenceValue){
        case 'täglich':
            document.getElementById('day_dropdown2').disabled   = false;
            document.getElementById('month_dropdown2').disabled = false;
            document.getElementById('year_dropdown2').disabled  = false;
        break;
        case 'wöchentlich':
            document.getElementById('day_dropdown2').disabled   = false;
            document.getElementById('month_dropdown2').disabled = false;
            document.getElementById('year_dropdown2').disabled  = false;
        break;
        case 'monatlich':
            document.getElementById('day_dropdown2').disabled   = true;
            document.getElementById('month_dropdown2').disabled = false;
            document.getElementById('year_dropdown2').disabled  = false;
        break;
        case 'jährlich':
            document.getElementById('day_dropdown2').disabled   = true;
            document.getElementById('month_dropdown2').disabled = true;
            document.getElementById('year_dropdown2').disabled  = false;
        break;
        default:
            document.getElementById('day_dropdown2').disabled   = false;
            document.getElementById('month_dropdown2').disabled = false;
            document.getElementById('year_dropdown2').disabled  = false;
            alert("Ausgewählter Wert ist nicht Teil der Auswahl!");
        break;
        }
}