<?php


$f=mssql_connect  ('POS-LIMEWAX','lime','123456789lkjhgfdsa');

mssql_select_db ("PosTerminal");

$sql1="select id, ldap, fio from LogisTeam";
$res1=mssql_query($sql1);


$branch="";
$date="";
$action ="";

if (isset($_POST['branch'])) $branch = ($_POST['branch']); 
if (isset($_POST['date'])) $date= ($_POST['date']); 
if (isset($_POST['action'])) $action = ($_POST['action']);
if (isset($_POST['dt_find'])) $dt_find = ($_POST['dt_find']);
if (isset($date) && $date!="") $dt=$date;
else $dt=date("d.m.Y");

/*
if (isset($branch) && $branch!="") $bt=$branch;
else $bt="-- выберите бранч --";
*/
?>


<html>

<head>

<title>Статистика склада ИТ РП (тест)</title>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta name="keywords" content="">
<meta name="description" content="">
<link rel=stylesheet type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal_2.js"></script> 

</head>


<body class='body_' >




<table width="100%" align="center" border="1" cellspacing="0" cellpadding="0" borderColorDark="#FFFFFF" borderColorLight="#208316">
<tr><td> 
	<table width='100%' border='0' cellspacing="0" cellpadding="0">
    <tr>
       
        <td><IMG SRC="img/logo.gif" WIDTH="60%" HEIGHT="60%"></td>
        <td align="center"><br><h2>ПЛАТЁЖНЫЕ ТЕРМИНАЛЫ</h2></td>
<td align='center'>
 	Банк <select name="choose_bank">
		<option value="">ПриватБанк(Украина)</option>
    
    </select> <input type="submit" name="btnSelect" value="выбрать" class="button">
</td>

    </tr>
	</table> 
</td></tr>
</table> 

<table width="100%" align="center" border="1" cellspacing="0" cellpadding="0" borderColorDark="#FFFFFF" borderColorLight="#208316">
<tr><td> 


<form method="post" action="index.php">
<table align='center'><br><br>
	<tr><td colspan='5' align='center'>
    <h2>Статистика склада РП</h2>
    </td></tr>
    <tr>
        <td  align='left'><b>Бранч:</b></td>
    <td  align='right'><select name='branch' id='branch'>
    
  
		<option value='all'>-- выберете бранч --</option>
      
         	    <?php
                                
                    $sql="SELECT syb_branch, name FROM Syb_Branch
                    where syb_branch not in ('SIH0','SEH0')";
                    $res=mssql_query($sql);
                    while($row=mssql_fetch_array($res))
                    
			{   
                            ?>
                            
                            <option value='<?php echo $row['syb_branch']; ?>' <?php if ( $row['syb_branch'] == $branch ) {?> selected="selected"<?php ;} ?> > <?php echo "{$row['syb_branch']}: {$row['name']}"; ?></option><?php			
			}
                ?>
  </select> </td>
<td>&nbsp &nbsp &nbsp</td>
    <td  align='left'><b>Дата:</b></td>
    <td  align='right'><div><input class="tcal" type="text" name='date'  size='10' value='<?php print($dt); ?>'></div></td>
    </tr>
    <tr><td colspan='5' align='center'>
    <br><br><br>
        <input type='submit' value='Показать' class='button'>
        <input type='hidden' name='action' value='find'>
    </td></tr>
</table>
</form>

<?php
if($action=='find' and $branch!='all')
{
	?>
<table align='center' border='1' width='70%' cellspacing="0" cellpadding="0" borderColorDark="#FFFFFF" borderColorLight="#208316">
	<th>Модель</th>
	<th  width='150' >БРТП</th>
	<th width='150'>ИТ</th>
    <tr><th colspan='3' align='center'><?php print($branch)?>&nbsp-&nbsp<?php print($date)?></th></tr>

<?php

$sql_="SELECT rtrim(o.name) as Model, isnull(s1.kol,0) as BRTP, isnull(s4.kol,0) as IT


FROM op_type_model as o
left join (SELECT model_id, kol FROM Sklad_RP_History 
where business=4
and convert(char(10),date,104)='$date'
and  syb_branch='$branch') as s4 on s4.model_id=o.model_id
left join (SELECT model_id, kol FROM Sklad_RP_History 
where business=1
and convert(char(10),date,104)='$date'
and  syb_branch='$branch') as s1 on s1.model_id=o.model_id

where device=1 and actual=1
and (case when isnull(s1.kol,0)=isnull(s4.kol,0) and isnull(s1.kol,0)=0 then 0 else 1 end )=1

order by rtrim(o.name)";
		$result=mssql_query($sql_);
		$num=mssql_num_rows($result);
        $it_sum=0;
        $brtp_sum=0;
         if($num!=0)
        {
           for($j=0; $j<$num; $j++)
           {
            
                $model=mssql_result($result,$j,0);
				$it=mssql_result($result,$j,1);
				$brtp=mssql_result($result,$j,2);
                
             $str="<tr><td align='center' bgcolor='#C5F2AC'>$model</a></td>
                       <td align='center' bgcolor='#CEF0AD'>$it</td>
                       <td align='center' bgcolor='#CEF0AD'>$brtp</td></tr>";  
             echo $str;
            $it_sum+=$it;
            $brtp_sum+=$brtp;
              
           }
           
            $strI="<tr><td align='center' bgcolor='#A7DA8B'><b>Итого</b></a></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$it_sum</b></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$brtp_sum</b></td></tr>";
            echo $strI;
            
        }
        else{
            
            $strI="<tr><td colspan='3' align='center'><b>НЕТ ДАННЫХ</b></a></td>";
            echo $strI;
        }
}

if($action=='find' and $branch=='all')
{
    
      ?>
            <table align='center' border='1' width='70%' cellspacing="0" cellpadding="0" borderColorDark="#FFFFFF" borderColorLight="#208316">
	<th>Модель</th>
	<th width='150'>БРТП</th>
	<th width='150'>ИТ</th>

<?php
    
$sql_2="SELECT syb_branch, name FROM Syb_Branch
                    where syb_branch not in ('SIH0','SEH0') order by syb_branch";
                    
        $res_2=mssql_query($sql_2);
		$num_2=mssql_num_rows($res_2);
     $it_sum2=0;
     $brtp_sum2=0;
     
     
      if($num_2!=0)
        {
           for($j=0; $j<$num_2; $j++)
           {
            
            $br=mssql_result($res_2,$j,0);
            ?>
            
            <tr><th colspan='3' align='center'><?php print($br) ?>&nbsp-&nbsp<?php print($date)?></th></tr>

<?php
            $sql_3="SELECT rtrim(o.name) as Model, isnull(s1.kol,0) as BRTP, isnull(s4.kol,0) as IT
                    FROM op_type_model as o
                    left join (SELECT model_id, kol FROM Sklad_RP_History 
                    where business=4
                    and convert(char(10),date,104)='$date'
                    and  syb_branch='$br') as s4 on s4.model_id=o.model_id
                    left join (SELECT model_id, kol FROM Sklad_RP_History 
                    where business=1
                    and convert(char(10),date,104)='$date'
                    and  syb_branch='$br') as s1 on s1.model_id=o.model_id
                    
                    where device=1 and actual=1
                    and (case when isnull(s1.kol,0)=isnull(s4.kol,0) and isnull(s1.kol,0)=0 then 0 else 1 end )=1
                    
                    order by rtrim(o.name)";
                
                 $res_3=mssql_query($sql_3);
		        $num_3=mssql_num_rows($res_3);
                $it_sum=0;
                $brtp_sum=0;
                 if($num_3!=0)
        {
           for($i=0; $i<$num_3; $i++)
           {     
                $model=mssql_result($res_3,$i,0);
				$it=mssql_result($res_3,$i,1);
				$brtp=mssql_result($res_3,$i,2);
                
             $str1="<tr><td align='center' bgcolor='#C5F2AC'>$model</a></td>
                       <td align='center' bgcolor='#CEF0AD'>$it</td>
                       <td align='center' bgcolor='#CEF0AD'>$brtp</td></tr>";  
             echo $str1;
             $it_sum+=$it;
             $brtp_sum+=$brtp;                 
            
           }
           $strI="<tr><td align='center' bgcolor='#A7DA8B' ><b>Итого</b></a></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$it_sum</b></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$brtp_sum</b></td></tr>";
            echo $strI;
            
            $it_sum2+=$it_sum;
            $brtp_sum2+=$brtp_sum;
    
        }
        else{
            
            $strI="<tr><td colspan='3' align='center'><b>НЕТ ДАННЫХ</b></a></td>";
            echo $strI;
        }
        
          
        
        }


  
                

}

            $strII="<tr><td align='center' bgcolor='#A7DA8B' ><b>ИТОГО</b></a></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$it_sum2</b></td>
                       <td align='center' bgcolor='#A7DA8B'><b>$brtp_sum2</b></td></tr>";
            echo $strII;
}

if($action=='find') {
    
    ?>

</table>

<?php    
    
                    }

?>


<br><br><br>

</td></tr>
</table> 

<table width="100%" align="center" border="1" cellspacing="0" cellpadding="0" borderColorDark="#FFFFFF" borderColorLight="#208316">
<tr>
<td align="right">
<font face=arial size=1 color=gray>
2003-2014 &copy; Процессинговый центр КБ "Приватбанк"
</font>
</td></tr>
</table>

</body>
</html>
