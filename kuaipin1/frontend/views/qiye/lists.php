<?php

 ?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
 	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
 	<title>Document</title>
 </head>
 <body>
 	<center>
 		<table border=1>
 			<th>企业名称</th>
 			<th>企业评分</th>
 			<th>操作</th>
 			<?php foreach($a as $key=>$val) { ?>
 			<tr>
 				<td><?php echo $val['q_name']?></td>
 				<td><?php echo $val['q_pf']?></td>
 				<td><a href="javascript:update1(<?php echo $val['q_id']?>)">详情</a></td>
 			</tr>
 			<?php } ?>
 		</table>
 	</center>
 </body>
 </html>
 <script src="frontend/web/jquery1.8.js"></script>
 <script>
function update1(id)
{
	data={'id':id};
	url="index.php?r=qiye/updates";
	$.get(url,data,function(msg){
		alert(msg)
	})
}
 </script>