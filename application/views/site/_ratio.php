<style>
    .nav-item {
        min-width: 250px;
    }
</style>
<script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>

<div class="box_ratio card card-widget widget-user-2 m-4">
    <div class="d-flex justify-content-center">
        <div class="widget-user-header pt-0 pb-2">
            <h5 align="center" style="margin: 6px auto 0px;">
                <div class="knob-label font-weight-bold">
                    <font color="white" size="3" face="Segoe UI">PENANGANAN PERKARA</font>
                </div>
            </h5>

            <h5 align="center" style="margin: 0px auto 0px;">
                <?php if (isset($ratio)): ?>
                    <input type="text" class="knob" data-min="0" data-max="100" value="<?php echo $ratio->kinerja_perkara ?>" data-readonly="true" data-skin="tron" data-thickness="0.2" data-width="150" data-height="150" data-fgColor="<?php echo getColor($ratio->kinerja_perkara) ?>">
                <?php else: ?>
                    <font size="10" face="Bernard MT Condensed" class="badge-number"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></font>
                <?php endif ?>
            </h5>
        </div>
        <div class="widget-user-header pt-0 pb-2">
            <h5 align="center" style="margin: 6px auto 0px;">
                <div class="knob-label font-weight-bold">
                    <font color="white" size="3" face="Segoe UI">PERKARA E-COURT</font>
                </div>
            </h5>
            <h5 align="center" style="margin: 0px auto 0px;">
                <?php if (isset($ratio)): ?>
                    <input type="text" class="knob" data-min="0" data-max="100" value="<?php echo $ratio->kinerja_ecourt ?>" data-readonly="true" data-skin="tron" data-thickness="0.2" data-width="150" data-height="150" data-fgColor="<?php echo getColor($ratio->kinerja_ecourt) ?>">
                <?php else: ?>
                    <font size="10" face="Bernard MT Condensed" class="badge-number"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></font>
                <?php endif ?>
            </h5>
        </div>
        <div class="widget-user-header pt-0 pb-2">
            <h5 align="center" style="margin: 6px auto 0px;">
                <div class="knob-label font-weight-bold">
                    <font color="white" size="3" face="Segoe UI">BERITA ACARA SIDANG</font>
                </div>
            </h5>
            <h5 align="center" style="margin: 0px auto 0px;">
                <?php if (isset($ratio_bas)): ?>
                    <input type="text" class="knob" data-min="0" data-max="100" value="<?php echo $ratio_bas->kinerja_bas ?>" data-readonly="true" data-skin="tron" data-thickness="0.2" data-width="150" data-height="150" data-fgColor="<?php echo getColor($ratio_bas->kinerja_bas) ?>">
                <?php else: ?>
                    <font size="10" face="Bernard MT Condensed" class="badge-number"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></font>
                <?php endif ?>
            </h5>
        </div>
        <div class="widget-user-header pt-0 pb-2">
            <h5 align="center" style="margin: 6px auto 0px;">
                <div class="knob-label font-weight-bold">
                    <font color="white" size="3" face="Segoe UI">UPLOAD PUTUSAN</font>
                </div>
            </h5>
            <h5 align="center" style="margin: 0px auto 0px;">
                <?php if (isset($ratio_bas)): ?>
                    <input type="text" class="knob" data-min="0" data-max="100" value="<?php echo $ratio5->kinerja_dirput ?>" data-readonly="true" data-skin="tron" data-thickness="0.2" data-width="150" data-height="150" data-fgColor="<?php echo getColor($ratio5->kinerja_dirput) ?>">
                <?php else: ?>
                    <font size="10" face="Bernard MT Condensed" class="badge-number"><i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i></font>
                <?php endif ?>
            </h5>
        </div>
    </div>
    <div class="card-footer p-0">
        <div class="d-flex">
            <ul class="nav flex-column m-2">
                <li class="nav-item p-2">
                    <span class="float-left">Tunggakan (Sisa Perkara)</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio) ? number_format_indo($ratio->sisa_perkara) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Sisa Tahun Lalu</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio) ? number_format_indo($ratio->sisa_tahun_lalu) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Masuk Tahun Ini</span> <span class="badge-number float-right badge badge-status badge-default"><?= isset($ratio) ? number_format_indo($ratio->masuk) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">e-Court Tahun Ini</span> <span class="badge-number float-right badge badge-status badge-default"><?= isset($ratio) ? number_format_indo($ratio->masuk_ecourt) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Masuk Bulan Ini</span> <span class="badge-number float-right badge badge-status badge-default"><?= isset($ratio) ? number_format_indo($ratio->masuk_bulan_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">e-Court Bulan Ini</span> <span class="badge-number float-right badge badge-status badge-default"><?= isset($ratio) ? number_format_indo($ratio->ecourt_bulan_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
            </ul>
            <ul class="nav flex-column m-2">
                <li class="nav-item p-2">
                    <span class="float-left">Masuk Hari Ini</span> <span class="badge-number float-right badge badge-status badge-primary"><?= isset($ratio) ? number_format_indo($ratio->masuk_hari_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">e-Court Hari Ini</span> <span class="badge-number float-right badge badge-status badge-primary"><?= isset($ratio) ? number_format_indo($ratio->ecourt_hari_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Putus Hari Ini</span> <span class="badge-number float-right badge badge-status badge-primary"><?= isset($ratio) ? number_format_indo($ratio->putus_hari_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Redaksi Hari Ini</span> <span class="badge-number float-right badge badge-status badge-primary"><?= isset($ratio2) ? number_format_indo($ratio2->redaksi_hari_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Belum Input Putus</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio2) ?  number_format_indo($ratio2->belum_input_putus) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Belum Input Redaksi</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio3) ? number_format_indo($ratio3->putus_belum_redaksi) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
            </ul>
            <ul class="nav flex-column m-2">
                <li class="nav-item p-2">
                    <span class="float-left">File Gugatan Belum Ada</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_summary) ? number_format_indo($ratio_summary['belum_ada_gugatan_summary']['kosong']) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Relaas Belum Ada</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_summary) ? number_format_indo($ratio_summary['relaas_belum_ada']['kosong']) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Sidang Belum BAS</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_summary) ? number_format_indo($ratio_summary['sidang_belum_bas']['kosong']) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Putus Belum BAS</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_summary) ? number_format_indo($ratio_summary['putus_belum_bas']['kosong']) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Belum Minutasi</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_all) ? number_format_indo($ratio_all->all_no_minutasi) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Belum Anonimisasi</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_summary) ? number_format_indo($ratio_summary['belum_anonimasi']['kosong']) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
            </ul>
            <ul class="nav flex-column m-2">
                <li class="nav-item p-2">
                    <span class="float-left">Perkara Putus</span> <span class="badge-number float-right badge badge-status badge-info"><?= isset($ratio_all) ? number_format_indo($ratio_all->all_putus) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Putus Bulan Ini</span> <span class="badge-number float-right badge badge-status badge-default"><?= isset($ratio) ? number_format_indo($ratio->putus_bulan_ini) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Belum Ada e-Doc</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_all) ? number_format_indo($ratio_all->all_no_edoc) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Antrian Dirput</span> <span class="badge-number float-right badge badge-status badge-primary"><?= isset($ratio_antrian) ? number_format_indo($ratio_antrian->dirput_antrian) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Antrian Dirput Error</span> <span class="badge-number float-right badge badge-status badge-warning"><?= isset($ratio_antrian) ? number_format_indo($ratio_antrian->dirput_error) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
                <li class="nav-item p-2">
                    <span class="float-left">Redaksi & Putus Beda</span> <span class="badge-number float-right badge badge-status badge-danger"><?= isset($ratio4) ? number_format_indo($ratio4->selisih_redaksi_putus) : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>' ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.knob').knob({
            format: function(value) {
                return value + '%';
            },
            draw: function() {
                // Dynamically adjust font size
                let fontSize = Math.max(this.$.width() / 5, 24); // Calculate based on knob size
                this.i.css('font-size', fontSize + 'px'); // Apply font size to the inner text

                // "tron" case
                if (this.$.data('skin') == 'tron') {
                    var a = this.angle(this.cv) // Angle
                        ,
                        sa = this.startAngle // Previous start angle
                        ,
                        sat = this.startAngle // Start angle
                        ,
                        ea // Previous end angle
                        ,
                        eat = sat + a // End angle
                        ,
                        r = true

                    this.g.lineWidth = this.lineWidth

                    this.o.cursor &&
                        (sat = eat - 0.3) &&
                        (eat = eat + 0.3)

                    if (this.o.displayPrevious) {
                        ea = this.startAngle + this.angle(this.value)
                        this.o.cursor &&
                            (sa = ea - 0.3) &&
                            (ea = ea + 0.3)
                        this.g.beginPath()
                        this.g.strokeStyle = this.previousColor
                        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
                        this.g.stroke()
                    }

                    this.g.beginPath()
                    this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
                    this.g.stroke()

                    this.g.lineWidth = 2
                    this.g.beginPath()
                    this.g.strokeStyle = this.o.fgColor
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
                    this.g.stroke()

                    this.$.closest('.col-knob').slideDown();

                    return false
                }
            },
        })
    })
</script>