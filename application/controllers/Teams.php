<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams extends CI_Controller {


	public function __construct() {
		parent::__construct();
	}
	
	
	public function index() { #this view is for testing
		$data['teams'] = $this->team_model->get();
		$data['tasks'] = $this->task_model->get(1);
		$data['team_tasks'] = $this->task_model->get_team_tasks(1);
		$data['status'] = 1;
		$this->load->view('task/header', $data);		
		$this->load->view('test/index', $data);
		$this->load->view('task/index', $data);
	}


	public function post($id = null) {
		$team_id = 0;
		$members = [];
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			if($id != null) {
				$team_id = $id;
				$this->team_model->update_team($id, $this->input->post('name'));
			} else
				$team_id = $this->team_model->create_team(['name' => $this->input->post('name')]);
				
			$members = $this->input->post('members[]');
			$members[] = $this->session->user[0]->email_address;
			$this->team_model->update_members($team_id, $members);
		}
	}


	public function get($id = null)	{
		if($id != null)
			echo json_encode(array_merge((array)$this->team_model->get($id)[0], ['members' => (array)$this->team_model->get_members($id)]));
		else
			echo json_encode($this->team_model->get_all($this->session->user[0]->id));
	}	


	public function add_members() {
		if ($this->input->server('REQUEST_METHOD') == 'POST'){
			$peers = [];
			$peers = $this->input->post('peers[]'); #must get array[] from post
			$team_details = [
				'teams_id' => $this->input->post('team_id'),
				'peers' => $peers
			];

			$this->team_model->add_peers($team_details);
			redirect('teams');
		}
	}


	public function validate_member() {
        $user = $this->user_model->get('email_address', $this->input->post('email'));
        if($user !=  null)
            echo json_encode(array_merge((array)$user[0], ['exist' =>  true]));
        else
            echo json_encode(['exist' =>  false]);
    }
}