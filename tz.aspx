<%@ Page Language="C#"%>
<%@ Import Namespace="System.Globalization"%>
<%@ Import Namespace="Microsoft.Win32" %>
<%@ Import Namespace="System.IO"%>
<%@ Import Namespace="System.Diagnostics" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>创梦.Net探针 v1.0</title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
* {font-family: "Microsoft Yahei",Tahoma, Arial; }
body{text-align: center; margin: 0 auto; padding: 0; background-color:#fafafa;font-size:12px;font-family:Tahoma, Arial}
h1 {font-size: 26px; padding: 0; margin: 0; color: #333333; font-family: "Lucida Sans Unicode","Lucida Grande",sans-serif;}
h1 small {font-size: 11px; font-family: Tahoma; font-weight: bold; }
a{color: #666; text-decoration:none;}
a.black{color: #000000; text-decoration:none;}
table{width:100%;clear:both;padding: 0; margin: 0 0 10px;border-collapse:collapse; border-spacing: 0;
box-shadow: 1px 1px 1px #CCC;
-moz-box-shadow: 1px 1px 1px #CCC;
-webkit-box-shadow: 1px 1px 1px #CCC;
-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=2, Direction=135, Color='#CCCCCC')";}
th{padding: 3px 6px; font-weight:bold;background:#dedede;color:#626262;border:1px solid #cccccc; text-align:left;}
tr{padding: 0; background:#FFFFFF;}
td{padding: 3px 6px; border:1px solid #CCCCCC;}
.logo{height:25px;text-align:center;color:#333;FONT-SIZE: 15px; width:13%; }
.top{height:25px;text-align:center; width:8.7%;}
.top:hover{background:#dadada;}
.foot{height:25px;text-align:center; background:#dedede;}
input{padding: 2px; background: #FFFFFF; border-top:1px solid #666666; border-left:1px solid #666666; border-right:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC; font-size:12px}
input.btn{font-weight: bold; height: 20px; line-height: 20px; padding: 0 6px; color:#666666; background: #f2f2f2; border:1px solid #999;font-size:12px}
.bar {border:1px solid #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px; overflow: hidden;}
.bar_1 {border:1px dotted #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px; overflow: hidden;}
.barli_red{background:#ff6600; height:5px; margin:0px; padding:0;}
.barli_blue{background:#0099FF; height:5px; margin:0px; padding:0;}
.barli_green{background:#36b52a; height:5px; margin:0px; padding:0;}
.barli_black{background:#333; height:5px; margin:0px; padding:0;}
.barli_1{background:#999999; height:5px; margin:0px; padding:0;}
.barli{background:#36b52a; height:5px; margin:0px; padding:0;}
#page {width: 960px; padding: 0 auto; margin: 0 auto; text-align: left;}
#header{position:relative; padding:5px;}
.small{font-family: Courier New;}
.number{color: #f800fe;}
.sudu {padding: 0; background:#5dafd1; }
.suduk { margin:0px; padding:0;}
.resYes{}
.resNo{color: #FF0000;}
.word{word-break:break-all;}
</style>
<script language=javascript>
var t1 = new Date().getTime();
</script>
</head>
<body>
<div id="page">
	<table>
		<tr>
			<th class="logo">创梦.Net探针</th>
		</tr>
	</table>
<table>
  <tr><th colspan="4">服务器参数</th></tr>
  <tr>
    <td>服务器域名/IP地址</td>
    <td colspan="3"><%=Request.ServerVariables["SERVER_NAME"].ToString()%>(<%=Request.ServerVariables["LOCAl_ADDR"]%>)</td>
  </tr>
  <tr>
    <td width="13%">服务器操作系统</td>
    <td width="37%"><%=Environment.OSVersion.ToString()%></td>
    <td width="13%">服务器端口</td>
    <td width="37%"><%=Request.ServerVariables["Server_Port"].ToString()%></td>
  </tr>
  <tr>
	  <td>系统语言</td>
	  <td><%=CultureInfo.InstalledUICulture.EnglishName%></td>
	  <td>系统路径</td>
	  <td><%=Environment.SystemDirectory.ToString()%></td>
	</tr>
  <tr>
	  <td>系统用户名</td>
	  <td><%=Environment.UserName%></td>
	  <td>探针路径</td>
	  <td><%=Request.PhysicalApplicationPath%></td>
	</tr>
  <tr>
	  <td>CPU数量</td>
	  <td><%=Environment.GetEnvironmentVariable("NUMBER_OF_PROCESSORS").ToString()%></td>
	  <td>CPU类型</td>
	  <td><%=Environment.GetEnvironmentVariable("PROCESSOR_IDENTIFIER").ToString()%></td>
	</tr>
  <tr>
	  <td>启动时间</td>
	  <td><%=((Environment.TickCount / 0x3e8) / 60).ToString() + "分钟"%></td>
	  <td>逻辑驱动器</td>
	  <td><%
        string[] achDrives = Directory.GetLogicalDrives();
        for (int i = 0; i < Directory.GetLogicalDrives().Length - 1; i++)
        {
            Response.Write(achDrives[i].ToString()+" ");
        }
		%></td>
	</tr>
  <tr>
	  <td>IE版本</td>
	  <td colspan="3"><%
        RegistryKey key = Registry.LocalMachine.OpenSubKey(@"SOFTWARE\Microsoft\Internet Explorer\Version Vector");
        Response.Write(key.GetValue("IE", "未检测到").ToString() + "<br />");
		%></td>
	</tr>
  <tr>
	  <td>.Net版本</td>
	  <td><font color="green"><%=string.Concat(new object[] { Environment.Version.Major, ".", Environment.Version.Minor, Environment.Version.Build, ".", Environment.Version.Revision })%></font></td>
	  <td>脚本超时时间</td>
	  <td><%=(Server.ScriptTimeout / 1000).ToString() + "秒"%></td>
	</tr>
  <tr>
	  <td>.Net占用内存</td>
	  <td><%=((Double)Process.GetCurrentProcess().WorkingSet64 / 1048576).ToString("N2") + "M"%></td>
	  <td>.Net占用CPU</td>
	  <td><%=((TimeSpan)Process.GetCurrentProcess().TotalProcessorTime).TotalSeconds.ToString("N0")+ "%"%></td>
	</tr>
</table>

	<table>
		<tr>
			<td class="foot"><A HREF="https://www.150cn.com" target="_blank">创梦.Net探针v1.0</A></td>
			<td class="foot"><div id="TimeShow"></div></td>
			<td class="foot"><a href="#top">返回顶部</a></td>
		</tr>
	</table>
<SCRIPT LANGUAGE="JavaScript">
window.onload = function()
{
document.getElementById("TimeShow").innerHTML = "加载本页耗时 "+ (new Date().getTime()-t1) +" 毫秒";
}
</SCRIPT>
</div>

</body>

</html>