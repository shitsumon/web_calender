<!--
Copyright (c) 2012 Michael Flau <michael@flau.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../stylesheets/weekview_style.css"/>
    <script type="text/javascript" src="../javascript/textslider.js"></script>    
</head>
<body onLoad="javascript:onLoadStartSlideShow('tagContainer')">
    <div class="weekview">
    <?php
        include("htmlViewCreators.php");

        if( isset($_REQUEST['timestamp'])){
            $date = $_REQUEST['timestamp'];
        }else{
            $date = time();
        }
    
        createWeekView($date);
    ?>
    </div>
</body>
</html>