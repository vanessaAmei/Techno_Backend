<?php use chriskacerguis\RestServer\RestController;

Class LoginAdmin extends RestController {
    public function __construct(){
        header('Access-Control-Allow-Origin: *');         
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");         
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");         
        parent::__construct();         
        $this->load->model('login_model');         
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
            'field' => 'username',                     
            'label' => 'username',                     
            'rules' => 'required'                 
        ]  
    ];     
    
    public function Rules() { return $this->rule; }     

    
    public function index_post(){
        $validation = $this->form_validation;         
        $rule = $this->Rules();            
        $validation->set_rules($rule);         
        if (!$validation->run()) {             
            return $this->response($this->form_validation->error_array());         
        }        
        $pegawai = new UserData();
        $pegawai->password = $this->post('password');
        $pegawai->username = $this->post('username');

        if($result= $this->login_model->verifyUser($pegawai)){
           $token = AUTHORIZATION::generateToken(['ID'=> $result['id'],'username'=> $result['username']]);
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'token'=> $token, 'pegawai'=> $result];
            
            return $this->response($response, $status, $result);
        }
        else
        {
            return $this->response('Gagal');
        }
    }
} 

Class UserData{    
    public $nama;
    public $password;     
    public $username; 
}