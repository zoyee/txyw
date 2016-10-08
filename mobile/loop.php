<?php
echo "死循环任务启动";
while (1){
	$i++;
	echo i . "<br/>";
	sleep(1);
}