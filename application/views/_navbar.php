<style>
    body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header-plain {
        margin-left: 0;
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: #343a40;
    }
</style>

<nav id="my-navbar" class="<?php echo get_layout_classes((!isset($navbarClass) ? 'navbar' : $navbarClass)) ?> <?php echo $this->session->userdata('disableApiRequest') ? 'bg-red' : '' ?>">
    <ul class="navbar-nav">
        <?php if ($menus): ?>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
            </li>
        <?php endif ?>

        <?php if (isset($navbarClass) && $navbarClass == 'navbar-plain'): ?>
            <a href="<?php echo base_url("/") ?>" class="d-flex brand-link p-0">
                <img src="<?php echo base_url('assets/images/icon.png') ?>" alt="Logo <?php echo APP_SHORT_NAME ?>" class="brand-image" style="opacity: .8;">
                <span class="brand-text font-weight-bold"><?php echo APP_SHORT_NAME ?></span>
            </a>
        <?php endif ?>

        <?php /*if (($antrianIndex = arraySearchKeyIndex('title', 'Antrian', $menus))) {
            $configs = get_queue_config();
            foreach ($configs as $c => $conf) {
                if (isset($conf['show_in_display']) && $conf['show_in_display']  && $conf['enable']) {
                    $menus[$antrianIndex]['child'][] = [
                        'title' => str_replace('Antrian ', '', $conf['title_list']),
                        'icon' => 'circle-o text-' . ($conf['class']),
                        'href' => base_url("antrian/$c"),
                    ];
                }
            }
        } ?>

        <?php if ($antrianIndex) : ?>
            <?php foreach ($configs as $c => $conf) : ?>
                <?php if (isset($conf['show_in_display']) && $conf['show_in_display']  && $conf['enable']) : ?>
                    <li class="nav-item nav-item-antrian d-none d-sm-inline-block">
                        <a href="<?php echo base_url("antrian/$c") ?>" class="nav-link ellipsis <?php echo $type == $c ? 'active' : '' ?>" style="max-width: 135px;"><?php echo str_replace('Antrian ', '', $conf['title_list']) ?></a>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif*/ ?>
    </ul>
    <ul class="navbar-nav ml-auto justify-content-center align-items-center">
        <li class="nav-item text-right">
            <span class="chip realtime-clock text-xs px-3"><?php echo getLocaleTime(strftime("%A, %d %B %Y pukul %H:%M:%S", time())) ?></span>
        </li>
    </ul>
</nav>