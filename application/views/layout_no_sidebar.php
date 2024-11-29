<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ($title = isset($title) ? $title : APP_NAME) ?></title>

    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico') ?>" type="image/png">

    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/font-awesome/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/fontawesome-free/css/all-custom.css') ?>">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/dist/css/adminlte.min.css') ?>">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/toastr.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/busy-load.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dark.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/title.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/antrian.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/glow.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url((!isset($isPrivate) || $isPrivate ? 'assets/particles/particles.css' : 'assets/particles/particles-gray.css')) ?>">

    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

    <!-- jQuery -->
    <script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/jquery/jquery.min.js') ?>"></script>
    <script type="text/javascript">
        var $ = jQuery.noConflict();
    </script>

    <script src="<?php echo base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/busy-load.min.js') ?>"></script>

    <!-- moment -->
    <script src="<?php echo base_url('assets/vendor/moment/moment.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/moment/locale/id.js') ?>"></script>
</head>

<body id="my-layout-plain" class="<?php echo get_layout_classes('mode-layout-plain') . (!isset($isPrivate) || $isPrivate ? ' dark-mode' : '') ?>" style="background-color: transparent;">
    <?php $showParticles = !(isset($hideParticles) && $hideParticles) ?>
    <?php if ($showParticles) : ?>
        <div id="particles-js"></div>
    <?php endif ?>

    <?php if ((!isset($isPrivate) || $isPrivate) && (!isset($hasNavigation) || $hasNavigation)) {
        $this->load->view('_navbar', ['menus' => [], 'navbarClass' => 'navbar-plain',  'type' => isset($type) ? $type : '']);
    } ?>

    <div class="card container-main d-flex justify-content-center" style="background: transparent;">
        <?php $showLogo = isset($showLogo) ? $showLogo : false ?>
        <?php if ($showLogo) : ?>
            <img src="<?php echo base_url('assets/images/icon.png') ?>" height="100px" alt="Logo <?php echo SATKER_NAME ?>" class="brand-image mt-3">
        <?php endif ?>
        <?php if (isset($showTitle) && $showTitle) : ?>
            <span class="h4 title-h text-glowing text-center">
                <?php echo strtoupper($title) ?>
            </span>
        <?php endif ?>

        <?php $this->load->view($main_body) ?>
    </div>

    <?php $this->load->view('_footer', ['isPrivate' => !isset($isPrivate) || $isPrivate]) ?>

    <script src="<?php echo base_url('assets/vendor/AdminLTE-3.2.0/plugins/select2/js/select2.full.min.js') ?>"></script>

    <script type='text/javascript'>
        var hideLoader = true;

        $(document).ready(function() {
            var showParticles = '<?php echo $showParticles ? 1 : 0 ?>' == 1;
            if (showParticles) {
                particlesJS.load('particles-js', '<?php echo base_url((!isset($isPrivate) || $isPrivate ? 'assets/particles/particles.json' : 'assets/particles/particles-gray.json')) ?>', function() {});
            }
        });
    </script>
    <script src="<?php echo base_url('assets/particles/particles.min.js') ?>"></script>
</body>

</html>