<?php

// todo: NO PERMISSIONS!

class Admin extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();

		// Make sure to load the administrator library!
		$this->load->library('administrator');
		$this->load->model('vote_model');

		requirePermission("canViewAdmin");
	}

	public function index()
	{
		// Change the title
		$this->administrator->setTitle("Topsites");
		
		$topsites = $this->vote_model->getVoteSites();

		// Prepare my data
		$data = array(
			'url' => $this->template->page_url,
			'topsites' => $topsites
		);

		// Load my view
		$output = $this->template->loadPage("admin.tpl", $data);

		// Put my view in the main box with a headline
		$content = $this->administrator->box('Topsites', $output);

		// Output my content. The method accepts the same arguments as template->view
		$this->administrator->view($content, false, "modules/vote/js/admin.js");
	}

	/**
	 * Create a new vote site.
	 */
	public function create()
	{
		requirePermission('canCreate');

		$data["vote_sitename"] = $this->input->post("vote_sitename");
		$data["vote_url"] = $this->input->post("vote_url");
		$data["vote_image"] = $this->input->post("vote_image");
		$data["hour_interval"] = $this->input->post("hour_interval");
		$data["points_per_vote"] = $this->input->post("points_per_vote");
		$data["api_enabled"] = $this->input->post("api_enabled");

		$this->vote_model->add($data);

		// Add log
		$this->logger->createLog('Added topsite', $data['vote_sitename']);

		$this->plugins->onCreateSite($data);

		die('window.location.reload(true)');
	}

	/**
	 * Edit the vote site with the given id
	 * @param bool $id
	 */
	public function edit($id = false)
	{
		// Check for the permission
		requirePermission("canEdit");

		if(!is_numeric($id) || !$id)
		{
			die();
		}
		
		$topsite = $this->vote_model->getTopsite($id);
		
		if(!$topsite)
		{
			show_error("There is no topsite with ID ".$id);

			die();
		}
		
		// Change the title
		$this->administrator->setTitle($topsite['vote_sitename']);
			
		// Prepare my data
		$data = array(
			'url' => $this->template->page_url,
			'topsite' => $topsite
		);

		// Load my view
		$output = $this->template->loadPage("admin_edit.tpl", $data);

		// Put my view in the main box with a headline
		$content = $this->administrator->box('<a href="'.$this->template->page_url.'vote/admin">Topsites</a> &rarr; '.$topsite['vote_sitename'], $output);

		// Output my content. The method accepts the same arguments as template->view
		$this->administrator->view($content, false, "modules/vote/js/admin.js");
	}

	/**
	 * Save the details to the vote site with the given id.
	 * @param bool $id
	 */
	public function save($id = false)
	{
		// Check for the permission
		requirePermission("canEdit");

		if(!$id || !is_numeric($id))
		{
			die();
		}

		$data["vote_sitename"] = $this->input->post("vote_sitename");
		$data["vote_url"] = $this->input->post("vote_url");
		$data["vote_image"] = $this->input->post("vote_image");
		$data["hour_interval"] = $this->input->post("hour_interval");
		$data["points_per_vote"] = $this->input->post("points_per_vote");
		$data["api_enabled"] = $this->input->post("api_enabled");

		$this->vote_model->edit($id, $data);

		// Add log
		$this->logger->createLog('Edited topsite', $id);

		$this->plugins->onEditSite($id, $data);

		die('window.location="'.$this->template->page_url.'vote/admin"');
	}

	/**
	 * Delete the vote site with the given id.
	 * @param bool $id
	 */
	public function delete($id = false)
	{
		// Check for the permission
		requirePermission("canDelete");

		if(!$id || !is_numeric($id))
		{
			die();
		}

		$this->vote_model->delete($id);

		// Add log
		$this->logger->createLog('Deleted topsite', $id);

		$this->plugins->onDelete($id);
	}
}