<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {
	private $table='user';
	public  $id;
	public  $nama;
	public  $nohp;
	public  $alamat;
	public  $email;
    public  $password;
	public  $rule=[ [ 'field'=>'nama',
	'label'=>'nama',
	'rules'=>'required'
	],
	];
	public function Rules() {
		return $this->rule;
	}
	public function response_getbyid($id)
	{
        return $this->db->get_where($this->table,['id' => $id])->row();
	}
	public function response_getbyemail($id=null)
	{
	    $email=$request->email;
        return $this->db->get_where($this->table,['email' => $email])->row();
	}
	public function getAll() {
		return $this->db->get('user')->result();
	}

	public function store($request) {
		$this->nama=$request->nama;
		$this->email=$request->email;
		$this->alamat=$request->alamat;
		$this->nohp=$request->nohp;
		$this->password=password_hash($request->password, PASSWORD_BCRYPT);

		if($this->db->insert($this->table, $this)) {
			return ['msg'=>'Berhasil',
			'error'=>false];
		}
		return ['msg'=>'Gagal',
		'error'=>true];
	}

	public function update($request, $id) {
		$updateData=[
		'nama'=>$request->nama,
        'nohp'=>$request->nohp,
		'alamat'=>$request->alamat
	];

		if($this->db->where('id', $id)->update($this->table, $updateData)) {
			return ['msg'=>'Berhasil',
			'error'=>false];
		}

		return ['msg'=>'Gagal',
		'error'=>true];
	}

	public function destroy($id) {
		if (empty($this->db->select('*')->where(array('id'=> $id))->get($this->table)->row())) return ['msg'=>'Sukses',
		'error'=>true];

		if($this->db->delete($this->table, array('id'=> $id))) {
			return ['msg'=>'Berhasil',
			'error'=>false];
		}

		return ['msg'=>'Gagal',
		'error'=>true];
	}
	
	public function verify($request){
        $user = $this->db->select('*')->where(array('email' => $request->email))->get($this->table)->row_array();
        if(!empty($user) && password_verify($request->password, $user['password'])){
            return $user;
        }else{
            return false;
        }
    }
    public function activate($data, $id){
		$this->db->where('user.id', $id);
		return $this->db->update('user', $data);
	}

}

?>
