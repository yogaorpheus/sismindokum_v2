<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SDM extends CI_Model {

	public function __construct() 
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_all_data_sdm($kode_distrik, $nama_status = null)
	{
		if (!is_null($nama_status))
		{
			$this->db->where('nama_status', $nama_status);
			$this->db->where('penggunaan_tabel_status', "sertifikat");
			$id_status = $this->db->get('status')->row_array()['id_status'];	
		}

		if ($kode_distrik != 'Z')
		{
			$this->db->where('kode_distrik', $kode_distrik);
			$id_distrik = $this->db->get('distrik')->row_array()['id_distrik'];
		}

		$this->db->select('sdm.*, distrik.nama_distrik, pegawai.nama_lengkap_pegawai, status.nama_status');
		if (isset($id_status))
		{
			$this->db->where('sdm.status_sdm', $id_status);
		}

		if (isset($id_distrik))
		{
			$this->db->where('sdm.id_distrik', $id_distrik);
		}

		$this->db->join('distrik', 'distrik.id_distrik = sdm.id_distrik', 'left');
		$this->db->join('pegawai', 'pegawai.id_pegawai = sdm.id_pegawai', 'left');
		$this->db->join('lembaga', 'lembaga.id_lembaga = sdm.id_lembaga', 'left');
		$this->db->join('status', 'status.id_status = sdm.status_sdm', 'inner');

		$query = $this->db->get('sdm');

		return $query->result_array();
	}

	public function get_data_sdm($id_sdm)
	{
		$this->db->where('id_sdm', $id_sdm);
		$query = $this->db->get('sdm');

		return $query->row_array();
	}
}