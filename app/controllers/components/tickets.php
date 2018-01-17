<?php

class TicketsComponent
{
	var $controller = null;
	var $Ticket = null;
	
	function startup(&$controller) {
		$this->controller =& $controller;
		$this->Ticket = ClassRegistry::init('Ticket');
	}
	
	// Create a new ticket by providing the data to be stored in the ticket.
	function set($info = null)
	{
		$this->garbage();
		if ($info)
		{
			$data['Ticket']['hash'] = md5(uniqid($info . microtime()));
			$data['Ticket']['data'] = serialize($info);

			if ($this->Ticket->save($data))
			{
				return $data['Ticket']['hash'];
			}
		}
		return false;
	}
	
	// Return the value stored or false if the ticket can not be found.
	function get($ticket = null)
	{
		$this->garbage();
		if ($ticket)
		{
			$data = $this->Ticket->findByHash($ticket);
			if (is_array($data) && is_array($data['Ticket']))
			{
				$this->delete($ticket);
				
				//Unserialize the data
				$data = unserialize($data['Ticket']['data']);
				
				return $data;
			}
		}
		return false;
	}

	// Delete a used ticket
	function delete($ticket = null)
	{
		$this->garbage();
		if ($ticket)
		{
			$data = $this->Ticket->findByHash($ticket);
			if ( is_array($data) && is_array($data['Ticket']) )
			{
				return $data = $this->Ticket->delete($data['Ticket']['id']);
			}
		}
		return false;
	}

	// Remove old tickets
	function garbage()
	{		
		$deadline = date('Y-m-d H:i:s', time() - (7 * 24 * 60 * 60)); // keep tickets for 24h.
		$data = $this->Ticket->query('DELETE from tickets WHERE created < \''.$deadline.'\'');
	}
}

?>