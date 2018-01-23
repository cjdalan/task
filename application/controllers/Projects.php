<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller {

	# Fetch Projects
	public function get()	{
		
		$project 			= $this->project->get($this->input->post('id'));
		$project->members 	= $this->project->get_members($this->input->post('id'));
		
		echo json_encode($project);
	}
	
	
	# Fetch All Projects
	public function get_all()
	{
		echo json_encode($this->project->get_many_by_user($this->session->user->id));
	}

	
	# Create Project
	public function insert() {
		
		$proj_id	= $this->project->insert([
			'title'	=> $this->input->post('name'),
			'admin' => $this->session->user->id
		]);
				
		$members 	= $this->input->post('members[]');
		$members[]	= $this->session->user->email_address;

		$this->project->update_members($proj_id, $members);

		echo json_encode(['team_id' => $proj_id]);
	}


	# Update Project
	public function update()
	{
		$proj_id	= $this->input->post('id');

		$members 	= $this->input->post('members[]');
		$members[]	= $this->session->user->email_address;
		
		$this->project->update($id, ['title' => $this->input->post('name')]);
		$this->project->update_members($id, $members);
	}


	# Check if user is a member
	public function validate_member() {
        
		$user 		= $this->user_model->get_by(['email_address' => $this->input->post('email')]);
		$proj_id 	= $this->input->post('proj_id');

		if($user != null) {
			
			if($proj_id != null) {

				$user->exists = $this->project->check_project($proj_id, $user->id) != null;
				
			} else {
				
				$user->exists = true;
			}
			echo json_encode($user);
		} else {

			echo json_encode(['exist' =>  false]);
		}
	}
	

	# Leave Projects
	public function leave_project() {
		
		$this->project->delete_member($this->input->post('proj_id'), $this->session->user->id);
	}
}
