<?php $this->Html->setHeader('Verification is required'); ?>

<?php $email = Configure::read('App.email'); ?>

<p>Your site requires manual verification, if sucessful, your site will be visible on Anime Toplist within 24 hours.</p>

<p>If there are any problems, please contact us at <?php echo $this->Html->link($email, 'mailto:' . $email); ?></p>

<style>
p {font-size: 14px;}
</style>
