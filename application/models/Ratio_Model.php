<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ratio_Model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_sipp = $this->load->database('db_sipp', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.
	}

	function get_ratio_all()
	{
		$this->db_sipp->select([
			'SUM(CASE WHEN a.tanggal_putusan IS NOT NULL THEN 1 ELSE 0 END) AS all_putus',
			'SUM(CASE WHEN a.tanggal_minutasi IS NOT NULL THEN 1 ELSE 0 END) AS all_minutasi',
			'SUM(CASE WHEN a.tanggal_minutasi IS NULL THEN 1 ELSE 0 END) AS all_no_minutasi',
			'SUM(CASE WHEN a.amar_putusan_dok IS NOT NULL THEN 1 ELSE 0 END) AS all_edoc',
			'SUM(CASE WHEN a.amar_putusan_dok IS NULL THEN 1 ELSE 0 END) AS all_no_edoc',
		]);
		$this->db_sipp->from('perkara_putusan AS a');
		$this->db_sipp->join('perkara AS b', 'a.perkara_id = b.perkara_id', 'left');
		$this->db_sipp->where_not_in('b.alur_perkara_id', [112, 113, 114]);
		return $this->db_sipp->get()->row();
	}

	function get_ratio_summary($year = null, $month = null)
	{
		// Query 1: Perkara sudah di daftar belum ada file gugatan
		$query1 = $this->db_sipp
			->select("'belum_ada_gugatan_summary' AS kategori", false)
			->select("SUM(CASE WHEN petitum_dok IS NULL THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN petitum_dok IS NOT NULL THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara");
		if ($year) {
			$query1->where('YEAR(tanggal_pendaftaran)', $year);
			if ($month) {
				$query1->where('MONTH(tanggal_pendaftaran)', $month);
			}
		}
		$query1 = $query1->get_compiled_select();

		// Query 2: Perkara sudah putus belum ada Bas
		$query2 = $this->db_sipp
			->select("'putus_belum_bas' AS kategori", false)
			->select("SUM(CASE WHEN tanggal_putusan IS NOT NULL AND edoc_bas IS NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN tanggal_putusan IS NOT NULL AND edoc_bas IS NOT NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara_jadwal_sidang")
			->join("perkara_putusan", "perkara_jadwal_sidang.perkara_id = perkara_putusan.perkara_id", "left");
		if ($year) {
			$query2->where('YEAR(tanggal_putusan)', $year);
			if ($month) {
				$query2->where('MONTH(tanggal_putusan)', $month);
			}
		}
		$query2 = $query2->get_compiled_select();

		// Query 3: Sidang Sudah dilaksanakan belum ada Bas
		$query3 = $this->db_sipp
			->select("'sidang_belum_bas' AS kategori", false)
			->select("SUM(CASE WHEN edoc_bas IS NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN edoc_bas IS NOT NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara_jadwal_sidang");
		if ($year) {
			$query3->where('YEAR(tanggal_sidang)', $year);
			if ($month) {
				$query3->where('MONTH(tanggal_sidang)', $month);
			}
		}
		$query3 = $query3->get_compiled_select();

		// Query 4: Data panggilan belum dilengkapi upload dokumen
		$query4 = $this->db_sipp
			->select("'relaas_belum_ada' AS kategori", false)
			->select("SUM(CASE WHEN doc_relaas IS NULL THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN doc_relaas IS NOT NULL THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara_pelaksanaan_relaas");
		if ($year) {
			$query4->where('YEAR(tanggal_relaas)', $year);
			if ($month) {
				$query4->where('MONTH(tanggal_relaas)', $month);
			}
		}
		$query4 = $query4->get_compiled_select();

		// Query 5: Perkara Sudah Putus belum ada dokumen putusan
		$query5 = $this->db_sipp
			->select("'belum_ada_edoc' AS kategori", false)
			->select("SUM(CASE WHEN amar_putusan_dok IS NULL THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN amar_putusan_dok IS NOT NULL THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara_putusan");
		if ($year) {
			$query5->where('YEAR(tanggal_putusan)', $year);
			if ($month) {
				$query5->where('MONTH(tanggal_putusan)', $month);
			}
		}
		$query5 = $query5->get_compiled_select();

		// Query 6: Upload dok putusan anonimasi
		$query6 = $this->db_sipp
			->select("'belum_anonimasi' AS kategori", false)
			->select("SUM(CASE WHEN amar_putusan_anonimisasi_dok IS NULL AND amar_putusan_dok IS NOT NULL THEN 1 ELSE 0 END) AS kosong", false)
			->select("SUM(CASE WHEN amar_putusan_anonimisasi_dok IS NOT NULL AND amar_putusan_dok IS NOT NULL THEN 1 ELSE 0 END) AS isi", false)
			->from("perkara_putusan");
		if ($year) {
			$query6->where('YEAR(tanggal_putusan)', $year);
			if ($month) {
				$query6->where('MONTH(tanggal_putusan)', $month);
			}
		}
		$query6 = $query6->get_compiled_select();

		$data = [];
		foreach ($this->db_sipp->query("($query1) UNION ($query2) UNION ($query3) UNION ($query4) UNION ($query5) UNION ($query6)")->result_array() as $row) {
			$data[$row['kategori']] = [
				'isi' => $row['isi'],
				'kosong' => $row['kosong'],
			];
		}
		return $data;
	}

	function get_ratio_dirput($year = null, $month = null)
	{
		$this->db_sipp->select([
			"SUM(CASE WHEN (b.filename LIKE '%anonimisasi%' AND link_dirput IS NOT NULL) THEN 1 ELSE 0 END) AS published",
			"COUNT(DISTINCT a.perkara_id) - COUNT(DISTINCT CASE WHEN (b.filename LIKE '%anonimisasi%' AND link_dirput IS NOT NULL) THEN a.perkara_id END) AS not_published"
		]);
		$this->db_sipp->from('perkara_putusan a');
		$this->db_sipp->join('dirput_dokumen b', 'a.perkara_id = b.perkara_id', 'left');

		if ($year) {
			$this->db_sipp->where('YEAR(a.tanggal_putusan)', $year);
			if ($month) {
				$this->db_sipp->where('MONTH(a.tanggal_putusan)', $month);
			}
		}

		return $this->db_sipp->get()->row();
	}

	function get_ratio_antrian()
	{
		$this->db_sipp->select([
			'SUM(1) AS total',
			'SUM(CASE WHEN `status` = 0 THEN 1 ELSE 0 END) AS dirput_antrian',
			'SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END) AS dirput_sukses',
			'SUM(CASE WHEN `status` = -1 THEN 1 ELSE 0 END) AS dirput_error'
		]);
		$this->db_sipp->from('dirput_antrian');
		return $this->db_sipp->get()->row();
	}

	function get_ratio_bas()
	{
		$this->db_sipp->select([
			'ROUND(isi * 100 / total, 2) AS kinerja_bas',
		]);
		$this->db_sipp->from('
			(SELECT 
				SUM(CASE WHEN tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS total,
				SUM(CASE WHEN edoc_bas IS NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS kosong,
				SUM(CASE WHEN edoc_bas IS NOT NULL AND tanggal_sidang < NOW() THEN 1 ELSE 0 END) AS isi
			FROM perkara_jadwal_sidang
			WHERE YEAR(tanggal_sidang) = YEAR(NOW())) AS bas
		');
		return $this->db_sipp->get()->row();
	}

	function get_ratio()
	{
		$this->db_sipp->select([
			'C.sisa_tahun_lalu AS sisa_tahun_lalu',
			'C.masuk AS masuk',
			'C.masuk_ecourt AS masuk_ecourt',
			'C.masuk_bulan_ini AS masuk_bulan_ini',
			'C.masuk_hari_ini AS masuk_hari_ini',
			'C.ecourt_bulan_ini AS ecourt_bulan_ini',
			'C.ecourt_bulan_ini AS ecourt_bulan_ini',
			'C.ecourt_hari_ini AS ecourt_hari_ini',
			'C.minutasi AS minutasi',
			'C.sisa_perkara AS sisa_perkara',
			'C.putus AS putus',
			'C.putus_bulan_ini AS putus_bulan_ini',
			'C.putus_hari_ini AS putus_hari_ini',
			'C.belum_minutasi AS belum_minutasi',
			'(SELECT VALUE FROM sys_config WHERE id = 62) AS namaPN',
			'(SELECT VALUE FROM sys_config WHERE id = 80) AS versiSIPP',
			'ROUND(SUM(C.minutasi) * 100 / (SUM(C.masuk) + SUM(C.sisa_tahun_lalu)), 2) AS kinerja_perkara',
			'ROUND(masuk_ecourt * 100 / masuk, 2) AS kinerja_ecourt',
		]);

		$this->db_sipp->from('(SELECT
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) - 1 AND (YEAR(B.tanggal_minutasi) >= YEAR(NOW()) OR (B.tanggal_minutasi IS NULL OR B.tanggal_minutasi = "")) THEN 1 ELSE 0 END) AS sisa_tahun_lalu,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) THEN 1 ELSE 0 END) AS masuk,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) AND MONTH(A.tanggal_pendaftaran) = MONTH(NOW()) THEN 1 ELSE 0 END) AS masuk_bulan_ini,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) AND DATE(A.tanggal_pendaftaran) = DATE(NOW()) THEN 1 ELSE 0 END) AS masuk_hari_ini,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) AND D.efiling_id IS NOT NULL THEN 1 ELSE 0 END) AS masuk_ecourt,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) AND D.efiling_id IS NOT NULL AND MONTH(A.tanggal_pendaftaran) = MONTH(NOW()) THEN 1 ELSE 0 END) AS ecourt_bulan_ini,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) = YEAR(NOW()) AND D.efiling_id IS NOT NULL AND DATE(A.tanggal_pendaftaran) = DATE(NOW()) THEN 1 ELSE 0 END) AS ecourt_hari_ini,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) AND YEAR(B.tanggal_minutasi) = YEAR(NOW()) THEN 1 ELSE 0 END) AS minutasi,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) AND (B.tanggal_minutasi) IS NULL THEN 1 ELSE 0 END) AS sisa_perkara,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) AND YEAR(B.tanggal_putusan) = YEAR(NOW()) THEN 1 ELSE 0 END) AS putus,
			SUM(CASE WHEN YEAR(B.tanggal_putusan) = YEAR(NOW()) AND MONTH(B.tanggal_putusan) = MONTH(NOW()) THEN 1 ELSE 0 END) AS putus_bulan_ini,
			SUM(CASE WHEN DATE(B.tanggal_putusan) = DATE(NOW()) THEN 1 ELSE 0 END) AS putus_hari_ini,
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) AND (YEAR(B.tanggal_putusan) <= YEAR(NOW()) AND (B.tanggal_minutasi IS NULL OR B.tanggal_minutasi = "")) THEN 1 ELSE 0 END) AS belum_minutasi
		FROM perkara AS A
		LEFT JOIN perkara_putusan AS B ON A.perkara_id = B.perkara_id
		LEFT JOIN perkara_efiling_id AS D ON A.perkara_id = D.perkara_id
		WHERE A.alur_perkara_id <> 114) AS C', FALSE);
		$this->db_sipp->group_by('C.sisa_tahun_lalu, C.masuk, C.masuk_bulan_ini, C.masuk_hari_ini, C.minutasi, C.sisa_perkara, C.putus, C.putus_bulan_ini, C.putus_hari_ini, C.belum_minutasi');
		return $this->db_sipp->get()->row();
	}

	function get_ratio2()
	{
		$this->db_sipp->select([
			'G.redaksi_hari_ini AS redaksi_hari_ini',
			'G.belum_input_putus AS belum_input_putus'
		]);

		$this->db_sipp->from('(SELECT
			SUM(CASE WHEN YEAR(A.tanggal_pendaftaran) <= YEAR(NOW()) AND DATE(B.tanggal_transaksi) = DATE(NOW()) AND (B.jenis_biaya_id = 157) THEN 1 ELSE 0 END) AS redaksi_hari_ini,
			SUM(CASE WHEN (B.jenis_biaya_id = 157) AND (C.tanggal_putusan IS NULL) THEN 1 ELSE 0 END) AS belum_input_putus
		FROM perkara AS A
		LEFT JOIN perkara_biaya AS B ON A.perkara_id = B.perkara_id
		LEFT JOIN perkara_putusan AS C ON A.perkara_id = C.perkara_id
		WHERE A.alur_perkara_id <> 114) AS G', FALSE);
		return $this->db_sipp->get()->row();
	}

	function get_ratio3()
	{
		$subquery = $this->db_sipp->select('b.perkara_id')
			->from('perkara_biaya AS b')
			->where('b.jenis_biaya_id', '157')
			->get_compiled_select();

		$this->db_sipp->select('COUNT(*) AS putus_belum_redaksi')
			->from('perkara_putusan AS a')
			->where("a.perkara_id NOT IN ($subquery)", NULL, FALSE); // Use FALSE to prevent escaping

		return $this->db_sipp->get()->row();
	}

	function get_ratio4()
	{
		$this->db_sipp->select('
			SUM(CASE 
				WHEN (DATE(a.tanggal_transaksi) IS NOT NULL 
					AND (DATE(b.tanggal_putusan) <> DATE(a.tanggal_transaksi)) 
					AND a.jenis_biaya_id = "157") 
				THEN 1 ELSE 0 
			END) AS selisih_redaksi_putus', FALSE);
		$this->db_sipp->from('perkara_biaya AS a');
		$this->db_sipp->join('perkara_putusan AS b', 'a.perkara_id = b.perkara_id', 'left');
		$this->db_sipp->where('b.perkara_id NOT LIKE', '11870');
		$this->db_sipp->where('b.perkara_id NOT IN (SELECT perkara_id FROM perkara_verzet)', NULL, FALSE);

		$subquery = '(' . $this->db_sipp->get_compiled_select() . ') AS K';

		$this->db_sipp->select([
			'K.selisih_redaksi_putus',
			'@nilai_selisih_redaksi_putus := ROUND(K.selisih_redaksi_putus) AS nilai_selisih_redaksi_putus',
			'(CASE WHEN @nilai_selisih_redaksi_putus = "0" THEN "#ffffff" ELSE "#ff0000" END) AS warna_selisih_redaksi_putus'
		], FALSE);

		$this->db_sipp->from($subquery, FALSE);

		return $this->db_sipp->get()->row();
	}

	function get_ratio5()
	{
		$this->db_sipp->select([
			'SUM(CASE WHEN (YEAR(A.tanggal_putusan) = YEAR(NOW()) AND (B.filename LIKE "%putusan_anonimisasi%" OR B.filename IS NULL)) THEN 1 ELSE 0 END) AS diputus',
			'SUM(CASE WHEN (YEAR(A.tanggal_putusan) = YEAR(NOW()) AND (B.filename LIKE "%putusan_anonimisasi%")) THEN 1 ELSE 0 END) AS diupload',
			'SUM(CASE WHEN (YEAR(A.tanggal_putusan) = YEAR(NOW()) AND (B.filename IS NULL OR B.filename = "")) THEN 1 ELSE 0 END) AS belumupload'
		], FALSE);

		$this->db_sipp->from('perkara_putusan AS A');
		$this->db_sipp->join('dirput_dokumen AS B', 'A.perkara_id = B.perkara_id', 'left');

		$subquery = '(' . $this->db_sipp->get_compiled_select() . ') AS J';

		$this->db_sipp->select([
			'J.diputus AS diputus',
			'J.diupload AS diupload',
			'J.belumupload AS belumupload',
			'@kinerja_dirput := ROUND(SUM(J.diupload) * 100 / (SUM(J.diputus)), 2) AS kinerja_dirput'
		], FALSE);

		$this->db_sipp->from($subquery, FALSE);

		return $this->db_sipp->get()->row();
	}
}
