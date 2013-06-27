<?php

class Vote_model extends CI_Model
{
	private $vote_sites;

	/**
	 * Connect to the database
	 */
	public function __construct()
	{
		parent::__construct();
		
		if($this->config->item('delete_old_votes'))
			$this->deleteOld();

		//init our vote sites
		$this->vote_sites = $this->getVoteSites();
		
		if($this->vote_sites)
		{
			foreach($this->vote_sites as $key => $value)
			{
				$this->vote_sites[$key]['canVote'] = $this->canVote($value['id']);
				$this->vote_sites[$key]['nextVote'] = $this->getNextTime($this->vote_sites[$key]['canVote'], $value['id']);
			}
		}
	}
	
	public function getVoteSites()
	{
		if($this->vote_sites)
		{
			return $this->vote_sites;
		}
		else
		{
			$query = $this->db->query("SELECT * FROM vote_sites");

			if($query->num_rows())
			{
				$result = $query->result_array();
				return $result;
			}
			else
			{
				return false;
			}
		}
	}
	
	public function getVoteSite($id)
	{
		foreach($this->vote_sites as $key => $value)
		{
			if($value['id'] == $id)
			{
				return $this->vote_sites[$key];
			}
		}
	}

	public function getTopsite($id)
	{
		$query = $this->db->query("SELECT * FROM vote_sites WHERE id=?", array($id));
		
		if($query->num_rows())
		{
			$result = $query->result_array();

			return $result[0];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Gets the vote site by url, handy for the postback scripts.
	 */
	public function getVoteSiteByUrl($url)
	{
		$query = $this->db->query("SELECT * FROM vote_sites WHERE vote_url LIKE '%".$url."%'");
		
		if($query->num_rows())
		{
			$result = $query->result_array();

			return $result[0];
		}
		else
		{
			return false;
		}
	}
	
	public function getVoteLog($vote_site_id, $user_ip, $time_back, $ipLock = false)
	{
		if(!$ipLock)
		{
			$this->db->select('*')->from('vote_log')->where('vote_site_id', $vote_site_id)->where(array('ip' => $user_ip, 'time > ' => $time_back, 'user_id' => $this->user->getId()));
		}
		else
		{
			$this->db->select('*')->from('vote_log')->where('vote_site_id', $vote_site_id)->where(array('user_id' => $this->user->getId(), 'time > ' => $time_back));
		}
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			//Voted already
			return false;
		}
		else 
		{
			return true;
		}
	}

	private function deleteOld()
	{
		$time_back = time() - (24 * 60 * 60);
		$this->db->query("DELETE FROM vote_log WHERE `time` < (SELECT MAX(hour_interval) * 3600 FROM vote_sites)", array($time_back));
	}

	public function getNextTime($canVote, $vote_site_id)
	{
		if(!$canVote)
		{
			$user_ip = $this->input->ip_address();
			
			$vote_site = $this->getVoteSite($vote_site_id);
			$time_interval = $vote_site['hour_interval'];
			$time_back = time() - ($time_interval * 60 * 60);
		
			// Check for account or not
			if(!$this->config->item('vote_ip_lock') && !$vote_site['api_enabled'])
			{
				$this->db->select('*')->from('vote_log')->where('vote_site_id', $vote_site_id)->where(array('ip' => $user_ip, 'time > ' => $time_back, 'user_id' => $this->user->getId()));
			}
			else
			{
				$this->db->select('*')->from('vote_log')->where('vote_site_id', $vote_site_id)->where(array('user_id' => $this->user->getId(), 'time > ' => $time_back));
			}

			$query = $this->db->get();

			if($query->num_rows())
			{
				$row = $query->result_array();

				$nextTime = $row[0]['time'] + ($time_interval * 60 * 60);
				$untilNext = $nextTime - time();

				return $this->template->formatTime($untilNext);
			}
			else
			{
				return false;
			}
		}
	}
	
	public function vote_log($user_id, $user_ip, $voteSiteId)
	{
		//Insert into the logs.
		$data = array(
			'vote_site_id' => $voteSiteId,
			'user_id' => $user_id,
			'ip' => $user_ip,
			'time' => time()
		);
		
		$insert = $this->db->insert('vote_log', $data);

		if($insert)
		{
			$this->db->query("UPDATE account_data SET total_votes = total_votes + 1 WHERE id = ?", array($this->user->getId()));
		
			//Return true if we voted
			return true;
		}
		else
		{
			return false;
		}
	}

	public function updateVp($user_id, $extra_vp)
	{
		//Update account vp
		$this->db->query("UPDATE account_data SET `vp` = vp + ? WHERE id=?", array($extra_vp, $user_id));
		
		//Update the session
		$this->session->set_userdata('vp', $this->user->getVp() + $extra_vp);

		$this->updateMonthlyVotes();
	}

	private function updateMonthlyVotes()
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM monthly_votes WHERE month=?", array(date("Y-m")));

		$row = $query->result_array();

		if($row[0]['total'])
		{
			$this->db->query("UPDATE monthly_votes SET amount = amount + 1 WHERE month=?", array(date("Y-m")));
		}
		else
		{
			$this->db->query("INSERT INTO monthly_votes(month, amount) VALUES(?, ?)", array(date("Y-m"), 1));
		}
	}
	
	public function canVote($vote_site_id)
	{
		//Get the user ip
		$user_ip = $this->input->ip_address();
		
		//Get the vote site
		$vote_site = $this->getVoteSite($vote_site_id);
		
		//Get the hours between each vote
		$time_interval = $vote_site['hour_interval'];
		
		//Calculate the that should tell if they voted already or not.
		$time_back = time() - ($time_interval * 60 * 60);

		// Check for account or not
		$result = $this->getVoteLog($vote_site_id, $user_ip, $time_back, $this->config->item('vote_ip_lock'));

		return $result;
	}
	
	public function getCustomCallbackUrl($voteSiteId)
	{
		$query = $this->db->query("SELECT * FROM vote_site_callback WHERE site_id = ?", array($voteSiteId));
		$result = $query->result_array();
		
		return $result[0]['custom_callback_url'];
	}

	public function add($data)
	{
		$this->db->insert("vote_sites", $data);
	}

	public function edit($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('vote_sites', $data);
	}

	public function delete($id)
	{
		$this->db->query("DELETE FROM vote_site_callback WHERE site_id=?", array($id));
		$this->db->query("DELETE FROM vote_sites WHERE id=?", array($id));
	}
}