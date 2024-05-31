<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends Core_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Whatsapp_Model', 'whatsapp');
	}

	function send_notif()
	{
		$auth = $this->_authenticate();
		if ($auth && $auth['status'] !== 200) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Anda tidak memiliki akses']);
			exit;
		}

		if (!WA_TEST_TARGET) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Variabel WA_TEST_TARGET belum diset pada tabel configs']);
			exit;
		}
		if (!DIALOGWA_API_URL) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Variabel DIALOGWA_API_URL belum diset pada tabel configs']);
			exit;
		}
		if (!DIALOGWA_TOKEN) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Variabel DIALOGWA_TOKEN belum diset pada tabel configs. Token didapat dari <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>']);
			exit;
		}
		if (!DIALOGWA_SESSION) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Variabel DIALOGWA_SESSION belum diset pada tabel configs. Buat sesi di <strong><a href="https://dialogwa.id/#paket-0" target="blank">https://dialogwa.id</a></strong>']);
			exit;
		}

		$type = $this->uri->segment(3) ?: 'antrian';
		$list = null;

		try {
			switch ($type) {
				case 'antrian':
				case 'sidang':
					$list = $this->sipp->find_sidangs(get_notif_criteria($type));
					break;
				case 'calendar':
					$list = $this->sipp->find_court_calendar(get_notif_criteria($type));
					break;
				case 'jurnal':
					$list = $this->sipp->find_sisa_panjar(get_notif_criteria($type));
					break;
				case 'ac':
					$list = $this->sipp->find_ac(get_notif_criteria($type));
					break;
			}
		} catch (\Exception $e) {
			$this->_sendProgress(['progress' => 100, 'message' => $e->getMessage()]);
			exit;
		}

		if (!$list) {
			$this->_sendProgress(['progress' => 100, 'message' => 'Tidak ada notifikasi yang perlu dikirim', 'status' => true]);
			exit;
		}

		$prevType = $type;
		$log = [];
		$i = $x = 0;
		foreach ($list as $item) {
			$i++;
			$progress = round($i * 100 / count($list));

			$type = $prevType;
			$noP = isset($item->telepon_pengacara_P) ? cleansePhoneNumbers($item->telepon_pengacara_P) : [];
			if (!$noP) {
				$noP = isset($item->telepon_P) ? cleansePhoneNumbers($item->telepon_P) : [];
			}
			$noT = isset($item->telepon_pengacara_T) ? cleansePhoneNumbers($item->telepon_pengacara_T) : [];
			if (!$noT) {
				$noT = isset($item->telepon_T) ? cleansePhoneNumbers($item->telepon_T) : [];
			}

			foreach ((is_development() ? cleansePhoneNumbers(WA_TEST_TARGET) : array_merge($noP, $noT)) as $no) {
				switch ($type) {
					case 'antrian':
						$reference = "{$item->tanggal_sidang}_{$item->jam_sidang}";
						break;
					case 'sidang':
						$reference = $item->tanggal_sidang;
						break;
					case 'calendar':
						$reference = "{$item->rencana_tanggal}_{$item->rencana_jam}";
						break;
					case 'jurnal':
						$reference = $item->sisa_panjar;
						break;
					case 'ac':
						$reference = $item->tgl_akta_cerai;
						break;
				}

				// hanya kirim notifikasi bila belum pernah dikirimi
				if (!$this->whatsapp->find(['where' => [
					['type' => $this->typesText[$type], 'phone_number' => $no, 'reference' => $reference, 'success <>' => 0],
				]])) {
					$data = [
						'type' => $this->typesText[$type],
						'reference' => $reference,
						'perkara_id' => $item->perkara_id,
						'text' => get_template($type, $item),
						'sent_by' => 'system',
					];

					$send = $this->_sendWA($no, $data['text']);

					$isSuccess = isset($send[$no]['status']) && $send[$no]['status'] == 200;
					$sentTime = isset($send[$no]['sent_time']) ? $send[$no]['sent_time'] : date('Y-m-d H:i:s');

					$statusCode = 0;
					$message = '';
					if (isset($send[$no]['status'])) {
						$message = $send[$no]['message'];
						switch ($send[$no]['status']) {
							case 200:
								$statusCode = 1;
								break;
							case 422:
								$statusCode = 2; // nomor tidak terdaftar pada whatsapp
								break;
						}
					} else if (isset($send['status'])) {
						$message = isset($send['response']) ? $send['response'] : $send['message'];
						$statusCode = $send['status'];
					}

					$temp = '';
					switch ($type) {
						case 'antrian':
						case 'sidang':
							$temp = "$item->nomor_perkara | Sidang " . formatDate($item->tanggal_sidang, "%A, %d %b %Y") . ($item->jam_sidang ? " | $item->jam_sidang" : '') . " | " . (in_array($no, $noP) ? 'P' : 'T') . " | {$no} | {$this->typesText[$type]} | $sentTime | "  . ($isSuccess ? 'TERKIRIM' : ('TIDAK TERKIRIM : ' . $message));
							break;
						case 'calendar':
							$temp = "$item->nomor_perkara | Agenda " . formatDate($item->rencana_tanggal, "%A, %d %b %Y") . " " . $item->rencana_jam . " | " . $item->rencana_agenda . " | " . (in_array($no, $noP) ? 'P' : 'T') . " | {$no} | {$this->typesText[$type]} | $sentTime | "  . ($isSuccess ? 'TERKIRIM' : ('TIDAK TERKIRIM : ' . $message));
							break;
						case 'jurnal':
							$temp = "$item->nomor_perkara | Putus " . formatDate($item->tanggal_putusan, "%d %b %Y") . " | " . add_currency_symbol($item->sisa_panjar) . " | " . (in_array($no, $noP) ? 'P' : 'T') . " | {$no} | {$this->typesText[$type]} | $sentTime | "  . ($isSuccess ? 'TERKIRIM' : ('TIDAK TERKIRIM : ' . $message));
							break;
						case 'ac':
							$temp = "$item->nomor_perkara | Akta Cerai " . formatDate($item->tgl_akta_cerai, "%d %b %Y") . " | " . $item->nomor_akta_cerai . " | " . (in_array($no, $noP) ? 'P' : 'T') . " | {$no} | {$this->typesText[$type]} | $sentTime | "  . ($isSuccess ? 'TERKIRIM' : ('TIDAK TERKIRIM : ' . $message));
							break;
					}
					$log[] = $temp;

					$this->whatsapp->insert(array_merge($data, [
						'phone_number' => $no,
						'sent_time' => $sentTime,
						'success' => $statusCode,
						'callback' => isset($send[$no]['reference']) ?  $send[$no]['reference'] : '',
						'note' => $message,
					]));

					$x++;
					$this->_sendProgress(['progress' => $progress, 'no' => $x, 'response' => $temp]);
				}
			}

			if (is_development() && $x == count(cleansePhoneNumbers(WA_TEST_TARGET))) {
				$progress = 100;
				break;
			}
		}

		$this->_sendProgress(['progress' => $progress]);

		if ($log) {
			foreach (cleansePhoneNumbers(WA_TEST_TARGET) as $no) {
				$text = '*LOG NOTIF*

🔸' . implode('
🔸', $log) . '
';
				$this->_sendWA($no, $text);
			}
		}
	}

	private function _sendProgress($data)
	{
		echo json_encode($data) . "\n"; // Add newline to split JSON objects
		ob_flush();
		flush();
	}

	private function _sendWA($target, $text)
	{
		$result = hit_api(DIALOGWA_API_URL . '/send-text', 'post',  [
			'session' => DIALOGWA_SESSION,
			'target' => $target,
			'message' => $text,
		], DIALOGWA_TOKEN);

		if ($result['status'] === false) {
			$temp = json_decode($result['response'], 1);
			return ['message' => isset($temp['message']) ? $temp['message'] : 'Terjadi Kesalahan!'];
		}

		sleep(60);

		$result = json_decode($result['response'], 1);
		if (!isset($result['data'])) {
			return $result;
		}

		$data = ['status' => $result['status'], 'sent_time' => date('Y-m-d H:i:s')];
		foreach ($result['data'] as $item) {
			$data[$item['target']] = $item;
		}
		return $data;
	}

	private function _authenticate()
	{
		if ($this->input->method() !== 'post') {
			return [
				'status' => 405,
				'error' => 'Endpoint tidak ditemukan'
			];
		}

		$headers = $this->input->request_headers();
		if (!isset($headers['Authorization'])) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}

		$authorizationHeader = $headers['Authorization'];
		if (!preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}

		if ($matches[1] != DIALOGWA_TOKEN) {
			return [
				'status' => 401,
				'error' => 'Tidak memiliki akses!'
			];
		}
	}
}
