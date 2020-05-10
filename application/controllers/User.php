<?php use chriskacerguis\RestServer\RestController;

Class User extends RestController {
	public function __construct() {
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding, Authorization");
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->library('form_validation');
		$this->load->helper(['jwt','authorization']);
        $this->load->library('session');
	}
	public function getbyid_get($id)
	{
		return $this->returnData($this->db->get_where('user',['id' => $id])->row(),false);
	}
	public function getbyemail_post($id=null)
	{		
	    $email=$this->post('email');
		return $this->returnData($this->db->get_where('user',['email' => $email])->row(),false);
	}
	public function index_get() {      
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        if($data['status'] == 401){
            return $this->returnData($data['msg'],true);
        }  
		return $this->returnData($this->db->get('user')->result(), false);
	}

	public function index_post($id=null) {
		$validation=$this->form_validation;
		$rule=$this->UserModel->rules();
		
		if($id==null) {
			array_push($rule, [ 'field'=> 'password',
				'label'=> 'password',
				'rules'=> 'required'
				],
				[ 'field'=> 'email',
				'label'=> 'email',
				'rules'=> 'required|valid_email|is_unique[user.email]'
				]);
		}

		else {
			array_push($rule,
				[ 'field'=> 'email',
				'label'=> 'email',
				'rules'=> 'valid_email'
				]);
		}

		$validation->set_rules($rule);

		if ( !$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
		}

		$user = new UserData();
		$user->nama=$this->post('nama');
		$user->email=$this->post('email');
		$user->nohp=$this->post('nohp');
		$user->alamat=$this->post('alamat');
		$user->password=$this->post('password');
		$user->activationcode=$this->post('activationcode');
		$user->status=$this->post('status');


		if($id==null) {
			$response=$this->UserModel->store($user);
		}

		else {
			$response=$this->UserModel->update($user, $id);
		}

		return $this->returnData($response['msg'], $response['error']);
	}
	public function index_put() {
        $id = $this->put('id');
        $data = array(
            'nama'    => $this->put('nama'),
			'nohp'    => $this->put('nohp'),
			'alamat'    => $this->put('alamat')
		);
        $this->db->where('id', $id);
        $update = $this->db->update('user', $data);
        if ($update) {
            $this->response($data, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
     public function index_delete($id = null){
     if($id == null){
        return $this->returnData('Parameter Id Tidak Ditemukan', true);
     }
     $response = $this->UserModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
     }

	public function returnData($msg, $error) {
		$response['error']=$error;
		$response['message']=$msg;
		return $this->response($response, 200);
	}
	
	private function verify_request()
    {
        $headers = $this->input->request_headers();
        if(isset($headers['Authorization'])){
            $header =  $headers['Authorization'];
        }else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $response;
        }
        $token = explode(" ", $header)[1];
        try {
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            } else {
                $response = ['status' => 200, 'msg' => $data];
            }
            return $response;
        } catch (Exception $e) {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            return $response;
        }
    }
    public function register(){
		$this->form_validation->set_rules('email', 'Email', 'valid_email|required');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[30]');
        //$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');
 
        if ($this->form_validation->run() == FALSE) { 
         	$this->load->view('register', $this->data);
		}
		else{
			//get user inputs
			$email = $this->input->post('email');
			$password = $this->input->post('password');
 
			//generate simple random code
			$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$code = substr(str_shuffle($set), 0, 12);
 
			//insert user to users table and get id
			$user['email'] = $email;
			$user['password'] = $password;
			$user['activationcode'] = $code;
			$user['status'] = false;
			$id = $this->UserModel->insert($user);
 
			//set up email
			$config = array(
		  		'protocol' => 'smtp',
		  		'smtp_host' => 'ssl://smtp.google.com',
		  		'smtp_port' => 465,
		  		'smtp_user' => 'van.amei140@gmail.com', // change it to yours
		  		'smtp_pass' => 'Hamilton140', // change it to yours
		  		'mailtype' => 'html',
		  		'charset' => 'utf-8',
		  		'wordwrap' => TRUE
			);
 
			$message = 	"
						<html>
						<head>
							<title>Verification Code</title>
						</head>
						<body>
							<h2>Thank you for Registering.</h2>
							<p>Your Account:</p>
							<p>Email: ".$email."</p>
							<p>Password: ".$password."</p>
							<p>Please click the link below to activate your account.</p>
							<h4><a href='".base_url()."user/activate/".$id."/".$code."'>Activate My Account</a></h4>
						</body>
						</html>
						";
 
			$this->load->library('email', $config);
			$this->email->initialize($config);
			$this->email->set_mailtype("html");
			$this->email->set_newline("\r\n");
		    $this->email->from('van.amei140@gmail.com', 'Admin');
		    $this->email->to($email);
		    $this->email->subject('Signup Verification Email');
		    $this->email->message($message);
 
		    //sending email
		    if($this->email->send()){
		    	$this->session->set_flashdata('message','Activation code sent to email');
		    }
		    else{
		    	$this->session->set_flashdata('message', $this->email->print_debugger());
 
		    }
 
        	redirect('register');
		}
 
	}
 
	public function activate(){
		$id =  $this->uri->segment(3);
		$code = $this->uri->segment(4);
 
		//fetch user details
		$user = $this->UserModel->getUser($id);
 
		//if code matches
		if($user['activationcode'] == $code){
			//update user active status
			$data['status'] = true;
			$query = $this->UserModel->activate($data, $id);
 
			if($query){
				$this->session->set_flashdata('message', 'User activated successfully');
			}
			else{
				$this->session->set_flashdata('message', 'Something went wrong in activating account');
			}
		}
		else{
			$this->session->set_flashdata('message', 'Cannot activate account. Code didnt match');
		}
 
		redirect('register');
 
	}
    
}

Class UserData {
	public $nama;
	public $email;
	public $nohp;
	public $alamat;
	public $password;
	public $activationcode;
	public $status;
}
