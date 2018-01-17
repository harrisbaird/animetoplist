<?php
class CouponHelper extends AppHelper {
	
	/**
	 * Describe what a coupon does
	 *
	 * @return string
	 */
	function description($data) {
		if($data['Coupon']['type'] == 'trial') {
			$amount = floor($data['Coupon']['amount']);
			return String::insert(':amount free weeks per site', array('amount' => $amount));
		}
		
		if($data['Coupon']['type'] == 'percentage') {
			$amount = $data['Coupon']['amount'] * 100;
			return String::insert(':amount%', array('amount' => $amount));
		}
		
		if($data['Coupon']['type'] == 'amount') {
			return String::insert('$:amount', array('amount' => $data['Coupon']['amount']));
		}
	}
}
?>
