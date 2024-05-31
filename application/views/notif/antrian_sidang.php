<div class="callout callout-primary">
	<h5>Notifikasi <?php echo ucfirst($selectedType) ?></h5>

	<?php $range = get_notif_range($selectedType); ?>
	<p>
		Data <?php echo $selectedType ?> tanggal <?php echo formatDate(add_days_to_date(date('Y-m-d'), $range[0])) . ($range[0] == $range[1] ? '' : (' - ' . formatDate(add_days_to_date(date('Y-m-d'), $range[1])))) ?>
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
			<th>Tanggal Sidang</th>
			<th>Info Perkara</th>
			<th>Pihak</th>
			<th>No Telp P</th>
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
				$tanggal = formatDate($row->tanggal_sidang, "%A, %d %b %Y");
				$jam = $selectedType == 'antrian' ? date('H:i', strtotime($row->jam_sidang)) : '';
				echo "	<td>{$tanggal}<br/>{$jam}</td>";
				echo "  <td>" . "{$row->nomor_perkara}<br/>{$row->agenda}</td>";
				echo "	<td>{$row->para_pihak}</td>";

				$btnNoP = '';
				foreach (cleansePhoneNumbers($row->telepon_P) as $no) {
					$btnNoP .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}/{$row->id}"), $no, [
						'title'    => 'Lihat Notifikasi',
						'class'    => 'btn btn-xs btn-outline-success btn-modal',
					]);
				}
				$btnNoPengacaraP = '';
				foreach (cleansePhoneNumbers($row->telepon_pengacara_P) as $no) {
					$btnNoPengacaraP .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}/{$row->id}"), $no, [
						'title'    => 'Lihat Notifikasi',
						'class'    => 'btn btn-xs btn-outline-success btn-modal',
					]);
				}
				echo "	<td class='" . ($btnNoP || $btnNoPengacaraP ? '' : 'bg-red') . "'>{$row->nama_P}";
				if ($btnNoP) {
					echo "<br/>$btnNoP";
				}
				if ($row->nama_pengacara_P) {
					echo "<div class='d-flex justify-content-center item-aligns-center mb-1 text-xs'><span class='badge badge-status badge-primary'>Pengacara: {$row->nama_pengacara_P}</span></div>";
					if ($btnNoPengacaraP) {
						echo "$btnNoPengacaraP";
					}
				}
				echo "	</td>";

				$btnNoT = '';
				foreach (cleansePhoneNumbers($row->telepon_T) as $no) {
					$btnNoT .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}/{$row->id}"), $no, [
						'title'    => 'Lihat Notifikasi',
						'class'    => 'btn btn-xs btn-outline-success btn-modal',
					]);
				}
				$btnNoPengacaraT = '';
				foreach (cleansePhoneNumbers($row->telepon_pengacara_T) as $no) {
					$btnNoPengacaraT .= anchor(base_url("whatsapp/view_notif/{$row->perkara_id}/{$no}/{$row->id}"), $no, [
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