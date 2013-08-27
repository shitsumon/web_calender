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

function germanStrToIntMonthMapper(monthValue){
    switch(monthValue.toLowerCase()){
    case 'januar':
    case 'january':
        return 1;
    break;
    case 'februar':
    case 'february':
        return 2;
    break;
    case 'märz':
    case 'march':
        return 3;
    break;
    case 'april':
        return 4;
    break;
    case 'mai':
    case 'may':
        return 5;
    break;
    case 'juni':
    case 'june':
        return 6;
    break;
    case 'juli':
    case 'july':
        return 7;
    break;    
    case 'august':
        return 8;
    break;
    case 'september':
        return 9;
    break;
    case 'oktober':
    case 'october':
        return 10;
    break;
    case 'november':
        return 11;
    break;
    case 'dezember':
    case 'december':
        return 12;
    break;
    default:
        alert("Month value is no month");
        return -1;
    break;
    }
}

function intToMonth(monthValue, returnGerman){
    
    monthValue   = parseInt(monthValue);
    returnGerman = typeof(returnGerman) === 'undefined' ? false : true;
    
    switch(monthValue){
    case 1:
        return ("Januar" + (returnGerman ? "" : "y"));
    break;
    case 2:
        return ("Februar" + (returnGerman ? "" : "y")); 
    break;
    case 3:
        return ("M" + (returnGerman ? "ärz" : "arch"));
    break;
    case 4:
        return "April";
    break;
    case 5:
        return ("Ma" + (returnGerman ? "i" : "y"));
    break;
    case 6:
        return ("Jun" + (returnGerman ? "i" : "e"));
    break;
    case 7:
        return ("Jul" + (returnGerman ? "i" : "y"));
    break;
    case 8:
        return "August";
    break;
    case 9:
        return "September";
    break;
    case 10:
        return ("O" + (returnGerman ? "ktober" : "ctober"));
    break;
    case 11:
        return "November";
    break;
    case 12:
        return ("De" + (returnGerman ? "zember" : "cember"));
    break;
    default:
        alert("Passed value is no month!\nOnly values between 1 and 12 are valid!");
        return -1;
    break;
    }
}

function germanToEnglishMonth(month){

    switch(month.toLowerCase()){
    case 'januar':
        return 'january';
    break;
    case 'februar':
        return 'february';
    break;
    case 'märz':
        return 'march';
    break;
    case 'april':
        return 'april';
    break;
    case 'mai':
        return 'may';
    break;
    case 'juni':
        return 'june';
    break;
    case 'juli':
        return 'july';
    break;
    case 'august':
        return 'august';
    break;
    case 'september':
        return 'september';
    break;
    case 'oktober':
        return 'october';
    break;
    case 'november':
        return 'november';
    break;
    case 'dezember':
        return 'december';
    break;
    default:
        alert("Invalid string: " + month);
        return "Monat";
    break;
    }
}

function englishToGermanMonth(month){

    switch(month.toLowerCase()){
    case 'january':
        return 'Januar';
    break;
    case 'february':
        return 'Februar';
    break;
    case 'march':
        return 'März';
    break;
    case 'april':
        return 'April';
    break;
    case 'may':
        return 'Mai';
    break;
    case 'june':
        return 'Juni';
    break;
    case 'july':
        return 'Juli';
    break;
    case 'august':
        return 'August';
    break;
    case 'september':
        return 'September';
    break;
    case 'october':
        return 'Oktober';
    break;
    case 'november':
        return 'November';
    break;
    case 'december':
        return 'Dezember';
    break;
    default:
        alert("Invalid string: " + month);
        return "Monat";
    break;
    }
}

function hideObject(id){
    document.getElementById(id).style.display = 'none';
}

function showObject(id){
    document.getElementById(id).style.display = 'block';
}