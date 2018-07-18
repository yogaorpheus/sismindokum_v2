<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_crud extends CI_Controller {
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->library('authentifier');

		$this->load->model('status');
		$this->load->model('sertifikat');
		$this->load->model('log_database');

		$this->load->model('distrik');
		$this->load->model('lembaga');
		$this->load->model('dasar_hukum');
		$this->load->model('menu');
		$this->load->model('jenis_sertifikat');
		$this->load->model('sub_jenis_sertifikat');
		$this->load->model('unit');
	}

	public function upload_file_lampiran()
	{
		$file_path = "";

		$config['upload_path']          = './assets/lampiran/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png|pdf|docx|doc';
       	$config['remove_spaces']		= true;

		$this->load->library('upload', $config);
		$test_upload = $this->upload->do_upload('lampiran');
		
		if (! $test_upload)
		{
			$this->authentifier->set_flashdata('error', 3);
		}
		else
		{
			$file = $this->upload->data();
			$file_path = base_url('assets/lampiran')."/".$file['file_name'];
		}

		return $file_path;
	}

	//-------------------------------------- SEMUA DATA PERTANAHAN -----------------------------------------------
	public function pertanahan_edit($id_sertifikat)
	{
		$data_pertanahan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pertanahan");

		$data_pertanahan['tanggal_terbit'] = DateTime::createFromFormat('Y-m-d', $data_pertanahan['tanggal_sertifikasi'])->format('m/d/Y');
		$data_pertanahan['tanggal_berakhir'] = DateTime::createFromFormat('Y-m-d', $data_pertanahan['tanggal_kadaluarsa'])->format('m/d/Y');

		$jenis_distrik = $this->distrik->get_all_distrik();
		$lembaga = $this->lembaga->get_all_lembaga();

		$id_menu2 = $this->menu->get_id_menu2('pertanahan');
		$dasar_hukum = $this->dasar_hukum->get_dasar_hukum_by_menu($id_menu2);

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('pertanahan');
		$sub_jenis_sertifikat = $this->sub_jenis_sertifikat->get_sub_jenis_by_id_jenis_sertifikat($id_jenis_sertifikat);

		$data = array(
			'data_pertanahan'		=> $data_pertanahan,
			'distrik' 				=> $jenis_distrik,
			'lembaga'				=> $lembaga,
			'dasar_hukum'			=> $dasar_hukum,
			'sub_jenis_sertifikat'	=> $sub_jenis_sertifikat
			);
		return $this->template->load_view('form', 'edit_pertanahan', $data);
	}

	public function pertanahan_update()
	{
		$file_path = $this->upload_file_lampiran();

		$input = $this->input->post();

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('pertanahan');
		$tanggal_terbit = DateTime::createFromFormat('m/d/Y', $input['tanggal_terbit'])->format('Y-m-d');
		$tanggal_berakhir = DateTime::createFromFormat('m/d/Y', $input['tanggal_berakhir'])->format('Y-m-d');

		$data = array(
			'id_sertifikat'				=> $input['id_sertifikat'],
			'id_dasar_hukum_sertifikat'	=> $input['referensi_pertanahan'],
			'id_lembaga_sertifikat'		=> $input['lembaga'],
			'id_jenis_sertifikat'		=> $id_jenis_sertifikat,
			'id_sub_jenis_sertifikat'	=> $input['jenis_sertifikat'],
			'id_distrik_sertifikat'		=> $input['distrik'],
			'no_sertifikat'				=> $input['no_sertifikat'],
			'judul_sertifikat'			=> $input['lokasi_sertifikat'],
			'tanggal_sertifikasi'		=> $tanggal_terbit,
			'tanggal_kadaluarsa'		=> $tanggal_berakhir,
			'keterangan'				=> $input['keterangan'],
			'jabatan_pic'				=> $this->authentifier->get_user_detail()['posisi_pegawai'],
			'dibuat_oleh'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'status_sertifikat'			=> 3
			);
		// Status sertifikat masih menggunakan nilai default
		if (!is_null($file_path))
		{
			$data['file_sertifikat'] = $file_path;
		}

		$result = $this->sertifikat->update_data_sertifikat($data);
		
		if ($result)
		{
			$log_data = array(
				'nama_tabel'		=> 'sertifikat',
				'id_pegawai'		=> $this->authentifier->get_user_detail()['id_pegawai'],
				'id_status_log'		=> $this->status->get_id_status_by_nama("melakukan edit"),
				'id_data'			=> $input['id_sertifikat']
				);
			$id_log = $this->log_database->write_log($log_data);
			// Insert data sukses
			$this->authentifier->set_flashdata('error', 1);
		}
		else
		{
			// Insert data gagal
			$this->authentifier->set_flashdata('error', 2);
		}

		return redirect('data/pertanahan');
	}

	public function pertanahan_delete($id_sertifikat)
	{
		$data_pertanahan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pertanahan");
		$json_data = json_encode($data_pertanahan);

		$log_data = array(
			'nama_tabel'				=> "sertifikat",
			'json_data_before_delete'	=> $json_data,
			'id_pegawai'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'id_status_log'				=> $this->status->get_id_status_by_nama("melakukan delete"),
			'id_data'					=> $id_sertifikat
			);
		$id_log = $this->log_database->write_log($log_data);

		$result = $this->sertifikat->delete_sertifikat_by_id($id_sertifikat, $data_pertanahan['id_jenis_sertifikat']);

		if ($result) 
		{
			$this->authentifier->set_flashdata('error', 1);	// Delete berhasil
		}
		else
		{
			$this->log_database->delete_log_by_id($id_log);
			$this->authentifier->set_flashdata('error', 2);	// Delete gagal
		}

		return redirect ('data/pertanahan');
	}

	public function pertanahan_review($id_sertifikat)
	{
		$data_pertanahan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pertanahan");

		return redirect($data_pertanahan['file_sertifikat']);
	}
	//-------------------------- DATA APAPUN TERKAIT PERTANAHAN BERAKHIR DISINI ----------------------------------

	//--------------------------------- SEMUA DATA SERTIFIKAT LAIK OPERASI -----------------------------------------------
	public function slo_edit($id_sertifikat)
	{
		$data_slo = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "slo");

		$data_slo['tanggal_terbit'] = DateTime::createFromFormat('Y-m-d', $data_slo['tanggal_sertifikasi'])->format('m/d/Y');
		$data_slo['tanggal_berakhir'] = DateTime::createFromFormat('Y-m-d', $data_slo['tanggal_kadaluarsa'])->format('m/d/Y');

		$jenis_distrik = $this->distrik->get_all_distrik();
		$lembaga = $this->lembaga->get_all_lembaga();
		$unit = $this->unit->get_all_unit();

		$id_menu2 = $this->menu->get_id_menu2('slo');
		$dasar_hukum = $this->dasar_hukum->get_dasar_hukum_by_menu($id_menu2);

		$data = array(
			'data_slo'		=> $data_slo,
			'distrik' 		=> $jenis_distrik,
			'lembaga'		=> $lembaga,
			'dasar_hukum'	=> $dasar_hukum,
			'unit'			=> $unit
			);
		return $this->template->load_view('form', 'edit_slo', $data);
	}

	public function slo_update()
	{
		$file_path = $this->upload_file_lampiran();

		$input = $this->input->post();

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('slo');
		$tanggal_terbit = DateTime::createFromFormat('m/d/Y', $input['tanggal_terbit'])->format('Y-m-d');
		$tanggal_berakhir = DateTime::createFromFormat('m/d/Y', $input['tanggal_berakhir'])->format('Y-m-d');

		$data = array(
			'id_sertifikat'				=> $input['id_sertifikat'],
			'id_dasar_hukum_sertifikat'	=> $input['referensi_slo'],
			'id_lembaga_sertifikat'		=> $input['lembaga'],
			'id_jenis_sertifikat'		=> $id_jenis_sertifikat,
			'id_unit_sertifikat'		=> $input['unit_sertifikasi'],
			'id_distrik_sertifikat'		=> $input['distrik'],
			'no_sertifikat'				=> $input['no_sertifikat'],
			'tanggal_sertifikasi'		=> $tanggal_terbit,
			'tanggal_kadaluarsa'		=> $tanggal_berakhir,
			'file_sertifikat'			=> $file_path,
			'keterangan'				=> $input['keterangan'],
			'jabatan_pic'				=> $this->authentifier->get_user_detail()['posisi_pegawai'],
			'dibuat_oleh'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'status_sertifikat'			=> 3
			);

		if (!is_null($file_path))
		{
			$data['file_sertifikat'] = $file_path;
		}

		$result = $this->sertifikat->update_data_sertifikat($data);
		
		if ($result)
		{
			$log_data = array(
				'nama_tabel'		=> 'sertifikat',
				'id_pegawai'		=> $this->authentifier->get_user_detail()['id_pegawai'],
				'id_status_log'		=> $this->status->get_id_status_by_nama("melakukan edit"),
				'id_data'			=> $input['id_sertifikat']
				);
			$id_log = $this->log_database->write_log($log_data);
			// Insert data sukses
			$this->authentifier->set_flashdata('error', 1);
		}
		else
		{
			// Insert data gagal
			$this->authentifier->set_flashdata('error', 2);
		}

		return redirect('data/slo');
	}

	public function slo_delete($id_sertifikat)
	{
		$data_slo = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "slo");
		$json_data = json_encode($data_slo);

		$log_data = array(
			'nama_tabel'				=> "sertifikat",
			'json_data_before_delete'	=> $json_data,
			'id_pegawai'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'id_status_log'				=> $this->status->get_id_status_by_nama("melakukan delete"),
			'id_data'					=> $id_sertifikat
			);
		$id_log = $this->log_database->write_log($log_data);

		$result = $this->sertifikat->delete_sertifikat_by_id($id_sertifikat, $data_slo['id_jenis_sertifikat']);

		if ($result) 
		{
			$this->authentifier->set_flashdata('error', 1);	// Delete berhasil
		}
		else
		{
			$this->log_database->delete_log_by_id($id_log);
			$this->authentifier->set_flashdata('error', 2);	// Delete gagal
		}

		return redirect ('data/slo');
	}

	public function slo_review($id_sertifikat)
	{
		$data_slo = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "slo");

		return redirect($data_slo['file_sertifikat']);
	}
	//--------------------- DATA APAPUN TERKAIT SERTIFIKAT LAIK OPERASI BERAKHIR DISINI ----------------------------------

	//-------------------------------------- SEMUA DATA SERTIFIKAT SDM -----------------------------------------------
	public function sertifikat_sdm()
	{

	}
	//-------------------------- DATA APAPUN TERKAIT SERTIFIKAT SDM BERAKHIR DISINI ----------------------------------

	//------------------------------------------ SEMUA DATA PERIZINAN -----------------------------------------------
	public function perizinan_edit($id_sertifikat)
	{
		$data_perizinan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "perizinan");

		$data_perizinan['tanggal_terbit'] = DateTime::createFromFormat('Y-m-d', $data_perizinan['tanggal_sertifikasi'])->format('m/d/Y');
		$data_perizinan['tanggal_berakhir'] = DateTime::createFromFormat('Y-m-d', $data_perizinan['tanggal_kadaluarsa'])->format('m/d/Y');

		$jenis_distrik = $this->distrik->get_all_distrik();
		$lembaga = $this->lembaga->get_all_lembaga();

		$id_menu2 = $this->menu->get_id_menu2('perizinan');
		$dasar_hukum = $this->dasar_hukum->get_dasar_hukum_by_menu($id_menu2);

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('perizinan');
		$sub_jenis_sertifikat = $this->sub_jenis_sertifikat->get_sub_jenis_by_id_jenis_sertifikat($id_jenis_sertifikat);

		$data = array(
			'data_perizinan'		=> $data_perizinan,
			'distrik' 				=> $jenis_distrik,
			'lembaga'				=> $lembaga,
			'dasar_hukum'			=> $dasar_hukum,
			'sub_jenis_sertifikat'	=> $sub_jenis_sertifikat
			);
		return $this->template->load_view('form', 'edit_perizinan', $data);
	}

	public function perizinan_update()
	{
		$file_path = $this->upload_file_lampiran();

		$input = $this->input->post();

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('perizinan');
		$tanggal_terbit = DateTime::createFromFormat('m/d/Y', $input['tanggal_terbit'])->format('Y-m-d');
		$tanggal_berakhir = DateTime::createFromFormat('m/d/Y', $input['tanggal_berakhir'])->format('Y-m-d');

		$data = array(
			'id_sertifikat'				=> $input['id_sertifikat'],
			'id_dasar_hukum_sertifikat'	=> $input['referensi_perizinan'],
			'id_lembaga_sertifikat'		=> $input['lembaga'],
			'id_jenis_sertifikat'		=> $id_jenis_sertifikat,
			'id_sub_jenis_sertifikat'	=> $input['jenis_perizinan'],
			'id_distrik_sertifikat'		=> $input['distrik'],
			'no_sertifikat'				=> $input['no_sertifikat'],
			'judul_sertifikat'			=> $input['peralatan'],
			'tanggal_sertifikasi'		=> $tanggal_terbit,
			'tanggal_kadaluarsa'		=> $tanggal_berakhir,
			'keterangan'				=> $input['keterangan'],
			'jabatan_pic'				=> $this->authentifier->get_user_detail()['posisi_pegawai'],
			'dibuat_oleh'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'status_sertifikat'			=> 3
			);

		if (!is_null($file_path))
		{
			$data['file_sertifikat'] = $file_path;
		}

		$result = $this->sertifikat->update_data_sertifikat($data);
		
		if ($result)
		{
			$log_data = array(
				'nama_tabel'		=> 'sertifikat',
				'id_pegawai'		=> $this->authentifier->get_user_detail()['id_pegawai'],
				'id_status_log'		=> $this->status->get_id_status_by_nama("melakukan edit"),
				'id_data'			=> $input['id_sertifikat']
				);
			$id_log = $this->log_database->write_log($log_data);
			// Insert data sukses
			$this->authentifier->set_flashdata('error', 1);
		}
		else
		{
			// Insert data gagal
			$this->authentifier->set_flashdata('error', 2);
		}

		return redirect('data/perizinan');
	}

	public function perizinan_delete($id_sertifikat)
	{
		$data_perizinan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "perizinan");
		$json_data = json_encode($data_perizinan);

		$log_data = array(
			'nama_tabel'				=> "sertifikat",
			'json_data_before_delete'	=> $json_data,
			'id_pegawai'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'id_status_log'				=> $this->status->get_id_status_by_nama("melakukan delete"),
			'id_data'					=> $id_sertifikat
			);
		$id_log = $this->log_database->write_log($log_data);

		$result = $this->sertifikat->delete_sertifikat_by_id($id_sertifikat, $data_perizinan['id_jenis_sertifikat']);

		if ($result) 
		{
			$this->authentifier->set_flashdata('error', 1);	// Delete berhasil
		}
		else
		{
			$this->log_database->delete_log_by_id($id_log);
			$this->authentifier->set_flashdata('error', 2);	// Delete gagal
		}

		return redirect ('data/perizinan');
	}

	public function perizinan_review($id_sertifikat)
	{
		$data_perizinan = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "perizinan");

		return redirect($data_perizinan['file_sertifikat']);
	}
	//------------------------------ DATA APAPUN TERKAIT PERIZINAN BERAKHIR DISINI ----------------------------------

	//-------------------------------------- SEMUA DATA PENGUJIAN ALAT K3 -----------------------------------------------
	public function pengujian_alat_k3_edit($id_sertifikat)
	{
		$data_pengujian = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pengujian alat k3");

		$data_pengujian['tanggal_terbit'] = DateTime::createFromFormat('Y-m-d', $data_pengujian['tanggal_sertifikasi'])->format('m/d/Y');
		$data_pengujian['tanggal_berakhir'] = DateTime::createFromFormat('Y-m-d', $data_pengujian['tanggal_kadaluarsa'])->format('m/d/Y');

		$jenis_distrik = $this->distrik->get_all_distrik();
		$lembaga = $this->lembaga->get_all_lembaga();

		$id_menu2 = $this->menu->get_id_menu2('pengujian alat k3');
		$dasar_hukum = $this->dasar_hukum->get_dasar_hukum_by_menu($id_menu2);

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('pengujian alat k3');
		$sub_jenis_sertifikat = $this->sub_jenis_sertifikat->get_sub_jenis_by_id_jenis_sertifikat($id_jenis_sertifikat);

		$data = array(
			'data_pengujian'		=> $data_pengujian,
			'distrik' 				=> $jenis_distrik,
			'lembaga'				=> $lembaga,
			'dasar_hukum'			=> $dasar_hukum,
			'sub_jenis_sertifikat'	=> $sub_jenis_sertifikat
			);
		return $this->template->load_view('form', 'edit_pengujian_alat_k3', $data);
	}

	public function pengujian_alat_k3_update()
	{
		$file_path = $this->upload_file_lampiran();

		$input = $this->input->post();

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('pengujian alat k3');
		$tanggal_terbit = DateTime::createFromFormat('m/d/Y', $input['tanggal_terbit'])->format('Y-m-d');
		$tanggal_berakhir = DateTime::createFromFormat('m/d/Y', $input['tanggal_berakhir'])->format('Y-m-d');

		$data = array(
			'id_sertifikat'				=> $input['id_sertifikat'],
			'id_dasar_hukum_sertifikat'	=> $input['referensi_pengujian'],
			'id_lembaga_sertifikat'		=> $input['lembaga'],
			'id_jenis_sertifikat'		=> $id_jenis_sertifikat,
			'id_sub_jenis_sertifikat'	=> $input['jenis_pengujian'],
			'id_distrik_sertifikat'		=> $input['distrik'],
			'no_sertifikat'				=> $input['no_sertifikat'],
			'judul_sertifikat'			=> $input['peralatan'],
			'tanggal_sertifikasi'		=> $tanggal_terbit,
			'tanggal_kadaluarsa'		=> $tanggal_berakhir,
			'keterangan'				=> $input['keterangan'],
			'jabatan_pic'				=> $this->authentifier->get_user_detail()['posisi_pegawai'],
			'dibuat_oleh'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'status_sertifikat'			=> 3
			);

		if (!is_null($file_path))
		{
			$data['file_sertifikat'] = $file_path;
		}

		$result = $this->sertifikat->update_data_sertifikat($data);
		
		if ($result)
		{
			$log_data = array(
				'nama_tabel'		=> 'sertifikat',
				'id_pegawai'		=> $this->authentifier->get_user_detail()['id_pegawai'],
				'id_status_log'		=> $this->status->get_id_status_by_nama("melakukan edit"),
				'id_data'			=> $input['id_sertifikat']
				);
			$id_log = $this->log_database->write_log($log_data);
			// Insert data sukses
			$this->authentifier->set_flashdata('error', 1);
		}
		else
		{
			// Insert data gagal
			$this->authentifier->set_flashdata('error', 2);
		}

		return redirect('data/pengujian_alat_k3');
	}

	public function pengujian_alat_k3_delete($id_sertifikat)
	{
		$data_pengujian = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pengujian alat k3");
		$json_data = json_encode($data_pengujian);

		$log_data = array(
			'nama_tabel'				=> "sertifikat",
			'json_data_before_delete'	=> $json_data,
			'id_pegawai'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'id_status_log'				=> $this->status->get_id_status_by_nama("melakukan delete"),
			'id_data'					=> $id_sertifikat
			);
		$id_log = $this->log_database->write_log($log_data);

		$result = $this->sertifikat->delete_sertifikat_by_id($id_sertifikat, $data_pengujian['id_jenis_sertifikat']);

		if ($result) 
		{
			$this->authentifier->set_flashdata('error', 1);	// Delete berhasil
		}
		else
		{
			$this->log_database->delete_log_by_id($id_log);
			$this->authentifier->set_flashdata('error', 2);	// Delete gagal
		}

		return redirect ('data/pengujian_alat_k3');
	}

	public function pengujian_alat_k3_review($id_sertifikat)
	{
		$data_pengujian = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "pengujian alat k3");

		return redirect($data_pengujian['file_sertifikat']);
	}
	//-------------------------- DATA APAPUN TERKAIT PENGUJIAN ALAT K3 BERAKHIR DISINI ----------------------------------

	//-------------------------------------------- SEMUA DATA LISENSI -----------------------------------------------
	public function lisensi_edit($id_sertifikat)
	{
		$data_lisensi = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "lisensi");

		$data_lisensi['tanggal_terbit'] = DateTime::createFromFormat('Y-m-d', $data_lisensi['tanggal_sertifikasi'])->format('m/d/Y');
		$data_lisensi['tanggal_berakhir'] = DateTime::createFromFormat('Y-m-d', $data_lisensi['tanggal_kadaluarsa'])->format('m/d/Y');

		$jenis_distrik = $this->distrik->get_all_distrik();
		$lembaga = $this->lembaga->get_all_lembaga();

		$id_menu2 = $this->menu->get_id_menu2('lisensi');
		$dasar_hukum = $this->dasar_hukum->get_dasar_hukum_by_menu($id_menu2);

		$data = array(
			'data_lisensi'		=> $data_lisensi,
			'distrik' 			=> $jenis_distrik,
			'lembaga'			=> $lembaga,
			'dasar_hukum'		=> $dasar_hukum
			);
		return $this->template->load_view('form', 'edit_lisensi', $data);
	}

	public function lisensi_update()
	{
		$file_path = $this->upload_file_lampiran();

		$input = $this->input->post();

		$id_jenis_sertifikat = $this->jenis_sertifikat->get_id_jenis_sertifikat('lisensi');
		$tanggal_terbit = DateTime::createFromFormat('m/d/Y', $input['tanggal_terbit'])->format('Y-m-d');
		$tanggal_berakhir = DateTime::createFromFormat('m/d/Y', $input['tanggal_berakhir'])->format('Y-m-d');

		$data = array(
			'id_sertifikat'				=> $input['id_sertifikat'],
			'id_dasar_hukum_sertifikat'	=> $input['referensi_lisensi'],
			'id_lembaga_sertifikat'		=> $input['lembaga'],
			'id_jenis_sertifikat'		=> $id_jenis_sertifikat,
			'id_distrik_sertifikat'		=> $input['distrik'],
			'no_sertifikat'				=> $input['no_sertifikat'],
			'judul_sertifikat'			=> $input['nama_lisensi'],
			'spesifikasi_lisensi'		=> $input['spesifikasi'],
			'tanggal_sertifikasi'		=> $tanggal_terbit,
			'tanggal_kadaluarsa'		=> $tanggal_berakhir,
			'keterangan'				=> $input['keterangan'],
			'jabatan_pic'				=> $this->authentifier->get_user_detail()['posisi_pegawai'],
			'dibuat_oleh'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'status_sertifikat'			=> 3
			);

		if (!is_null($file_path))
		{
			$data['file_sertifikat'] = $file_path;
		}

		$result = $this->sertifikat->update_data_sertifikat($data);
		
		if ($result)
		{
			$log_data = array(
				'nama_tabel'		=> 'sertifikat',
				'id_pegawai'		=> $this->authentifier->get_user_detail()['id_pegawai'],
				'id_status_log'		=> $this->status->get_id_status_by_nama("melakukan edit"),
				'id_data'			=> $input['id_sertifikat']
				);
			$id_log = $this->log_database->write_log($log_data);
			// Insert data sukses
			$this->authentifier->set_flashdata('error', 1);
		}
		else
		{
			// Insert data gagal
			$this->authentifier->set_flashdata('error', 2);
		}

		return redirect('data/lisensi');
	}

	public function lisensi_delete($id_sertifikat)
	{
		$data_lisensi = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "lisensi");
		$json_data = json_encode($data_lisensi);

		$log_data = array(
			'nama_tabel'				=> "sertifikat",
			'json_data_before_delete'	=> $json_data,
			'id_pegawai'				=> $this->authentifier->get_user_detail()['id_pegawai'],
			'id_status_log'				=> $this->status->get_id_status_by_nama("melakukan delete"),
			'id_data'					=> $id_sertifikat
			);
		$id_log = $this->log_database->write_log($log_data);

		$result = $this->sertifikat->delete_sertifikat_by_id($id_sertifikat, $data_lisensi['id_jenis_sertifikat']);

		if ($result) 
		{
			$this->authentifier->set_flashdata('error', 1);	// Delete berhasil
		}
		else
		{
			$this->log_database->delete_log_by_id($id_log);
			$this->authentifier->set_flashdata('error', 2);	// Delete gagal
		}

		return redirect ('data/lisensi');
	}

	public function lisensi_review($id_sertifikat)
	{
		$data_lisensi = $this->sertifikat->get_sertifikat_by_id($id_sertifikat, "lisensi");

		return redirect($data_lisensi['file_sertifikat']);
	}
	//-------------------------------- DATA APAPUN TERKAIT LISENSI BERAKHIR DISINI ----------------------------------
}