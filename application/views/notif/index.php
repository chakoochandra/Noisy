<style>
    #progressResponses {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid royalblue;
        padding: 10px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/nprogress/nprogress.css') ?>">
<script src="<?php echo base_url('assets/nprogress/nprogress.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/moment/moment.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/moment/locale/id.js') ?>"></script>

<div class="card-body">
    <div id="summary-info"></div>
    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
        <?php foreach ($this->notifs as $type => $title) : ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $selectedType == $type ? 'active' : null ?>" id="<?php echo $type ?>-tab" data-toggle="pill" href="#<?php echo $type ?>" role="tab" aria-controls="<?php echo $type ?>" aria-selected="true" data-type="<?php echo $type ?>" data-href="<?php echo base_url("whatsapp/notif/$type") ?>"><?php echo $allTypes[$title] ?></a>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="tab-content pt-2" id="custom-content-below-tabContent">
        <?php foreach ($this->notifs as $type => $title) : ?>
            <div class="tab-pane tab-pane-<?php echo $type ?> fade <?php echo $selectedType == $type ? 'active show' : null ?>" id="<?php echo $type ?>" role="tabpanel" aria-labelledby="<?php echo $type ?>-tab">
                <?php if ($selectedType == $type) : ?>
                    <?php $view = in_array($selectedType, ['antrian', 'sidang']) ? 'antrian_sidang' : $selectedType ?>
                    <?php $this->load->view("notif/$view", $data) ?>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        var selectedType = '<?php echo $selectedType ?>';

        $('.nav-link').on('click', function(e) {
            selectedType = $(this).attr('data-type');
            loadPartial($(this).attr('data-href'), '.tab-pane-' + selectedType)
        });
    });

    function sendNotification(el, url) {
        if (confirm($(el).data("confirm-message"))) {
            $.busyLoadFull("show");

            const data = {
                title: 'Sedang mengirim',
                content: `<div class="progress">
                <div id="dynamicProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
            </div>
            <div id="progressResponses" class="d-none flex-column mt-2"></div>`,
            };

            setModalContent(data);

            NProgress.start();

            fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + '<?php echo DIALOGWA_TOKEN ?>'
                },
            }).then(response => {
                const reader = response.body.getReader();
                const decoder = new TextDecoder();

                function read() {
                    reader.read().then(({
                        done,
                        value
                    }) => {
                        if (done) {
                            NProgress.done();
                            return;
                        }

                        const chunk = decoder.decode(value, {
                            stream: true
                        });
                        const responses = chunk.split('\n');

                        responses.forEach(response => {
                            if (response.trim()) {
                                try {
                                    const data = JSON.parse(response);
                                    const progress = data.progress;

                                    NProgress.set(progress / 100);

                                    const progressBar = $('#dynamicProgressBar');
                                    progressBar.attr('aria-valuenow', progress);
                                    progressBar.css('width', progress + '%');
                                    progressBar.text(progress + '%');

                                    if (data.no) {
                                        if ($('#progressResponses').hasClass('d-none')) {
                                            $('#progressResponses').removeClass('d-none');
                                            $('#progressResponses').addClass('d-flex');
                                        }

                                        $('#progressResponses').append(`<span>${data.no}. ${data.response}</span>`)
                                    } else if (data.message) {
                                        if ($('#progressResponses').hasClass('d-none')) {
                                            $('#progressResponses').removeClass('d-none');
                                            $('#progressResponses').addClass('d-flex');
                                        }

                                        $('#progressResponses').append(`<div class="callout callout-${data.status==1?'success':'danger'} mb-0" role="alert">${data.message}</div>`)
                                    }

                                    if (progress >= 100) {
                                        progressBar.removeClass("progress-bar-animated");
                                        $('.modal-title').text('Selesai');
                                        $.busyLoadFull("hide");
                                    }
                                } catch (e) {
                                    $.busyLoadFull("hide");
                                    console.error('Error parsing JSON', e);
                                }
                            }
                        });

                        read();
                    });
                }

                read();
            }).catch(error => {
                $.busyLoadFull("hide");

                NProgress.done();
                console.error('Fetch error:', error);
            });
        }
    }

    function checkGateway(type) {
        $.busyLoadFull("show");

        fetch('<?php echo base_url('whatsapp/check_gateway') ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                $.busyLoadFull("hide");

                if (data.status) {
                    const result = JSON.parse(data.response);
                    if (result.name) {
                        $('#summary-info').html(`<div class="alert alert-primary py-4" role="alert">
                            <h5>Pesan Terkirim</h5> 
                            <div class="align-items-center d-flex">
                                <span class="mr-2">Hari ini &nbsp; <span class="badge badge-success">${result.summary.today}</span></span> | &nbsp;
                                <span class="mr-2">Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.yesterday}</span></span> | &nbsp;

                                <span class="mr-2">Minggu ini &nbsp; <span class="badge badge-success">${result.summary.cur_week}</span></span> | &nbsp;
                                <span class="mr-2">Minggu Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.prev_week}</span></span> | &nbsp;

                                <span class="mr-2">Bulan ini &nbsp; <span class="badge badge-success">${result.summary.cur_month}</span></span> | &nbsp;
                                <span class="mr-2">Bulan Kemarin &nbsp; <span class="badge badge-secondary">${result.summary.prev_month}</span></span> | &nbsp;

                                <span class="mr-2">Semua &nbsp; <span class="badge badge-success">${result.summary.all}</span></span>
                            </div>
                        </div>`);
                        $('#container-info-' + type).append(`<div class="callout callout-${result.is_expired || result.is_out_of_limit || !result.status?'danger':'success'} d-flex align-items-center mb-0 ml-4 py-1" role="alert">Sesi: ${result.name} (${result.number}) | ${result.status?'Aktif':'Tidak Aktif'} | Limit Pesan: ${new Intl.NumberFormat('id-ID').format(result.limit_message)} | Expired: ${moment(result.expires).format('dddd, Do MMMM YYYY h:mm')}</div>`);
                    } else {
                        $('#summary-info').html('');
                        $('#container-info-' + type).append(`<div class="callout callout-danger d-flex align-items-center mb-0 ml-4 py-1" role="alert">${result.message}</div>`);
                    }
                } else {
                    $('#summary-info').html('');
                    $('#container-info-' + type).append(`<div class="callout callout-danger d-flex align-items-center mb-0 ml-4 py-1" role="alert">${data.message}</div>`);
                }
            })
            .catch(error => {
                $.busyLoadFull("hide");
                console.error('Fetch error:', error);
            });
    }
</script>