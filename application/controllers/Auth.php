<?php use chriskacerguis\RestServer\RestController;

Class Auth extends RestController {
    public function __construct(){
        header('Access-Control-Allow-Origin: *');         
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");         
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");         
        parent::__construct();         
        $this->load->model('UserModel');         
        $this->load->library('form_validation');     
        $this->load->helper(['jwt', 'authorization']);   
    }    
    public $rule = [  
        [                     
            'field' => 'password',                     
            'label' => 'password',                     
            'rules' => 'required'                 
        ],                 
        [                     
            'field' => 'email',                     
            'label' => 'email',                     
            'rules' => 'required|valid_email'                 
        ]  
    ];     
    
    public function Rules() {
        return $this->rule; 
    }     

    public function index_post(){
        $validation = $this->form_validation;         
        $rule = $this->Rules();            
        $validation->set_rules($rule);         
        if (!$validation->run()) {             
            return $this->response($this->form_validation->error_array());         
        }        

        $user = new UserData();
        $user->password = $this->post('password');
        $user->email = $this->post('email');

        if($result= $this->UserModel->verify($user)){
            $token = AUTHORIZATION::generateToken(['ID' => $result['id'],'Email' => $result['email']]);
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'token' => $token, 'user' => $result];
            return $this->response($response, $status, $result);
        }else{
            return $this->response('Gagal');
        }
    }
} 

Class UserData{     
    public $name;
    public $password;     
    public $email; 
}