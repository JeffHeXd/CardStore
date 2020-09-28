<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="https://new.ldygo.fun/favicon.png" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title><?php echo $title; ?></title>
        <!--此处加载CSS文件-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.css">
        <!--此处加载必要的JS文件-->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/bootstrap-table/1.1.4/locale/bootstrap-table-zh-CN.min.js"></script>
        <style>
            .cnfont{
                font-family:Avenir,Helvetica,Arial,sans-serif; 
                color: #606266;
                font-weight:bold
            }
            .panel-group{max-height:770px;overflow: auto;}
            .leftMenu{margin:10px;margin-top:5px;}
            .leftMenu .panel-heading{font-size:14px;padding-left:20px;height:36px;line-height:36px;color:white;position:relative;cursor:pointer;}/*转成手形图标*/
            .leftMenu .panel-heading span{position:absolute;right:10px;top:12px;}
            .leftMenu .menu-item-left{padding: 2px; background: transparent; border:1px solid transparent;border-radius: 6px;}
            .leftMenu .menu-item-left:hover{background:#C4E3F3;border:1px solid #1E90FF;}
        </style>
    </head>
    <body>
        <script>
        </script>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">JStore后台管理</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index">主页</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="orderlist">订单管理</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="coupons_type">商品管理</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="account">账户管理</a>
      </li>
        <li class="nav-item">
            <a class="nav-link" href="log">全站日志</a>
        </li>
      <li class="nav-item">
        <a class="nav-link" href="ajax?type=loginout">注销登录</a>
      </li>
    </ul>
    <span class="navbar-text">
      当前管理员是<?php echo $_SESSION['name']; ?>
    </span>
  </div>
</nav>
