<html>
<body BGCOLOR="#FFFFEE">
<center>
<h1>Washngton double stars catalog</h1>
<?php
#if ($submit == "Submit Query")
#{
echo("<br/>Start PHP <br/>");

if ( (is_numeric($_POST['ra_c1'])) &&   (is_numeric($_POST['de_c1'])) 
   // && ( is_numeric($_POST['ra_c2']) || $_POST['ra_c2']== null) 
  //  && ( is_numeric($_POST['de_c2']) || $_POST['de_c2']==null ) 
    && is_numeric($_POST['d'] ) && ($_POST['d'] > 0.) && ($_POST['d'] < 1000.) )
{
    $ra_c1 = $_POST['ra_c1'];
    $ra_c2 = $_POST['ra_c2'];
    $de_c1 = $_POST['de_c1'];
    $de_c2 = $_POST['de_c2'];

    $d = $_POST['d'];
    $deg = $d/60.;
    $use_sp = $_POST['use_sp'];
    $sp = $_POST['sp'];
    $use_min = $_POST['use_min'];
    $use_max = $_POST['use_max'];
    $m1 = $_POST['m_min'];
    $m2 = $_POST['m_max'];
    $sort_by = $_POST['order'];

    #Connect to MySQL
    ## *****************************************************************************************
    ####### grant select on DBLStars.* to guest@localhost IDENTIFIED BY "kIr0thEkIng-123456"; ########
    ####$conn = mysql_connect("localhost", "guest", "kIr0thEkIng-123456");
    #SELECT
    ##$result = @mysql_select_db("starcat",$conn) or die("Error in opening DB");
    ####$db_name = 'Star_catalog';
    
    $servername = 'localhost';
    $username = 'admin';
    $db_name = 'DBLStars';
    $password = 'kIr0thEkIng-123456';
    /*
    
    $link = mysql_connect('localhost', 'guest', 'kIr0thEkIng-123456');
    if (!$link) {
        echo ("<h3> Not connected </h3>");
        die('Not connected : ' . mysql_error());
        
    }

    echo("You enter DE_center(in degrees):<b> $de_c1</b><br>");
    // make foo the current db
    $conn = mysql_select_db($db_name, $link);
    if (!$conn) {
        die ("Can\'t use foo $db_name: " . mysql_error());
    }
    */
    echo("<h3> Try to connect </h3>");
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo ("<h3> After try to connect </h3>");
    $tbl_name = 'Star_cat';
    ####$res = @mysql_select_db("$db_name",$conn) or die("Error in opening DB");

    # Get params from HTML form
    ###$ra1 = 3.; $ra2 = 3.5; $de1 = 25.; $de2 = 30.;
    ###$ra_c = $ra_c1+$ra_c2/60.;

    $ra_c = $ra_c1+$ra_c2/60.;

    if ($de_c1 < 0)
        {
        $de_c = $de_c1-$de_c2/60.;
        }
    else
        {
        $de_c = $de_c1+$de_c2/60.;
        }
    $ra1 = $ra_c-($d/120.)/15.;
    $ra2 = $ra_c+($d/120.)/15.;
    $de1 = $de_c-$d/120.;
    $de2 = $de_c+$d/120.;
    $whr_mag =  " ";


    $query = 'SELECT star_id, RA, DE, M1, M2, Sp, NumbObs, pmRA FROM '.$tbl_name;


    echo("<u><b>Result of your query:</b></u><br>");

    echo("You enter RA_center(in hours):");
    echo("<b>".$ra_c."</b><br>");

    echo("You enter DE_center(in degrees):<b> $de_c</b><br>");

    echo("You enter size box: <u>$d arcmin</u><br>");

    if ($use_sp)
    {
        echo("You enter Sp: <b>$sp</b> <br>");
    }

    if ($use_min) 
    {
        if (is_numeric($m1))
        {
        echo("You enter min magnitude: <b>$m1</b> <br>");
        $whr_mag =  " AND m1 > ".$m1. " ";
        }
        else
        {
        echo("You enter <b>invalid</b> min magnitude: <b>$m1</b> it is ignored <br>");
        }
        }
    if ($use_max)
    {
    if (is_numeric($m2)) 
        {
        echo("You enter max magnitude: <b>$m2</b> <br");
        $whr_mag =  $whr_mag." AND m1 < ".$m2. " ";
        }
    else
        {
        echo("You enter <b>invalid</b> min magnitude: <b>$m2</b> it is ignored <br>");
        }
    }
    #$whr = '  WHERE star_id < 100';

    $whr = " WHERE (ra BETWEEN $ra1 AND $ra2) AND (de BETWEEN $de1 AND $de2) ";
    if ($use_sp)
    {
        $whr .= " AND upper(Sp) like upper('%".$sp."%')";
    }
    ##echo("Your WHERE clause is <br><u>$whr</u><br><br>");

    $ord = " ORDER BY ";


    if ($sort_by == "de")
        {
            $ord = " ORDER BY DE ";
        }
        elseif ($sort_by == "mag1")
            {
                $ord = " ORDER BY M1 ";
            }
            elseif ($sort_by == "mag1")
                {
                $ord = " ORDER BY (".$ra_c." -ra)*(".$ra_c." -ra)+(".$de_c." -de)*(".$de_c." -de) ";
                }
    else
    { 
        $ord = " ORDER BY RA ";
    }


    $query = $query.$whr.$whr_mag.$ord; # $query = $query.$whr.$ord;
    #####echo("<table Border=1>\n");

    ####$result = mysql_query($query,$conn);
    $result = $conn->query($query);
    
    ####$numb = mysql_num_rows($result);
    $numb = $result->num_rows;

    echo("<br> There are $numb records selected<br>"); 

    $i=0;

    if ($numb > 0)
    {
    echo("<br> There are $numb records selected<br>");
    echo("<table Border=1>\n");
    echo("<tr><td>Numer:</td><td align=center>RA</td><td align=center>De</td>");
    echo("<td>Magnitude1</td><td>Magnitude2</td><td align=center>Sp</td><td align=right>Number of observations</td></tr>");
    ###while( $line = mysql_fetch_array($result))
    while($line = $result->fetch_assoc()) {
        {
            $i++;
            $res_m1 = $line["M1"];
            $res_m2 = $line["M2"];
            $res_sp = $line["Sp"];
            $ra_h = (int)$line["RA"];
            $ra_m = (($line["RA"]*600)%60)/10.;
            $de_d = (int)$line["DE"];
            $de_m = (($line["DE"]*600)%60)/10.;
            $numb_obs = $line["NumbObs"];
            if ($numb_obs == null) $numb_obs = "Not available";
            echo("<tr><td align=right>".$i."</td><td align=right>".$line["RA"]."</td><td align=right>".$line["DE"]);
            
            #echo("<tr><td>"$i"</td><td>".$line["RA"]);echo("</td><td>".$line["DE"]);

    if ($res_m1 == null)
            {
                $res_m1 = "Not available";
            }
            if ($res_m2 == null)
            {
                $res_m2 = "Not available";
            }
            if ($res_sp == null)
            {
                $res_sp = "Not available";
            } 

        echo("</td><td align=right>".$res_m1."</td><td align=right>".$res_m2."</td><td align=right>".$res_sp);
            echo("</td><td align=right>$numb_obs");
        
        echo("</td></tr>");
    #   Test	
    ###	echo("<tr><td align=right>".$i."</td><td align=right> $ra_h:$ra_m </td><td align=right>$de_d:$de_m");
    ###	echo("</td><td align=right>$res_m1</td><td align=right>$res_m2</td><td align=right>$res_sp</td></tr>");
        }  
    echo("</table>");
    if ($numb == 1)
    {
    ##    echo("There is $numb record selected"); 
        echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
    }
    if ($numb > 1)
    {  
    ##   echo("There are $numb records selected");
    echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
    }
    }
    else
    {
    echo("<br><u> No result records</u>");  
    echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
    } 
    ###mysql_free_result($result);
    ###mysql_close($conn);
    $conn->close();
}
else
{
echo("<h3><b><u> Hmm </u></b></h3>");
if ( $_POST['d'] > 1000.)
     {
     echo("<p><b><u> Size of the field is too big</u></b></p>");
     echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
    }
elseif ($_POST['d'] < 0)
    {
     echo("<p><b><u> Size of the field can not be negative</u></b></p>");
     echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
     }  
else
{     
    echo("<p><b><u> Please enter valid RA and DE</u></b></p>");
    echo("<br><a href=\"info.html\"><u> Back to query form</u></a>");
}
}
}
?>
</center>
</body>
</html>
