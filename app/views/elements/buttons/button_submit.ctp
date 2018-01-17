<?php
//Setup the classes
$linkClass = 'button';
if(!empty($class)) $linkClass .= ' ' . $class;


//Fix for wizard component
if(is_array($text)) $text = $text[0];

$text = '<span>' . $text . '</span>';

if(!empty($icon)) {
	$text = '<div class="icon"></div>' . $text;
}

//Generate a standard link
if(@$type == 'link'):

if(!empty($url)) {
	$url = Router::url($url);
} else {
	$url = '#';
}

echo $this->Html->tag('a', $text, array('href' => $url, 'class' => $linkClass, 'escape' => false));


//Generate a <button>
else: ?>

<button class="<?php echo $linkClass; ?> submit" type="submit" <?php if(isset($name)) echo 'name="' . $name . '"'; ?> value="<?php if(isset($text)) echo strip_tags($text); ?>">
	<?php echo $text; ?>
</button>

<?php endif; ?>
