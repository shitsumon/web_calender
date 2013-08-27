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

var dataArray;

function sDataContainer(day){
    this.counter       = 0;
    this.day           = day;
    this.contentString = new Array();
}

function sMultiDataContainer(){
    this.counter         = 0;
    this.eventCellID     = "";
    this.locationCellID  = "";
    this.eventStrings    = new Array();
    this.locationStrings = new Array();
}

function onLoadStartDayviewSlideShow(Id){

    divID = Id;
    dataArray = getMultipleHTMLObjects(divID);

    if(dataArray === -1){
        alert("global data array is empty!");
        return;
    }

    displayMultipleElementsInTurn();
}

function onLoadStartSlideShow(Id){

    divID = Id;
    dataArray = getHTMLObject(divID);

    if(dataArray === -1){
        return;
    }

    displayTextElementsInTurn();
}

function getHTMLObject(name){

    var test = document.getElementById(name).innerHTML;

    //alert(test);

    if(test === "" || test === "undefined"){
        return -1;
    }

    var tagArray = test.split("#");

    var finalArray = new Array();

    for(index = 0; index < tagArray.length; ++index){
        if(tagArray[index] !== ""){
            finalArray.push(tagArray[index]);
        }
    }

    //alert(finalArray);

    var tmpDataArray = new Array();

    for(index = 0; index < finalArray.length; ++index){

        var dataContainer   = new sDataContainer(finalArray[index]);
        var tmpString       = document.getElementById(finalArray[index]).innerHTML;

        document.getElementById(finalArray[index]).innerHTML = "";

        var tmpArray        = tmpString.split("#");

        for(index2 = 0; index2 < tmpArray.length; ++index2){

            if(tmpArray[index2] !== ""){
                dataContainer.contentString.push(tmpArray[index2]);
            }
        }
        
        tmpDataArray.push(dataContainer);
    }
    
    return tmpDataArray;
}

/*getHTMLObject for elements in multiple cells*/
function getMultipleHTMLObjects(names){

    if(names.length !== 2){
        alert("Length of input array must be exactly 2!");
        return -1;
    }

    var finalContentArray   = new Array();
    var eventItems          = new Array();
    var locationItems       = new Array();

    var tmpEventItems    = document.getElementById(names[0]).innerHTML.split("#");
    var tmpLocationItems = document.getElementById(names[1]).innerHTML.split("#");

    if(eventItems.length !== locationItems.length){
        alert("Length of input arrays is not consistent among them!");
        return -1;
    }

    for(index = 0; index < tmpEventItems.length; ++index){

        if(tmpEventItems[index] !== ""){
            eventItems.push(tmpEventItems[index]);
        }


        if(tmpLocationItems[index] !== ""){
            locationItems.push(tmpLocationItems[index]);
        }
    }

    for(index = 0; index < eventItems.length; ++index){

        finalContentArray.push(new sMultiDataContainer());
        finalContentArray[index].eventCellID     = eventItems[index];
        finalContentArray[index].locationCellID  = locationItems[index];

        var tmpArray  = document.getElementById(eventItems[index]).innerHTML.split("#");
        document.getElementById(eventItems[index]).innerHTML = "";

        var tmpArray2 = document.getElementById(locationItems[index]).innerHTML.split("#");
        document.getElementById(locationItems[index]).innerHTML = "";

        if(tmpArray.length !== tmpArray2.length){
            alert("Cell array length mismatch!");
        }

        for(index2 = 0; index2 < tmpArray.length; ++index2){

            if(tmpArray[index2] !== ""){
                finalContentArray[index].eventStrings.push(tmpArray[index2]);
            }

            if(tmpArray2[index2] !== ""){
                finalContentArray[index].locationStrings.push(tmpArray2[index2]);
            }
        }
    }

    return finalContentArray;
}

//ends here

function displayTextElementsInTurn(){

    for(index = 0; index < dataArray.length; ++index){
        if(dataArray[index].counter >= dataArray[index].contentString.length - 1){
            dataArray[index].counter = 0;
        }else{
            dataArray[index].counter++;
        }

        if(typeof dataArray[index].contentString[dataArray[index].counter] === 'undefined'){
                document.getElementById(dataArray[index].day).innerHTML = '';
        }else{
                document.getElementById(dataArray[index].day).innerHTML = dataArray[index].contentString[dataArray[index].counter];
        }

        
    }

    setTimeout("displayTextElementsInTurn()", 2000);
}

function displayMultipleElementsInTurn(){

    for(index = 0; index < dataArray.length; ++index){
        if(dataArray[index].counter >= dataArray[index].eventStrings.length - 1){

            dataArray[index].counter = 0;
        }else{

            dataArray[index].counter++;
        }

        var rawEventString = dataArray[index].eventStrings[dataArray[index].counter];
        var eventIDNumber  = parseInt(rawEventString.substring(rawEventString.length - 1, rawEventString.length));
        var eventString    = rawEventString.substring(0, rawEventString.length - 2);

        for(index2 = 0; index2 < dataArray[index].locationStrings.length; ++index2){

            var locationIDNumber = parseInt(dataArray[index].locationStrings[index2].substring(dataArray[index].locationStrings[index2].length - 1));

            if(eventIDNumber === locationIDNumber){

                var locationString = dataArray[index].locationStrings[index2].substring(0, dataArray[index].locationStrings[index2].length - 2);
                document.getElementById(dataArray[index].eventCellID).innerHTML = eventString;
                document.getElementById(dataArray[index].locationCellID).innerHTML = locationString;
                break;
            }
        }
    }

    setTimeout("displayMultipleElementsInTurn()", 2000);
}
