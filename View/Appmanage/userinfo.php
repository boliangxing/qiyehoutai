<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<script type="text/javascript" src="lib/PIE_IE678.js"></script>
<![endif]-->
<link href="<?php echo FC_URL ?>css/H-ui.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>css/H-ui.admin.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>lib/icheck/icheck.css" rel="stylesheet" type="text/css" />
<link href="<?php echo FC_URL ?>lib/Hui-iconfont/1.0.1/iconfont.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script>
<![endif]-->
<title>用户资料</title>
</head>
<body>
<div class="pd-20">
 
    <div class="row cl">
      <label class="form-label col-3">用户名：</label>
      <div class="formControls col-5">
        <input type="text" class="input-text" value='<?php echo $info['nick'] ?>'  disabled="disabled" placeholder="" id="member-name" name="nick" datatype="*2-16" nullmsg="">
      </div>
      <input type="hidden" value="<?php echo $info['uid'] ?>" name="uid">
      <div class="col-4"> </div>
    </div>
    <div class="row cl">
      <label class="form-label col-3">密码：</label>
      <div class="formControls col-5">
        <input type="text" class="input-text" value='<?php echo $info['pwd'] ?>' disabled="disabled" placeholder="" id="member-pwd" name="pwd" datatype="*2-16" nullmsg="">
      </div>
      <div class="col-4"> </div>
    </div>
    <div class="row cl">
      <label class="form-label col-3">手机：</label>
      <div class="formControls col-5">
        <input type="text" class="input-text" value="<?php echo $info['phone'] ?>" disabled="disabled" placeholder="" id="member-tel" name="phone"  datatype="m" nullmsg="">
      </div>
      <div class="col-4"> </div>
    </div>
    <div class="row cl">
      <label class="form-label col-3">邮箱：</label>
      <div class="formControls col-5">
        <input type="text" class="input-text"  name="email" id="email" datatype="e" disabled="disabled" nullmsg="" value="<?php echo $info['email'] ?>">
      </div>
      <div class="col-4"> </div>
    </div>
   
</div>
</div>
<script type="text/javascript" src="<?php echo FC_URL ?>lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>lib/icheck/jquery.icheck.min.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>lib/Validform/5.3.2/Validform.min.js"></script>
<script type="text/javascript" src="<?php echo FC_URL ?>lib/layer/1.9.3/layer.js"></script>
<script type="text/javascript" src="<?php echo FC_URL ?>js/H-ui.js"></script> 
<script type="text/javascript" src="<?php echo FC_URL ?>js/H-ui.admin.js"></script>

</body>
</html>