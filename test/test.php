<?php
//一、假设用户名和密码为admin,admin

//二、开启session,判断登录状态
session_start();

//三、定义JSON函数
/*
$status 用来做匹配的状态
$data 传输的数据(数组格式)
$msg 传输后反应的信息
*/
function returnJson($status,$data="",$msg="")
{
	$j_array = array("status"=>$status,"data"=>$data,"msg"=>$msg);
	echo json_encode($j_array);
	exit;
}

/*判断是否通过POST传来了name和pwd,这里先判断是否有用户名,
因为没有用户名直接输出前端的 
error: function()
{  
    alert('请输入用户名,和密码');  
}, 
原因是没有返回json:详情可以看代码逻辑
*/

if(isset($_POST['name'])&&$_POST['name']!='')
{
	$name = $_POST['name'];
	$pwd = $_POST['pwd'];
	//判断是否有密码
	if(empty($pwd))
	{
		returnJson(0,"","请输入密码");
	}
	else
	{
		//当有密码时候,判断用户和密码是否正确
		if($name=='admin'&&$pwd=='admin')
		{
			//登录成功后赋值给session
			$_SESSION['username'] = $name;
			
			/*这里可以检索数据库语句
			我就简单的写一个for循环当做数据库的数据
			*/
			$i=0;
			for($i=0;$i<=10;$i++)
			{	
				//把循环的数据写成数组
				$data[$i]['id'] = $i;
			}
			
			//通过JSON函数将数组转化为json格式,这里我将session写到msg里面,也可以另写json
			returnJson(1,$data,$_SESSION['username']);
		}
		else
		{
			//用户或者密码不正确的
			returnJson(0,"","账户或者密码输入错误");
		}
	}
	
	
}
//个人喜欢这样引入HTML文件,我觉得写起来比较美观,方便查看.
include "test.html";
?>
