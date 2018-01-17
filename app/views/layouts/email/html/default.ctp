<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background-color: #e6e6e6;">
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" style="background-color: #333537; padding: 15px 0; color: #fff; border-bottom: 1px solid #2f87d6;">
				<table width="600" cellspacing="0" cellpadding="0">
					<tr>
						<td><img src="<?php echo $logo; ?>" /></td>
						<?php if(!empty($username)): ?>
						<td align="right" style="color: #fff;">Your username: <?php echo $username; ?></td>
						<?php endif; ?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center" style="background-color: #2d67a1; border-bottom: 1px solid #1053ba;">
				<table width="600" cellspacing="0" cellpadding="0">
					<tr>
						<td><div style="font-size: 30px; font-family: Arial, sans-serif; color: #fff; padding: 22px 0;"><?php echo $title; ?></span></td>
					</tr>
				</table>
			</td>
		</tr>
		<?php echo $content_for_layout; ?>

	</table>
</body>
</html>