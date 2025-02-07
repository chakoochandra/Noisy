<style>
    .box_ratio .badge {
        font-size: 100%;
    }

    .thumbnail-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .web-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        /* Ensure this is set to the desired width or let it inherit */
    }

    .fixed-bottom-right {
        position: fixed;
        bottom: 30px;
        right: 10px;
        /* Adjust this value to move the element further from the right */
    }

    .figure-img {
        border-top-left-radius: 1.5rem !important;
    }

    /* Custom default button */
    .btn-secondary,
    .btn-secondary:hover,
    .btn-secondary:focus {
        color: #333;
        text-shadow: none;
        /* Prevent inheritance from `body` */
    }

    .nav-masthead .nav-link:hover,
    .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(0, 0, 0, .25);
    }

    .text-bg-dark .nav-masthead .nav-link:hover,
    .text-bg-dark .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(255, 255, 255, .25);
    }

    .transform-scale:hover {
        -moz-transform: scale(1.05);
        -webkit-transform: scale(1.05);
        transform: scale(1.05);
        transition-duration: 0.5s;
    }
</style>

<section>
    <div class="card-header text-center py-0">
        <div>
            <span class="display-4 font-weight-bold"><?php echo APP_SHORT_NAME ?></span>
            <p><strong><?php echo APP_NAME ?></strong></p>
            <p class="p-2">
                <?php foreach ($apps as $category => $socmed) : ?>
                    <?php if ($category == 'Socmed') : ?>
                        <?php foreach ($socmed as $s) : ?>
                            <?php if ($s[2]) : ?>
                                <a href="<?php echo $s[1] ?>" rel="noopener noreferrer"><img src="<?php echo $s[2] ?>" class="transform-scale" alt="<?php echo $s[0] ?>" width="<?php echo $s[3] ?: 30 ?>" height="<?php echo $s[4] ?: 30 ?>"></a>
                            <?php else : ?>
                                <a href="<?php echo $s[1] ?>" rel="noopener noreferrer"><?php echo $s[0] ?></a>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endforeach ?>
            </p>
        </div>

        <div class="input-group col-lg-6 col-md-8 mx-auto p-2">
            <input type="text" id="textfield-search" class="form-control bg-dark text-muted" placeholder="Cari aplikasi" aria-label="Cari aplikasi" aria-describedby="button-addon2">
            <span id="basic-addon2" class="btn mx-0 input-group-text" style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">&#x1F50D;</span>
            <button class="btn btn-xs btn-outline-info btn-clear inner-btn text-red collapse" onClick="$('#textfield-search').val('').trigger('input');" style="z-index: 10;">x</button>
        </div>

        <?php if (is_local_ip()): ?>
            <div id="box-ratio">
                <div class="container-ratio d-flex flex-row justify-content-center">
                    <?php $this->load->view('site/_ratio') ?>
                </div>
            </div>
            <script>
                const is_development = '<?php echo is_development() ? 1 : 0 ?>' == 1;
                $(document).ready(function() {
                    function loadRatio() {
                        $('.badge-number').html('<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>');
                        loadPartial('<?php echo base_url('site/get_ratio') ?>', '.container-ratio');
                    }

                    loadRatio();

                    if (!is_development) {
                        setInterval(loadRatio, 3 * 60 * 1000);
                    }
                });
            </script>

        <?php endif ?>
    </div>

    <div class="container card-body album pt-0">
        <div id="box-result" class="collapse p-2"></div>

        <div id="box-recommendation" class="collapse p-2"></div>

        <div id="box-apps" class="collapse p-2"></div>
    </div>
</section>

<a href="#" class="fixed-bottom-right mb-4"><img src="<?php echo base_url('assets/images/arrow_up.svg') ?>" width="30" height="30" alt="Kembali ke atas" /></a>

<script src="<?php echo base_url('assets/js/js.cookie.min.js') ?>"></script>
<script>
    const numOfRecommendation = 4;
    var is_local_ip = '<?php echo is_local_ip() ?>' == true;
    var allApps = [];

    function setDarkTheme(isDark) {
        Cookies.set('cookie-theme-dark1', isDark, {
            expires: 365 * 10
        })

        if (isDark == 1) {
            $('body').addClass('text-bg-dark');
            $('body').removeClass('text-bg-light');

            $('footer').addClass('bg-dark');
            $('footer').removeClass('bg-light');

            $('.navbar').addClass('navbar-dark bg-dark');
            $('.navbar').removeClass('navbar-light bg-light');

            $('#textfield-search').addClass('bg-dark');
            $('#textfield-search').next('span').addClass('bg-dark');
        } else {
            $('body').removeClass('text-bg-dark');
            $('body').addClass('text-bg-light');

            $('footer').removeClass('bg-dark');
            $('footer').addClass('bg-light');

            $('.navbar').removeClass('navbar-dark bg-dark');
            $('.navbar').addClass('navbar-light bg-light');

            $('#textfield-search').removeClass('bg-dark');
            $('#textfield-search').next('span').removeClass('bg-dark');
        }

        $('html').show();
    }

    function sortFunction(a, b) {
        if (a[4] === b[4]) {
            return 0;
        } else {
            return (a[4] > b[4]) ? -1 : 1;
        }
    }

    function getPlaceholderImg(placeholder) {
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" preserveAspectRatio="none">
            <defs>
                <style type="text/css">
                    #holder_190d4a343a8 text { fill:rgba(255,255,255,.75);font-weight:normal;font-family:Helvetica, monospace;font-size:20pt }
                </style>
            </defs>
            <g id="holder_190d4a343a8">
                <rect width="100%" height="100%" fill="#777"></rect>
                <g>
                    <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle">` + placeholder + `</text>
                </g>
            </g>
        </svg>`);
    }

    function showApps(target, data, category, count = null, isReset = false) {
        if (isReset) {
            $(target).html('');
        }

        var elId = category.replace(' ', '').toLowerCase();

        $(target).append('<div id="' + category.replace(' ', '').toLowerCase() + '" class="leaves border border-secondary m-3 p-3"></div>');
        $(target).find('#' + elId).append('<span class="category h4">' + category + '</span>');
        $(target).find('#' + elId).append('<div class="my-apps row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mt-2"></div>');

        if (data.length > 0) {
            $.each(count ? data.slice(0, count) : data, function(index, value) {
                if (!['Rekomendasi', 'Hasil Pencarian'].includes(category)) {
                    allApps.push(value);
                }

                $(target).find('#' + elId).find('.my-apps').append(
                    '<div class="col">' +
                    ' <div class="thumbnail-box transform-scale card shadow-sm">' +
                    '   <a href="' + value[1] + '">' +
                    '       <img class="figure-img img-fluid rounded mb-0" alt="' + value[0] + '" src="' + (value[2] ? value[2] : getPlaceholderImg(value[0])) + '" data-holder-rendered="true" width="100" height="50">' +
                    '   </a>' +
                    '   <div class="card-body">' +
                    '     <div class="d-flex justify-content-between align-items-center">' +
                    '       <h6 class="web-name card-text mb-0">' + value[0] + '</h6>' +
                    (value[3] ? '       <small class="text-muted"><span class="web-type badge bg-' + classes[value[3]] + '">' + value[3] + '</span></small>' : '') +
                    '     </div>' +
                    '   </div>' +
                    ' </div>' +
                    '</div>'
                );
            });
        } else {
            $(target).find('#' + elId).find('.my-apps').append('<span class="text-center">Web tidak ditemukan</span>');
        }

        $(target).show();
    }

    function getUniqueApps(arr) {
        var uniques = [];
        var itemsFound = {};
        for (var i = 0, l = arr.length; i < l; i++) {
            var temp = [arr[i][0], arr[i][1], arr[i][2]];

            var stringified = JSON.stringify(temp);
            if (itemsFound[stringified]) {
                continue;
            }
            uniques.push(arr[i]);
            itemsFound[stringified] = true;
        }

        return uniques;
    }

    if (!(mycookie = localStorage.getItem('user-favorites'))) {
        var my_recommendation = [];
        localStorage.setItem('user-favorites', JSON.stringify(my_recommendation));
    } else {
        var my_recommendation = JSON.parse(mycookie);
    }

    //populate cookie
    var classes = <?php echo json_encode($classes) ?>;
    if (my_recommendation.length > 0)
        showApps('#box-recommendation', my_recommendation, 'Rekomendasi', numOfRecommendation);

    //populate all apps
    $.each(<?php echo json_encode($apps) ?>, function(category, apps) {
        if (category == 'Lokal') {
            if (is_local_ip) {
                showApps('#box-apps', apps, category);
            }
        } else if (category != 'Socmed') {
            showApps('#box-apps', apps, category);
        }
    });

    // logic pada aksi klik thumbnail web
    $('body').on('click', '.thumbnail-box', function(e) {
        // e.preventDefault();
        var isExistBefore = false;
        var clickedIndex = null;

        var clickedWebName = $(this).find('.web-name').text();
        var clickedWebUrl = $(this).children('a').attr('href');
        var clickedWebImg = $(this).find('a>img').attr('src');
        var clickedWebType = $(this).find('.web-type').text();

        my_recommendation.sort(sortFunction);

        my_recommendation.forEach((item, index) => {
            if (item[0] == clickedWebName) {
                isExistBefore = true;
                clickedIndex = index;
                my_recommendation[index][4] += 1;
            }
        });

        //taruh web yang di klik pada first index
        if (!isExistBefore) {
            my_recommendation.unshift([
                clickedWebName,
                clickedWebUrl,
                clickedWebImg,
                clickedWebType,
                1,
            ]);
        } else {
            my_recommendation.unshift(my_recommendation.splice(clickedIndex, 1)[0]);
        }

        localStorage.setItem('user-favorites', JSON.stringify(my_recommendation));

        showApps('#box-recommendation', my_recommendation, 'Rekomendasi', numOfRecommendation, true);
    });

    $('#textfield-search').focus();

    //initial theme from cookie
    var initTheme = Cookies.get('cookie-theme-dark1') ? Cookies.get('cookie-theme-dark1') : 1;
    $('#switch-theme').prop('checked', initTheme == 1);
    setDarkTheme(initTheme);

    //switch theme
    $('#switch-theme').on('change.bootstrapSwitch', function(e) {
        setDarkTheme(e.target.checked ? 1 : 0);
    });

    //logic search
    function escapeHTML(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    $('#textfield-search').on('input', function(e) {
        if ($(this).val()) {
            var filterApps = [];
            var keyword = escapeHTML($(this).val());
            $.each(getUniqueApps(allApps), function(index, item) {
                var appName = item[0];
                var appUrl = item[1];
                if (appName.toLowerCase().includes(keyword.toLowerCase()) || appUrl.toLowerCase().includes(keyword.toLowerCase())) {
                    filterApps.push(item);
                }
            });

            showApps('#box-result', filterApps, 'Hasil Pencarian', null, true);

            $('.btn-clear').show();
            $('#box-result').slideDown();
            $('#box-ratio').slideUp();
            // $('#box-recommendation').slideUp();
            // $('#box-apps').slideUp();
        } else {
            $('.btn-clear').hide();
            $('#box-result').slideUp();
            $('#box-ratio').slideDown();
            // $('#box-recommendation').slideDown();
            // $('#box-apps').slideDown();
        }
    });
</script>