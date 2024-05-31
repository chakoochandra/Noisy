<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sipp_Model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_sipp = $this->load->database('db_sipp', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
	}

	function find_sidangs($where, $offset = null, $limit = null)
	{
		$this->db_sipp->select('
			perkara_jadwal_sidang.id,
			perkara_jadwal_sidang.perkara_id,
			perkara_jadwal_sidang.urutan,
			perkara_jadwal_sidang.sidang_keliling,
			perkara_penetapan.majelis_hakim_kode, 
			CONCAT(perkara_penetapan.majelis_hakim_text,"</br>",perkara_penetapan.panitera_pengganti_text) AS majelis_hakim_text,
			perkara.nomor_perkara, 
			perkara.jenis_perkara_nama,
			perkara.para_pihak,
			CONCAT("[P] ", perkara.pihak1_text, (CASE WHEN perkara.pihak2_text IS NOT NULL THEN CONCAT("</br>[T] ", perkara.pihak2_text) ELSE "" END)) AS pihak,
			tanggal_sidang, 
			jam_sidang, 
			sampai_jam,
			ruangan_id,
			ruangan_sidang.nama AS nama_ruang, 
			agenda, 
			alasan_ditunda,
			ikrar_talak,
			(CASE WHEN ikrar_talak = "Y" THEN "Ya" ELSE "Bukan" END) AS ikrar,
			(CASE 
				WHEN perkara.alur_perkara_id = 16 THEN 998 #permohonan
				WHEN perkara.tahapan_terakhir_id = 12 THEN 999 #sidang pertama
				WHEN (agenda LIKE "%putusan%" OR agenda LIKE "%musyawarah%") THEN 800
				WHEN (agenda LIKE "%lanjutan%") THEN 997
				WHEN (
					agenda LIKE "%memanggil%" 
					OR agenda LIKE "%mermanggil%"
					OR agenda LIKE "%panggil%"
				) THEN (CASE WHEN perkara.jenis_perkara_id IN (346, 347) THEN (CASE WHEN agenda LIKE "%bukti%" THEN 850 ELSE 900 END) ELSE 997 END)
				ELSE 997 
			END) AS tahapan,
			(CASE WHEN perkara.tahapan_terakhir_id = 12 THEN 3 #sidang pertama
				ELSE 5
			END) AS jam,
			"" AS tanggal_tunda,
			"" AS keterangan_tunda,
			"" AS agenda_lanjutan,
			perkara.pihak1_text AS pihak1, 
			perkara.pihak2_text AS pihak2, 
			pihak.nama AS nama_P, 
			(CASE WHEN pengacara_P.id IS NULL THEN pihak.telepon ELSE NULL END) AS telepon_P,
			pihak_T.nama AS nama_T, 
			(CASE WHEN pengacara_T.id IS NULL THEN pihak_T.telepon ELSE NULL END) AS telepon_T,
			pengacara_P.nama AS nama_pengacara_P,
			pengacara_P.telepon AS telepon_pengacara_P,
			pengacara_T.nama AS nama_pengacara_T,
			pengacara_T.telepon AS telepon_pengacara_T
		');
		$this->db_sipp->from('perkara_jadwal_sidang');
		$this->db_sipp->join('perkara', 'perkara.perkara_id=perkara_jadwal_sidang.perkara_id', 'left');
		$this->db_sipp->join('perkara_penetapan', 'perkara_penetapan.perkara_id=perkara.perkara_id', 'left');
		$this->db_sipp->join('ruangan_sidang', 'ruangan_sidang.id=perkara_jadwal_sidang.ruangan_id', 'left');
		$this->db_sipp->join('perkara_pihak1', 'perkara.perkara_id = perkara_pihak1.perkara_id AND perkara_pihak1.urutan = 1', 'left');
		$this->db_sipp->join('perkara_pihak2', 'perkara.perkara_id = perkara_pihak2.perkara_id AND perkara_pihak2.urutan = 1', 'left');
		$this->db_sipp->join('pihak', 'perkara_pihak1.pihak_id = pihak.id', 'left');
		$this->db_sipp->join('pihak pihak_T', 'perkara_pihak2.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_P', 'perkara.perkara_id = perkara_pengacara_P.perkara_id AND perkara_pengacara_P.pihak_id = pihak.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_T', 'perkara.perkara_id = perkara_pengacara_T.perkara_id AND perkara_pengacara_T.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('pihak pengacara_P', 'perkara_pengacara_P.pengacara_id = pengacara_P.id', 'left');
		$this->db_sipp->join('pihak pengacara_T', 'perkara_pengacara_T.pengacara_id = pengacara_T.id', 'left');
		$this->db_sipp->where($where);

		if ($limit) {
			$this->db_sipp->limit($limit);
		}
		if ($offset) {
			$this->db_sipp->offset($offset);
		}
		$this->db_sipp->group_by('perkara_jadwal_sidang.id');
		$this->db_sipp->order_by(
			'tanggal_sidang,
			(nama_ruang * -1) DESC,
			`ikrar` DESC,
			`tahapan` ASC, 
			(CASE WHEN (
				agenda LIKE "%memanggil%"
				OR agenda LIKE "%mermanggil%"
				OR agenda LIKE "%panggil%"
			) THEN (CASE WHEN perkara_jadwal_sidang.urutan <= 3 THEN tahapan ELSE 901 END) ELSE tahapan END) ASC, 
			`perkara`.`perkara_id` ASC'
		);

		if (($result = $this->db_sipp->get()) === FALSE) {
			throw new Exception(isset($this->db_sipp->error()['message']) ? $this->db_sipp->error()['message'] : 'Unknown database error');
		}
		return $result->result();
	}

	function num_sidangs($where)
	{
		return $this->db_sipp->from('perkara_jadwal_sidang')
		->where($where)->get()->num_rows();
	}

	function find_court_calendar($where, $offset = null, $limit = null)
	{
		$this->db_sipp->select(
			'perkara_court_calendar.*,
			perkara.perkara_id,
			perkara.nomor_perkara, 
			pihak1_text AS pihak1, 
			pihak2_text AS pihak2, 
			pihak.nama AS nama_P, 
			(CASE WHEN pengacara_P.id IS NULL THEN pihak.telepon ELSE NULL END) AS telepon_P,
			pihak_T.nama AS nama_T, 
			(CASE WHEN pengacara_T.id IS NULL THEN pihak_T.telepon ELSE NULL END) AS telepon_T,
			para_pihak,
			pengacara_P.nama AS nama_pengacara_P,
			pengacara_P.telepon AS telepon_pengacara_P,
			pengacara_T.nama AS nama_pengacara_T,
			pengacara_T.telepon AS telepon_pengacara_T'
		);
		$this->db_sipp->from('perkara_court_calendar');
		$this->db_sipp->join('perkara', 'perkara.perkara_id=perkara_court_calendar.perkara_id', 'left');
		$this->db_sipp->join('perkara_pihak1', 'perkara.perkara_id = perkara_pihak1.perkara_id', 'left');
		$this->db_sipp->join('perkara_pihak2', 'perkara.perkara_id = perkara_pihak2.perkara_id', 'left');
		$this->db_sipp->join('pihak', 'perkara_pihak1.pihak_id = pihak.id', 'left');
		$this->db_sipp->join('pihak pihak_T', 'perkara_pihak2.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_P', 'perkara.perkara_id = perkara_pengacara_P.perkara_id AND perkara_pengacara_P.pihak_id = pihak.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_T', 'perkara.perkara_id = perkara_pengacara_T.perkara_id AND perkara_pengacara_T.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('pihak pengacara_P', 'perkara_pengacara_P.pengacara_id = pengacara_P.id', 'left');
		$this->db_sipp->join('pihak pengacara_T', 'perkara_pengacara_T.pengacara_id = pengacara_T.id', 'left');
		$this->db_sipp->where($where);

		if ($limit) {
			$this->db_sipp->limit($limit);
		}
		if ($offset) {
			$this->db_sipp->offset($offset);
		}
		$this->db_sipp->order_by(
			'rencana_tanggal ASC, rencana_jam ASC'
		);

		if (($result = $this->db_sipp->get()) === FALSE) {
			throw new Exception(isset($this->db_sipp->error()['message']) ? $this->db_sipp->error()['message'] : 'Unknown database error');
		}
		return $result->result();
	}

	function num_court_calendar($where)
	{
		return $this->db_sipp->from('perkara_court_calendar')->where($where)->get()->num_rows();
	}

	function find_sisa_panjar($where, $offset = null, $limit = null)
	{
		$this->db_sipp->select(
			'perkara.perkara_id,
			perkara.nomor_perkara, 
			pihak1_text AS pihak1, 
			pihak.nama AS nama_P, 
			(CASE WHEN pengacara_P.id IS NULL THEN pihak.telepon ELSE NULL END) AS telepon_P,
			pengacara_P.nama AS nama_pengacara_P,
			pengacara_P.telepon AS telepon_pengacara_P,
			para_pihak,
			tanggal_putusan,
			status_putusan.nama AS jenis_putusan,
			jenis_perkara_nama,
			`pemasukan`,
			`pengeluaran`,
			(`pemasukan` - `pengeluaran`) AS sisa_panjar'
		);
		$this->db_sipp->from('perkara');
		$this->db_sipp->join('perkara_penetapan', 'perkara_penetapan.perkara_id=perkara.perkara_id', 'left');
		$this->db_sipp->join('perkara_putusan', 'perkara_putusan.perkara_id=perkara.perkara_id', 'left');
		$this->db_sipp->join('status_putusan', 'status_putusan.id=perkara_putusan.status_putusan_id', 'left');
		$this->db_sipp->join('perkara_ikrar_talak', 'perkara_ikrar_talak.perkara_id=perkara.perkara_id', 'left');
		$this->db_sipp->join('perkara_pihak1', 'perkara.perkara_id = perkara_pihak1.perkara_id', 'left');
		$this->db_sipp->join('pihak', 'perkara_pihak1.pihak_id = pihak.id', 'left');
		// $this->db_sipp->join('perkara_pihak2', 'perkara.perkara_id = perkara_pihak2.perkara_id', 'left');
		// $this->db_sipp->join('pihak pihak_T', 'perkara_pihak2.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_P', 'perkara.perkara_id = perkara_pengacara_P.perkara_id AND perkara_pengacara_P.pihak_id = pihak.id', 'left');
		$this->db_sipp->join('pihak pengacara_P', 'perkara_pengacara_P.pengacara_id = pengacara_P.id', 'left');
		$this->db_sipp->join('v_sum_perkara_biaya', '.perkara.perkara_id = v_sum_perkara_biaya.perkara_id', 'left');
		$this->db_sipp->where($where);
		// $this->db_sipp->where('(pemasukan - pengeluaran) >', 0);

		if ($limit) {
			$this->db_sipp->limit($limit);
		}
		if ($offset) {
			$this->db_sipp->offset($offset);
		}
		$this->db_sipp->order_by(
			'tanggal_putusan DESC'
		);
		$this->db_sipp->group_by('perkara.perkara_id');

		if (($result = $this->db_sipp->get()) === FALSE) {
			throw new Exception(isset($this->db_sipp->error()['message']) ? $this->db_sipp->error()['message'] : 'Unknown database error');
		}
		return $result->result();
	}

	function num_sisa_panjar($where)
	{
		return $this->db_sipp->from('perkara')
		->join('perkara_penetapan', 'perkara_penetapan.perkara_id=perkara.perkara_id', 'left')
		->join('perkara_putusan', 'perkara_putusan.perkara_id=perkara.perkara_id', 'left')
		->join('perkara_ikrar_talak', 'perkara_ikrar_talak.perkara_id=perkara.perkara_id', 'left')
		->join('v_sum_perkara_biaya', 'perkara.perkara_id = v_sum_perkara_biaya.perkara_id', 'left')
		->where($where)
			->where('(pemasukan - pengeluaran) >', 0)
			->get()->num_rows();
	}

	function find_ac($where, $offset = null, $limit = null)
	{
		$this->db_sipp->select(
			'perkara.perkara_id,
			perkara.nomor_perkara, 
			pihak_T.nama AS nama_T, 
			(CASE WHEN pengacara_T.id IS NULL THEN pihak_T.telepon ELSE NULL END) AS telepon_T,
			pengacara_T.nama AS nama_pengacara_T,
			pengacara_T.telepon AS telepon_pengacara_T,
			para_pihak,
			tgl_akta_cerai,
			nomor_akta_cerai,
			no_seri_akta_cerai,
			jenis_cerai'
		);
		$this->db_sipp->from('perkara_akta_cerai');
		$this->db_sipp->join('perkara', 'perkara.perkara_id=perkara_akta_cerai.perkara_id', 'left');
		$this->db_sipp->join('perkara_pihak2', 'perkara.perkara_id = perkara_pihak2.perkara_id', 'left');
		$this->db_sipp->join('pihak pihak_T', 'perkara_pihak2.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('perkara_pengacara perkara_pengacara_T', 'perkara.perkara_id = perkara_pengacara_T.perkara_id AND perkara_pengacara_T.pihak_id = pihak_T.id', 'left');
		$this->db_sipp->join('pihak pengacara_T', 'perkara_pengacara_T.pengacara_id = pengacara_T.id', 'left');
		$this->db_sipp->where($where);

		if ($limit) {
			$this->db_sipp->limit($limit);
		}
		if ($offset) {
			$this->db_sipp->offset($offset);
		}
		$this->db_sipp->order_by(
			'tgl_akta_cerai DESC'
		);

		if (($result = $this->db_sipp->get()) === FALSE) {
			throw new Exception(isset($this->db_sipp->error()['message']) ? $this->db_sipp->error()['message'] : 'Unknown database error');
		}
		return $result->result();
		// var_dump($this->db_sipp->last_query());
		// exit;
	}

	function num_ac($where)
	{
		return $this->db_sipp->from('perkara_akta_cerai')->where($where)->get()->num_rows();
	}
}
