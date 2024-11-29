<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Web_Model extends CI_Model
{
	function findOne($id)
	{
		$this->db->from('tmst_web');
		$this->db->where(['id' => $id]);

		if (($row = $this->db->get()->row())) {
			return $row;
		}

		$this->session->set_flashdata('error_message', 'Data web tidak ditemukan #' . $id);
		return redirect('site/error');
	}

	function find($where = [], $offset = null, $limit = null)
	{
		$this->db->from('tmst_web');

		$this->_populateWhere($where);
		$this->db->where('is_active', 1);

		if ($limit) {
			$this->db->limit($limit);
		}

		if ($offset) {
			$this->db->offset($offset);
		}

		return $this->db->order_by('(CASE WHEN category="Socmed" THEN 0 WHEN category="Lokal" THEN 1 WHEN category="Web" THEN 2 WHEN category="PTA Surabaya" THEN 3 WHEN category="Badilag" THEN 4 WHEN category="MA" THEN 5 WHEN category="Lain-lain" THEN 6 ELSE 99 END) ASC, (CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END) ASC, `order` ASC, `name` ASC')->get()->result();
	}

	function insert($data)
	{
		$this->db->trans_start();

		$this->db->insert('tmst_web', $data);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	function update($id, $data)
	{
		return $this->db->where('id', $id)->update('tmst_web', $data);
	}

	function delete($id)
	{
		$this->db->delete('tmst_web', array('id' => $id));
		return $this->db->affected_rows() > 0;
	}

	function num_rows($where = [])
	{
		$this->db->from('tmst_web');

		$this->_populateWhere($where);

		return $this->db->get()->num_rows();
	}

	private function _populateWhere($where)
	{
		foreach ($where as $key => $q) {
			switch ($key) {
				case 'like':
					if (is_array($q) && !empty($q)) {
						for ($i = 0; $i < count($q); $i++) {
							if ($i == 0) {
								$this->db->like($q[$i]);
							} else {
								$this->db->or_like($q[$i]);
							}
						}
					}
					break;
				default:
					if (is_array($q) && !empty($q)) {
						foreach ($q as $w) {
							$this->db->where($w);
						}
					}
					break;
			}
		}
	}
}
