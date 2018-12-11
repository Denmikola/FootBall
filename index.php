<?php 
header('Content-Type: text/html; charset=utf-8;');
setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia. 65001', 'russian');

include 'data.php';

# в этой функции я позволил себе вольные манипуляции с вероятностями
# по правильному в данном случае можно использовать распределение Пуассона
# но по скольку в условии было использование рандома, то и придерживаться
# математической логики я не увидел смысла
function match($c1,$c2){
  global $data;     if (!isset($data)) return;
  $com=$data[$c1];  if (!isset($com)) return; elseif(!isset($com['games'])) return;
  $p1_n=$com['win']/$com['games'];  #вероятность выигрыша K1
  $p1_o=$com['defeat']/$com['games'];  #вероятность проигрыша K1
  $g1_sc=$com['goals']['scored']/$com['games'];    #среднее количество забитых голов К1 
  $g1_sk=$com['goals']['skiped']/$com['games'];    #среднее количество пропущенных голов К1  
  if ((!isset($p1_n))OR(!isset($p1_o))OR(!isset($g1_sc))OR(!isset($g1_sk))) return;

  $com=$data[$c2];  if (!isset($com)) return; elseif(!isset($com['games'])) return;
  $p2_n=$com['win']/$com['games'];  #вероятность выигрыша K2
  $p2_o=$com['defeat']/$com['games'];  #вероятность проигрыша K2
  $g2_sc=$com['goals']['scored']/$com['games'];    #среднее количество забитых голов К2 
  $g2_sk=$com['goals']['skiped']/$com['games'];    #среднее количество пропущенных голов К2  
  if ((!isset($p2_n))OR(!isset($p2_o))OR(!isset($g2_sc))OR(!isset($g2_sk))) return;

  $o1_z=$p1_n*$p2_o*$g1_sc; #ожидание забитых голов командой К1 на встрече c К2
  $o2_z=$p2_n*$p1_o*$g2_sc; #ожидание забитых голов командой К2 на встрече c К1

  $o1_p=$p1_n*$p2_o*$g1_sk; #ожидание пропущенных голов командой К1 на встрече c К2
  $o2_p=$p2_n*$p1_o*$g2_sk; #ожидание пропущенных голов командой К2 на встрече c К1

  $o1=1-($o1_z+$o2_p)*0.5; # среднее ожидание голов для К1
  $o2=1-($o2_z+$o1_p)*0.5; # среднее ожидание голов для К2

  $r1=round((mt_rand(0, 500)*$o1)/100,0); # элемент случайности для К1
  $r2=round((mt_rand(0, 500)*$o2)/100,0); # элемент случайности для К1

return array($r1,$r2);
}
?>

<html>
<head>
   <title>Футбольный предсказатель</title>
</head>
<body>
   <form method="post" 
      action="<?php $s1=$_POST['sel1']; $s2=$_POST['sel2']; 
                    if($s1<>$s2) $res=match($s1,$s2);?>"> 
       <p>Первая команда - 
          <select name="sel1" size="1">
             <?php $i=0; if (!isset($s1)) $s1=0; 
               foreach ($data as $comand) { ?>                
                 <option 
                     <?php if ($i==$s1) echo "selected"; ?>  
                     value="<?php echo $i; $i+=1; ?>">
                     <?php echo $comand['name']; } ?>
                 </option>
          </select></p> 
        <p>Вторая команда -
          <select name="sel2" size="1" >
              <?php $i=0; if (!isset($s2)) $s2=1;
                foreach ($data as $comand) { ?>                
                 <option 
                     <?php if ($i==$s2) echo "selected"; ?>  
                     value="<?php echo $i; $i+=1; ?>">
                     <?php echo $comand['name']; } ?>
                 </option>
          </select></p>
          <p><input type="submit" value="Предсказать"></p>
     </form>
     <p>Результат матча -<?php if (isset($res)) echo '  ',$res[0],':',$res[1]; ?></p
</body>
</html>