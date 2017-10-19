<!DOCTYPE HTML>
<html>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="/Public/Home/css/style.css" />
</head>
<body>
<div class="wp">
	<div class="error">
		<p class="face">:(</p>
		<h1><?php echo strip_tags($e['message']);?></h1>
		<div class="content">
		<?php if(isset($e['file'])) {?>
			<div class="info">
				<div class="title">
					<h3>错误位置</h3>
				</div>
				<div class="text">
					<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
				</div>
			</div>
		<?php }?>
		<?php if(isset($e['trace'])) {?>
			<div class="info">
				<div class="title">
					<h3>TRACE</h3>
				</div>
				<div class="text">
					<p><?php echo nl2br($e['trace']);?></p>
				</div>
			</div>
		<?php }?>
		</div>
	</div>
</div>
</body>
</html>