<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artikel extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('M_Web');
		$level = $this->session->userdata('level');
		if ($level!='admin') {
			redirect('404');
		}
	}
	
	public function index()
	{
		redirect('artikel/view');
	}

	public function view($aksi='', $id='')
	{
		$ceks 	 = $this->session->userdata('username');
		$id_user = $this->session->userdata('id_user');
		if(!isset($ceks)) {
			redirect('web/login');
		}else{
			$data['user']  = $this->db->get_where('tbl_pakar', "username='$ceks'");

			$this->db->order_by('kode_artikel', 'ASC');
			$data['sql'] = $this->db->get("tbl_artikel");

				if ($aksi == 't') {
					$p = "tambah";
					$data['judul_web'] 	  = "Tambah Artikel";
				}elseif ($aksi == 'e') {
					$p = "edit";
					$data['judul_web'] 	  = "Edit Artikel";
					$data['query'] = $this->db->get_where("tbl_artikel", array('kode_artikel' => "$id"))->row();
					if ($data['query']->kode_artikel=='') {redirect('404');}
				}
				elseif ($aksi == 'h') {
					$cek_data = $this->db->get_where("tbl_artikel", array('kode_artikel' => "$id"));
					if ($cek_data->num_rows() != 0) {
							$this->db->delete('tbl_artikel', array('kode_artikel' => $id));
							$this->session->set_flashdata('msg',
								'
								<div class="alert alert-success alert-dismissible" role="alert">
									 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										 <span aria-hidden="true">&times;&nbsp; &nbsp;</span>
									 </button>
									 <strong>Sukses!</strong> Berhasil dihapus.
								</div>'
							);
							redirect('artikel/view');
					}else {
						redirect('404_content');
					}
				}else{
					$p = "index";
					$data['judul_web'] 	  = "Data Artikel";
				}

					$this->load->view('users/header', $data);
					$this->load->view("users/artikel/$p", $data);
					$this->load->view('users/footer');

					date_default_timezone_set('Asia/Jakarta');
					$tgl = date('Y-m-d H:i:s');

					if (isset($_POST['btnsimpan'])) {
						
						$judul       = $this->input->post('judul');
						$isi         = $this->input->post('isi');

						$config['upload_path']    = './assets/upload/';
						$config['allowed_types']  = 'gif|jpg|png|jpeg';
						$config['max_size']       = 2048000;
						$config['max_width']      = 20000;
						$config['max_height']     = 20000;
						$config['encrypt_name']   = TRUE;

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('image'))
						{
							$errors = array('error' => $this->upload->display_errors());
						}else{
							$image_uploaded	= $this->upload->data();
							$image      = $image_uploaded['file_name']; 
						}
						
						$image      = $image_uploaded['file_name']; 
						$data            = array('kode_artikel'  =>'',
												 'judul'         =>$judul,
												 'isi'      	 =>$isi,
												 'gambar'        =>$image,
												 'tgl_artikel'   =>$tgl
												 );
						
						if ($this->M_Web->insert($data) == TRUE) {
							$this->session->set_flashdata('msg',
								'
								<div class="alert alert-success alert-dismissible" role="alert">
									 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										 <span aria-hidden="true">&times;&nbsp; &nbsp;</span>
									 </button>
									 <strong>Sukses!</strong> Berhasil disimpan.
								</div>'
							);
						}else{
							$this->session->set_flashdata('msg',
								'
								<div class="alert alert-danger alert-dismissible" role="alert">
									 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										 <span aria-hidden="true">&times;&nbsp; &nbsp;</span>
									 </button>
									 <strong>Gagal!</strong> Gagal disimpan.
								</div>'
							);
						}
						redirect('artikel/view/t');
					}


					if (isset($_POST['btnupdate'])) {
						$kode_artikel       = $this->input->post('kode_artikel');
						$judul    		    = $this->input->post('judul');
						$isi	            = $this->input->post('isi');
						$tgl_artikel        = $this->input->post('tgl_artikel');
						$gambar        		= $this->input->post('gambar');

						$config['upload_path']    = './assets/upload/';
						$config['allowed_types']  = 'gif|jpg|png|jpeg';
						$config['max_size']       = 2048000;
						$config['max_width']      = 20000;
						$config['max_height']     = 20000;
						$config['encrypt_name']   = TRUE;

						$this->load->library('upload');
						$this->upload->initialize($config);
						if(!$this->upload->do_upload('image'))
						{
							$errors = array('error' => $this->upload->display_errors());
						}else{
							$image_uploaded	= $this->upload->data();
							$image      = $image_uploaded['file_name']; 
						}
						
						$image      = $image_uploaded['file_name']; 						
						$data            = array('judul'         =>$judul,
												 'isi'      	 =>$isi,
												 'gambar'        =>$image,
												 'tgl_artikel'   =>$tgl_artikel
												 );
						
						if ($this->M_Web->edit($data, $kode_artikel) == TRUE) {
							unlink('./assets/upload/'.$gambar);
							$this->session->set_flashdata('msg',
								'
								<div class="alert alert-success alert-dismissible" role="alert">
									 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										 <span aria-hidden="true">&times;&nbsp; &nbsp;</span>
									 </button>
									 <strong>Sukses!</strong> Berhasil disimpan.
								</div>'
							);
						}else{
							$this->session->set_flashdata('msg',
								'
								<div class="alert alert-danger alert-dismissible" role="alert">
									 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
										 <span aria-hidden="true">&times;&nbsp; &nbsp;</span>
									 </button>
									 <strong>Gagal!</strong> Gagal disimpan.
								</div>'
							);
						}
						redirect('artikel/view/e/'.$id);
					}
		}
	}

}
