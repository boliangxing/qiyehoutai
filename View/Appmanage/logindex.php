<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,member-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<script type="text/javascript" src="lib/PIE_IE678.js"></script>
<![endif]-->
<link href="<?php echo FC_URL ?>css/H-ui.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>css/H-ui.admin.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>lib/Hui-iconfont/1.0.1/iconfont.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>css/style.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>操作日志</title>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> APP管理 <span class="c-gray en">&gt;</span> 操作日志 <a class="btn btn-success radius r mr-20" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="select" style="width:100px">
            <option value="10">三天</option>
            <option value="25">一个星期</option>
            <option value="50">一个月</option>
            <option value="100">一年</option>
        </select>
        前的内容
        <a class="btn btn-primary radius" href="javascript:clean();">清除</a>
    </div>
    
    <div class="mt-20">
    <table class="table table-border table-bordered table-hover table-bg table-sort">
        <thead>
            <tr class="text-c">
                
                <th width="30">uid</th>
                <th width="90">url</th>
                <th width="40">方法</th>
                <th width="120">数据</th>
                <th width="90">ip</th>
                <th width="90">时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($cmdsList as $cmds){ ?>
            <tr class="text-c">
                
                
                <td><a href="javascript:;" onclick="member_show('查看','<?php echo site_url('userlog/info').'/uid/'.$cmds['uid'] ?>','<?php echo $cmds['uid'] ?>','420','310')" ><?php echo $cmds['uid'] ?></a></td>
                <td><?php echo $cmds['url'] ?></td>
                <td><?php echo $cmds['method'] ?></td>
                <td><?php echo $cmds['data'] ?></td>
                <td><?php echo $cmds['ip'] ?></td>
                <td><?php echo $cmds['time'] ?></td>
                <!-- <td class="td-status"><span class="label label-success radius">已启用</span></td> -->
                
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="page">
        <ul class="pagination"><?php echo $page ?></ul>
    </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo FC_URL ?>lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript" src="<?php echo FC_URL ?>lib/laypage/1.2/laypage.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>lib/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>js/H-ui.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>js/H-ui.admin.js"></script>
<script type="text/javascript">
function member_show(title,url,id,w,h){
    layer_show(title,url,w,h);
} 

function clean(){
    alert('这个功能稍后再做，请先手动清除  哈哈');
}
</script>
</body>
</html>