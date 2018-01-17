	<?php
	$cost_week = Configure::read('App.premium.cost');
	$extend = false;
	
	$this->Html->setHeader('Premium Membership');
	?>

	<p>Select the number of weeks you want premium membership to be active per site.</p>
	
	<p>If you have multiple sites and only want one site to have premium membership, leave the other sites at 0 weeks.</p>
	<?php echo $this->Form->create('PremiumOrder'); ?>

	<table id="premium">
		<thead>
			<tr>
				<th>Site</th>
				<th>Duration</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tfoot>
			<?php if(!empty($data['Order']['discount'])): ?>
				<tr>
					<th colspan="2" class="premium-cost">Subtotal:</th>
					<td class="premium-cost"><?php echo $this->Number->currency($data['Order']['subtotal'], 'USD'); ?></td>
				</tr>
			<tr>
				<th colspan="2" class="premium-cost">Coupon Discount (<?php echo $this->Coupon->description($data); ?>):</th>
				<td class="premium-cost">-<?php echo $this->Number->currency($data['Order']['discount'], 'USD'); ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th colspan="2" class="premium-cost">Total:</th>
				<td class="premium-cost"><?php echo $this->Number->currency($data['Order']['total'], 'USD'); ?></td>
			</tr>
		</tfoot>
		<tbody>
	<?php foreach($data['PremiumItem'] as $item): ?>
		<?php if($item['Site']['is_premium']) $extend = true; ?>
		<tr>
			<td><?php echo $this->Html->link($item['Site']['official_name'], array('controller' => 'sites', 'action' => 'view', $item['Site']['id'])); ?><span class="url"><?php echo $item['Site']['url']; ?></span></td>
			<td class="duration">
				<?php echo $this->Form->input('PremiumItem.' . $item['id'] .  '.duration', array('value' => $item['duration'], 'label' => false)); ?> Weeks
			</td>
			<?php if(isset($item['discount'])): ?>
				<td class="premium-cost">
					<del><?php echo $this->Number->currency($item['cost_total'], 'USD'); ?></del><br />
					<?php echo $this->Number->currency($item['discount'], 'USD'); ?>
				</td>
			<?php else: ?>
				<td class="premium-cost"><?php echo $this->Number->currency($item['cost_total'], 'USD'); ?></td>		
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>

	<div class="grid_3 alpha">
		<?php echo $this->Form->input('Coupon.code', array('label' => 'Coupon code:', 'div' => array('id' => 'premium-coupon'))); ?>
	</div>

	<div class="premium-buttons submit grid_3 omega">
		<ul>
		<li><?php echo $this->element('buttons/button_submit', array('text' => 'Update', 'class' => 'grey')); ?></li>
	
		<li>
		<?php
		//If the total is $0, change the button text
		$payButton = $data['Order']['total'] != 0 ? 'Pay' : 'Finish';
		echo $this->element('buttons/button_submit', array('text' => $payButton, 'name' => 'data[Order][pay]'));
		?></li>
		</ul>
	</div>

	<?php echo $this->Form->end(); ?>
	
</div> <!-- /#content -->

<div class="sidebar grid_3">
	
	<div class="module module-top">
		<h2>Advantages</h2>
		<ul>
			<li>Ranking boxes are converted to a highly visible premium box.</li>
			<li>A link at the top of the sidebar.</li>
			<li>A banner at the top of all ranking pages.</li>
			<li>Have streaming Anime? Your site will be highlighted on each Anime page.</li>
			<li>Costs <strong>just <?php echo $this->Number->currency($cost_week, 'USD'); ?></strong> per week</li>
		</ul>
		
	</div> <!-- /.module -->		
		
	<?php if($extend): ?>
	<div class="module">
		<h2>Extending</h2>
		<ul>
			<li>When you buy more weeks for a site which already has premium membership, it will automatically be extended.</li>

		</ul>
	</div> <!-- /.module -->
	<?php endif; ?>
	
	<div class="module premium">
		<h2>Discount coupons</h2>
		<ul>
			<li>Enter <span style="font-size: 150%"><strong>at10</strong></span> and get 10% off 10 or more weeks.</li>
			<li>Enter <span style="font-size: 150%"><strong>at20</strong></span> and get 20% off 20 or more weeks.</li>
		</ul>
	</div> <!-- /.module -->