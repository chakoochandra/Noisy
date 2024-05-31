<div class="callout callout-primary">
	<h5>Notifikasi Akta Cerai</h5>

	<?php $range = get_notif_range($selectedType); ?>
	<p>
		Akta Cerai dari tanggal <?php echo formatDate(add_days_to_date(date('Y-m-d'), $range)) ?>
	</p>
</div>

<div id='container-info-<?php echo $selectedType ?>' class="d-flex ">
	<?php echo '<a onclick="sendNotification(this, \'' . base_url("api/send_notif/{$selectedType}") . '\')" class="btn btn-outline-primary" data-confirm-message="Anda yakin akan mengirimkan notifikasi?" style="height: 40px;"><i class="fa fa-paper-plane" aria-hidden="true"></i>&nbsp;&nbsp;Kirim Notifikasi</a>' ?>
</div>

<?php echo $this->$paginationVar->get_summary() ?>

<table class="table table-hover table-sticky">
	<thead>
		<tr>
			<th>No.</th>
			<th>Tanggal Akta Cerai</th>
			<th>Info Perkara</th>
			<th>Pihak</th>
			<th>No Telp T</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($list) {
			$i = 0;
			$r = 0;
			$enable = true;
			foreach ($list as $j => $row) {
				echo "<tr>";
				echo "	<td align='center'>" . ($j + $offset) . "</td>";
				$tanggal = formatDate($row->tgl_akta_cerai, "%d %b %Y");
				echo "	<td>{$tanggal}<br/>{$row->nomor_akta_cerai}<br/>{$row->no_seri_akta_cerai}</td>";
				echo "  <td>{$row->nomor_perkara}<br />$row->jenis_cerai</td>";
				echo "	<td>{$row->para_pihak}</td>";

				$btnNoT = '';
				foreach (cleansePhoneNumbers($row->telepon_T) as $no) {
					$btnNoT .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}"), $no, [
						'title'    => 'Lihat Notifikasi',
						'class'    => 'btn btn-xs btn-outline-success btn-modal',
					]);
				}
				$btnNoPengacaraT = '';
				foreach (cleansePhoneNumbers($row->telepon_pengacara_T) as $no) {
					$btnNoPengacaraT .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}"), $no, [
						'title'    => 'Lihat Notifikasi',
						'class'    => 'btn btn-xs btn-outline-success btn-modal',
					]);
				}
				echo "	<td class='" . ($btnNoT || $btnNoPengacaraT ? '' : 'bg-red') . "'>{$row->nama_T}";
				if ($btnNoT) {
					echo "<br/>$btnNoT";
				}
				if ($row->nama_pengacara_T) {
					echo "<div class='d-flex justify-content-center item-aligns-center mb-1 text-xs'><span class='badge badge-status badge-primary'>Pengacara: {$row->nama_pengacara_T}</span></div>";
					if ($btnNoPengacaraT) {
						echo "$btnNoPengacaraT";
					}
				}
				echo "	</td>";
				echo "</tr>";
			}
		} else {
			echo "<tr><td colspan='4'>Tidak ada data</td></tr>";
		}
		?>
	</tbody>
</table>

<?php echo $this->$paginationVar->create_links() ?>

<script>
	$(document).ready(function() {
		checkGateway('<?php echo $selectedType ?>');
	});
</script>