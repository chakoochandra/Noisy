function stripHtmlTags(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html.replace(/<br\s*\/?>/gi, ' - '); // Replace <br> tags with spaces
    return tmp.textContent || tmp.innerText || "";
}

function showButtons(tableId) {
    $(tableId + '_wrapper .dt-search').css('display', 'block');
    $(tableId + '_wrapper .dt-buttons').css('display', 'block');
    $('.dt-layout-row').slideDown({
        start: function () {
            $(this).css({
                display: "flex"
            })
        }
    });
}

// function hideButtons(tableId) {
//     $(tableId + '_wrapper .dt-search').css('display', 'none');
//     $(tableId + '_wrapper .dt-buttons').css('display', 'none');
// }

function initDataTable(tableId, customConfigs = {}) {
    var table;
    var selectedIds = [];
    var title = typeof customConfigs.title !== 'undefined' ? customConfigs.title : '';
    var configs = {
        fixedHeader: {
            header: true,
            headerOffset: $('.navbar').outerHeight() // Adjust for navbar height
        },
        // autoWidth: false,
        responsive: true,
        paging: customConfigs.paging || true,
        pageLength: 20,
        colReorder: true,
        lengthMenu: [
            [10, 25, 50, -1],
            ['10 baris', '25 baris', '50 baris', 'Tampilkan Semua']
        ],
        language: {
            "sEmptyTable": "Tidak ada data",
            "sProcessing": "",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Mengambil data...",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            },
            // url: languageUrl,
            paginate: {
                "first": "<i class='fas fa-angle-double-left'></i>",
                "last": "<i class='fas fa-angle-double-right'></i>",
                "next": "<i class='fas fa-angle-right'></i>",
                "previous": "<i class='fas fa-angle-left'></i>"
            },
            buttons: {
                copyTitle: 'Ditambahkan ke clipboard',
                copyKeys: 'Tekan <i>ctrl</i> atau <i>\u2318</i> + <i>C</i> untuk menyalin data tabel ke clipboard Anda. <br><br>Untuk membatalkan, klik pesan ini atau tekan Esc.',
                copySuccess: {
                    _: '%d baris disalin',
                    1: '1 baris disalin'
                }
            }
        },
        initComplete: function (settings, json) {
            if (title) {
                $(`${tableId}_wrapper`).before(`<h3 id="${tableId.replace('#', '')}-title" class="h4 text-center mb-0">${title}</h3>`);
            }

            showButtons(tableId);

            var parent = $(`.my-button`).closest('.dt-button');
            if (parent) {
                $(`.my-button`).closest('.dt-buttons').prepend($(`.my-button`));
                parent.remove();
            }
        },
        stateSaveParams: function (settings, data) {
            // Save the current table title in the state
            data.tableTitle = $(`${tableId}-title`).text();
        },
        stateLoadParams: function (settings, data) {
            // Restore the table title from the saved state
            if (data.tableTitle) {
                $(`${tableId}-title`).text(data.tableTitle);
            }
        },
        drawCallback: function () {
            // After each draw, reselect the rows based on the stored selectedIds
            if (table) {
                table.rows().every(function () {
                    if (selectedIds.indexOf(this.data().row_id) !== -1) {
                        this.select();
                    }
                });
            }
        }
    };

    if (customConfigs.columns) {
        customConfigs.columns.forEach(c => {
            if (!c.name && c.data) {
                c.name = c.data;
            }
        });
    }

    /** Ajax Cell Datepicker */
    if (typeof customConfigs.ajaxCellDatepicker !== 'undefined') {
        customConfigs.createdRow = function (row, data, dataIndex) {
            $('td', row).addClass('table-cell-ellipsis');

            // Assuming customConfigs.ajaxCellDatepicker is an array of configurations
            if (Array.isArray(customConfigs.ajaxCellDatepicker)) {
                customConfigs.ajaxCellDatepicker.forEach(function (config) {
                    $('td', row).eq(config.column).on('click', function () {
                        var standalone = config.dependant == null;
                        if (config.editable == 1 && (standalone || data[config.dependant] != null)) {
                            var cell = table.cell(this);
                            var $input = $('<input type="text" />').datepicker({
                                autoclose: true,
                                clearBtn: true,
                                todayHighlight: true,
                                todayBtn: 'linked',
                            });
                            $input.on('changeDate', function (e, d) {
                                if (cell.data() != e.format('yyyy-mm-dd')) {
                                    table.row(cell.node()).select();
                                    table.rows().every(function () {
                                        if (selectedIds.includes(this.data().row_id)) {
                                            table.cell(this.index(), config.column).data(e.format('yyyy-mm-dd'));
                                        }
                                    });

                                    var data_csrf = {};
                                    data_csrf[csrf_token_name] = csrf_hash;

                                    $.ajax({
                                        url: config.url,
                                        type: 'POST',
                                        data: $.extend({
                                            date: e.format('yyyy-mm-dd'),
                                            ids: selectedIds,
                                            // perkara_id: data.perkara_id,
                                        }, data_csrf),
                                        success: function (response) {
                                            selectedIds = [];

                                            if (response.csrf_hash) {
                                                csrf_hash = response.csrf_hash;
                                            }

                                            if (config.callback) {
                                                loadPartial(config.callback, '.card-body');
                                            }
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('AJAX error:', error);
                                        }
                                    });
                                }
                            });
                            $input.on('hide', function (e, d) {
                                cell.data(e.format('yyyy-mm-dd'));
                            });
                            $input.val(cell.data()).appendTo($(this).empty()).focus();
                        }
                    });
                });
            } else {
                if (customConfigs.ajaxCellDatepicker.editable == 1) {
                    $('td', row).eq(customConfigs.ajaxCellDatepicker.column).on('click', function () {
                        var cell = table.cell(this);       // Get the clicked cell
                        var $input = $('<input type="text" />').datepicker({
                            autoclose: true,
                            clearBtn: true,
                            todayHighlight: true,
                            todayBtn: 'linked',
                        });
                        $input.on('changeDate', function (e, d) {
                            if (cell.data() != e.format('yyyy-mm-dd')) {
                                table.row(cell.node()).select();
                                table.rows().every(function () {
                                    if (selectedIds.includes(this.data().row_id)) {
                                        table.cell(this.index(), customConfigs.ajaxCellDatepicker.column).data(e.format('yyyy-mm-dd'));
                                    }
                                });

                                var data_csrf = {};
                                data_csrf[csrf_token_name] = csrf_hash;

                                $.ajax({
                                    url: customConfigs.ajaxCellDatepicker.url,
                                    type: 'POST',
                                    data: $.extend({
                                        date: e.format('yyyy-mm-dd'),
                                        ids: selectedIds,
                                        // perkara_id: data.perkara_id,
                                    }, data_csrf),
                                    success: function (response) {
                                        selectedIds = [];

                                        if (response.csrf_hash) {
                                            csrf_hash = response.csrf_hash;
                                        }

                                        if (customConfigs.ajaxCellDatepicker?.callback) {
                                            loadPartial(customConfigs.ajaxCellDatepicker.callback, '.card-body');
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('AJAX error:', error);
                                    }
                                });
                            }
                        });
                        $input.on('hide', function (e, d) {
                            cell.data(e.format('yyyy-mm-dd'))/*.draw()*/;
                        });
                        $input.val(cell.data()).appendTo($(this).empty()).focus();
                    });
                }
            }
        }
    } else {
        customConfigs.createdRow = function (row, data, dataIndex) {
            $('td', row).addClass('table-cell-ellipsis');
        }
    }

    /** State */
    if (typeof customConfigs.stateSave === 'undefined') {
        customConfigs.stateSave = false;
    }

    /** Scroller */
    if (customConfigs.scroller) {
        // customConfigs.scrollCollapse = typeof customConfigs.scrollCollapse === 'undefined' || customConfigs.scrollCollapse;
        customConfigs.scrollY = typeof customConfigs.scrollY === 'undefined' ? 700 : customConfigs.scrollY;
        customConfigs.paging = true;
    }

    /** Buttons */
    var buttons = [];
    if (customConfigs.scroller !== true && (typeof customConfigs.btnHidePagelength === 'undefined' || customConfigs.btnHidePagelength === false)) {
        buttons.push({
            extend: 'pageLength',
            text: function (dt, button, config) {
                return dt.i18n("buttons.pageLength", {
                    "-1": "Tampilkan semua baris",
                    _: "Tampilkan %d baris"
                }, dt.page.len());
            },
        });
    }

    if (typeof customConfigs.btnHideColvis === 'undefined' || customConfigs.btnHideColvis === false) {
        buttons.push({
            extend: 'colvis',
            text: function (dt, button, config) {
                return dt.i18n('buttons.colvis', 'Kolom');
            },
            // collectionLayout: 'fixed columns',
            popoverTitle: 'Pilih Kolom',
            postfixButtons: [{
                extend: 'colvisRestore',
                text: 'Reset Kolom'
            }],
            columns: ':not(.noVis)' // Example to exclude specific columns
        });
    }

    if (typeof customConfigs.btnHideCollection === 'undefined' || customConfigs.btnHideCollection === false || customConfigs.scroller) {
        /** Export all rows */
        if (typeof customConfigs.exportAllRows === 'undefined') {
            customConfigs.exportAllRows = true;
        }

        buttons.push({
            extend: 'collection',
            text: 'Export',
            buttons: getExportButtonsConfig(customConfigs.exportAllRows),
        });
    }

    let showSearchField = customConfigs.showSearchField !== undefined ? customConfigs.showSearchField : true;
    let topStart = {};
    if (showSearchField) {
        topStart['search'] = {
            text: '',
            placeholder: 'Cari',
        }
        topStart['buttons'] = [
            customConfigs?.ajax?.url ? 'reload' : 'reloadNonAjax',
        ];
    }

    configs.layout = {
        bottomStart: '',
        topEnd: 'info',
        top2End: {
            buttons: buttons
        },
        topStart: topStart,
    };

    // ['top2End', 'topStart'].forEach(position => {
    //     const customButtons = customConfigs.layout?.[position]?.buttons;
    //     if ($.isArray(customButtons)) {
    //         configs.layout[position].buttons = $.isArray(configs.layout[position]?.buttons)
    //             ? $.merge(customButtons, configs.layout[position].buttons)
    //             : customButtons;
    //     }
    // });

    configs.layout = {
        ...configs.layout, // Spread existing layout
        ...customConfigs.layout || [], // Spread custom layout
        top2End: {
            ...configs.layout.top2End, // Retain existing top2End structure
            buttons: [
                ...(configs.layout.top2End?.buttons || []), // Preserve existing buttons
                ...(customConfigs.layout?.top2End?.buttons || []) // Add custom buttons
            ]
        },
        topStart: {
            ...configs.layout.topStart, // Retain existing topStart structure
            buttons: [
                ...(configs.layout.topStart?.buttons || []), // Preserve existing buttons
                ...(customConfigs.layout?.topStart?.buttons || []) // Add custom buttons
            ]
        }
    };

    if (customConfigs.layout) {
        delete customConfigs.layout;
    }

    /** Ajax */
    if (customConfigs?.ajax?.url) {
        customConfigs.processing = true;
        customConfigs.serverSide = true;
        customConfigs.ajax.type = "POST";
        customConfigs.ajax.dataSrc = function (json) {
            csrf_hash = json.csrf_hash; // Update CSRF Token hash
            return json.data;
        };
        customConfigs.ajax.data = customConfigs.ajax.data || function (d) {
            d[csrf_token_name] = csrf_hash; // Add CSRF Token
        };
    }

    $.fn.dataTable.ext.type.order['file-size-pre'] = function (data) {
        var normalizedData = data.replace(/&nbsp;/g, ' ');
        var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        var regex = /^(\d+(?:\.\d+)?)\s*([KMGTPEZY]?B)$/i;
        var match = normalizedData.match(regex);
        if (match) {
            var value = parseFloat(match[1]);
            var unit = match[2].toUpperCase();
            var exponent = units.indexOf(unit);
            return value * Math.pow(1024, exponent);
        }
        return 0; // Return 0 for any non-matching data, as a fallback
    };

    $.fn.dataTable.ext.buttons.reload = {
        text: '<i class="fas fa-sync"></i>',
        action: function (e, dt, node, config) {
            // Reset the table state (search, paging, sorting)
            $(`${tableId}-title`).text(title);
            dt.state.clear();  // Clear the saved state
            dt.search('');     // Clear the global search
            dt.columns().search('');  // Clear individual column search
            dt.order([]);  // Reset sorting
            dt.colReorder.reset();//Reset column ordering
            // table.colReorder.reset();//Reset column ordering

            // Reset all input elements within the DataTable
            $(`${tableId} input`).val(''); // Clear all text inputs
            $(`${tableId} select`).prop('selectedIndex', 0); // Reset all select dropdowns to the first option
            $(`${tableId} input[type="checkbox"], ${tableId} input[type="radio"]`).prop('checked', false); // Uncheck all checkboxes and radio buttons

            // Reset the visibility of columns to their default state
            if (dt.button('.buttons-colvis').length) {
                dt.columns().visible(true); // Show all columns
                // dt.buttons('.buttons-colvis').trigger(); // Reset the ColVis settings
            }

            $('.my-button select').val(null).trigger('change'); // Unselect all selected dropdowns .my-button
            $('.my-button input').datepicker('clearDates'); // Reset all Bootstrap datepickers inside .my-button

            dt.ajax.reload(null, false); // Use false to stay on the same page, true to reset pagination

            dt.draw(false);  // Use false to stay on the same page, true to reset pagination
        }
    };

    // $.fn.dataTable.ext.buttons.reloadNonAjax = {
    //     text: '<i class="fas fa-sync"></i>',
    //     action: function (e, dt, node, config) {
    //         location.reload();
    //     }
    // };

    // $.fn.dataTable.ext.buttons.openModal = {
    //     text: '<i class="fas fa-file-o"></i>',
    //     action: function (e, dt, node, config) {
    //         openModal(config.url, {
    //             row_ids: selectedIds
    //         });
    //     }
    // };

    $.fn.dataTable.ext.buttons.customButton = {
        text: function (dt, button, config) {
            return config.text || '';
        },
        action: function (e, dt, button, config) {
            if (config.preventDefault) {
                e.preventDefault();
            }
            if (config.onClick) {
                config.onClick(e, dt, button, config); // Call a custom click handler if provided
            }
        },
        init: function (dt, button, config) {
            $(button).addClass(config.className || ''); // Add custom classes
            $(button).attr('href', config.url || '#'); // Set the href attribute
        }
    };

    $.fn.dataTable.ext.buttons.resetOrdering = {
        text: function (dt, button, config) {
            return config.text || 'Reset Urutan';
        },
        action: function (e, dt, node, config) {
            dt.order([]).draw();
        }
    };

    $.fn.dataTable.ext.buttons.orderByColumn = {
        text: function (dt, button, config) {
            return config.text || 'Urutkan'; // Set text dynamically based on config
        },
        action: function (e, dt, node, config) {
            var orderArray = [];
            if (Array.isArray(config.columns)) {
                config.columns.forEach(function (column) {
                    var columnIndex = dt.column(column.name + ':name').index();
                    orderArray.push([columnIndex, column.order]);
                });
            } else {
                var columnIndex = dt.column(config.columns.name + ':name').index();
                orderArray.push([columnIndex, config.columns.order]);
            }
            dt.order(orderArray).draw();
        }
    };

    $.fn.dataTable.ext.buttons.customColvisGroup = $.extend(true, {}, $.fn.dataTable.ext.buttons.colvisGroup, {
        action: function (e, dt, node, config) {
            // Call the original colvisGroup action
            $.fn.dataTable.ext.buttons.colvisGroup.action.call(this, e, dt, node, config);

            if (config.groupElementId) {
                $(`.${config.groupElementId}`).hide();
            }
            if (config.toggleElementId) {
                $(`.${config.toggleElementId}`).show();
            }
        }
    });

    $.fn.dataTable.ext.buttons.datepicker = {
        // className: 'datepicker-btn',
        init: function (dt, node, config) {
            if (config?.config) {
                let configs = config?.config;
                let format = configs.format || (configs.minViewMode == 'months' ? 'yyyy-mm' : 'yyyy-mm-dd');
                let startDate = configs.startDate || '-75y';
                let endDate = configs.endDate || '+1y';
                let viewMode = configs.viewMode || 'days';
                let minViewMode = configs.minViewMode || 'days';
                let isSemester = configs.isSemester || false;
                let placeholder = configs.placeholder || 'Pilih';
                let value = configs.value || '';

                switch (format) {
                    case 'mm':
                        var options = {
                            format: "mm",
                            viewMode: "months",
                            minViewMode: "months",
                            maxViewMode: "months",
                            autoclose: true,
                            clearBtn: true,
                            todayBtn: 'linked',
                            todayHighlight: true,
                            startDate: startDate,
                            endDate: endDate,
                        };
                        break;
                    case 'yyyy':
                        var options = {
                            format: "yyyy",
                            viewMode: "years",
                            minViewMode: "years",
                            autoclose: true,
                            clearBtn: true,
                            todayBtn: 'linked',
                            todayHighlight: true,
                            startDate: startDate,
                            endDate: endDate,
                        };
                        break;
                    default:
                        var options = {
                            format: format,
                            viewMode: viewMode,
                            minViewMode: minViewMode,
                            autoclose: true,
                            clearBtn: true,
                            todayHighlight: true,
                            todayBtn: minViewMode == 'days' ? 'linked' : false,
                            startDate: startDate,
                            endDate: endDate,
                            beforeShowMonth: function (date) {
                                if (isSemester) {
                                    date = new Date(date.getTime() - (date.getTimezoneOffset() * 60 * 1000))
                                    return $.inArray(date.getMonth(), [0, 6]) > -1;
                                }
                                return true;
                            }
                        };
                        break;
                }

                var datepickerClass = configs.id ? configs.id : tableId.replace('#', '') + '-datepicker';
                $(node).empty().append(`<div class="${datepickerClass} my-button input-group float-left w-auto" style="flex-wrap: nowrap;">
                        <input type="text" class="form-control" value="${value}" placeholder="${placeholder}" />
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fa fa-calendar"></span></div>
                        </div>
                        <input type="hidden" />
                    </div>`);

                $(node).find('input').datepicker(options).on('changeDate', function (e) {
                    if (e.dates?.length > 0) {
                        var search = '';
                        let formattedText = '';
                        if (configs.format == 'yyyy-mm') {
                            search = `${e.format('yyyy')}-${e.format('mm')}`;
                            formattedText = moment(search, 'YYYY-MM').format('MMMM YYYY');
                        } else if (configs.format == 'mm') {
                            search = `${e.format('mm')}`;
                            formattedText = moment(search, 'MM').format('MMMM');
                        } else if (config?.type == 'years') {
                            search = `${e.format('yyyy')}`;
                            formattedText = search;
                        } else {
                            search = `${e.format('yyyy')}-${e.format('mm')}-${e.format('dd')}`;
                            formattedText = moment(search).format('D MMMM YYYY');
                        }

                        // $(this).val(formattedText);
                        $(`.${datepickerClass} input[type="hidden"]`).val(`${title} ${formattedText}`);

                        if (customConfigs?.ajax?.url) {
                            if (config.colSearch) {
                                table.columns(config?.colSearch).search(search).draw();
                            } else {
                                table.ajax.reload();
                            }
                        } else {
                            if (config.colSearch) {
                                table.columns(config?.colSearch).search(search).draw();
                            } else {
                                table.search(search).draw();
                            }
                        }
                    } else if (customConfigs?.ajax?.url) {
                        table.ajax.reload();
                    }
                });

                // if (value) {
                //     if (config.colSearch) {
                //         table.columns(config?.colSearch).search(value).draw();
                //     } else {
                //         table.search(value).draw();
                //     }
                // }
            }
        }
    };

    $.fn.dataTable.ext.buttons.dropdown = {
        init: function (dt, node, config) {
            if (config?.config) {
                let toggleColumn = config?.toggleColumn;
                let configs = config?.config;
                let allowClear = configs.allowClear !== undefined ? configs.allowClear : true;
                let placeholder = configs.placeholder || 'Pilih';
                let dropdownOptions = configs.options || {};
                let width = configs.width || 150;
                let dropdownClass = configs.id ? configs.id : tableId.replace('#', '') + '-dropdown';

                $(node).empty().append(`
                    <div class="${dropdownClass} my-button input-group float-left" style="flex-wrap: nowrap; width: ${width}px; max-width: ${width}px;">
                        <select class="form-control">
                            <option value="">${placeholder}</option>
                        </select>
                    </div>
                `);

                let $dropdown = $(node).find('select');
                Object.keys(dropdownOptions).forEach(function (key) {
                    let optionValue = dropdownOptions[key];
                    if (typeof optionValue === 'object' && optionValue !== null) {
                        $dropdown.append(new Option(optionValue[Object.keys(optionValue)[1]], optionValue[Object.keys(optionValue)[0]]));
                    } else {
                        $dropdown.append(new Option(optionValue, key));
                    }
                });

                $dropdown.select2({
                    theme: 'bootstrap4',
                    allowClear: allowClear,
                    placeholder: placeholder
                });

                $dropdown.on('change', function () {
                    if (toggleColumn) {
                        Object.keys(toggleColumn).forEach(key => {
                            table.column(key).visible(toggleColumn[key][$(this).val() || null]);
                        });
                    }

                    if (customConfigs?.ajax?.url) {
                        if (config.colSearch) {
                            table.columns(config?.colSearch).search($(this).val()).draw();
                        } else {
                            table.ajax.reload();
                            // table.search($(this).val()).draw();
                        }
                    } else {
                        if (config.colSearch) {
                            var columnData = table.column(config?.colSearch).data().toArray();
                            if (columnData.includes($(this).val())) {
                                if (config.colSearch) {
                                    table.columns(config.colSearch).search($(this).val()).draw();
                                } else {
                                    table.search($(this).val()).draw();
                                }
                            } else {
                                table.columns(config.colSearch).search('').draw();
                            }
                        } else {
                            table.search($(this).val()).draw();
                        }
                    }
                });

                setTimeout(function () {
                    let selectedValue = config.selected || '';
                    if (selectedValue !== '') {
                        $dropdown.val(selectedValue).trigger('change');
                    }
                }, 500);
            }
        }
    };

    function globalExportAction(e, dt, button, config, n) {
        var totalRecords = dt.page.info().recordsTotal;

        // Apply the custom logic only if totalRecords is less than 1000
        if (totalRecords < 5000) {
            var oldStart = dt.settings()[0]._iDisplayStart;
            var oldLength = dt.settings()[0]._iDisplayLength;

            dt.one('preXhr', function (e, s, data) {
                data.start = 0;
                data.length = totalRecords;
            });

            dt.one('draw', function (e, settings, json) {
                if (button[0].className.includes('buttons-copy')) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config, n);
                } else if (button[0].className.includes('buttons-pdf')) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config, n);
                } else if (button[0].className.includes('buttons-csv')) {
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config, n);
                } else if (button[0].className.includes('buttons-excel')) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config, n);
                } else if (button[0].className.includes('buttons-print')) {
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config, n);
                }

                dt.one('preXhr', function (e, s, data) {
                    data.start = oldStart;
                    data.length = oldLength;
                });

                dt.one('draw', function (e, settings, json) {
                    dt.page(oldStart / oldLength).draw(false);
                });

                dt.draw(false);
            });

            dt.draw();
        } else {
            // If totalRecords >= 1000, use the default export action
            if (button[0].className.includes('buttons-copy')) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config, n);
            } else if (button[0].className.includes('buttons-pdf')) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config, n);
            } else if (button[0].className.includes('buttons-csv')) {
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config, n);
            } else if (button[0].className.includes('buttons-excel')) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config, n);
            } else if (button[0].className.includes('buttons-print')) {
                $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config, n);
            }
        }
    }

    function getExportButtonsConfig(exportAllRows = true) {
        var filename = function () {
            const hiddenValues = $('.my-button input[type="hidden"]').map(function () {
                return $(this).val();
            }).get();

            return hiddenValues.find(function (value) {
                return value && value.trim();
            }) || 'file';
        }

        var globalExportOptions = {
            // columns: ':visible',
            columns: function (idx, data, node) {
                return ($(node).is(':visible') && !$(node).hasClass('exclude-export')) || $(node).hasClass('include-export');
            },
            orthogonal: 'export',
            format: {
                body: function (data, row, column, node) {
                    if ($(node).hasClass('exclude-export')) {
                        return ''; // Exclude data from export
                    }
                    return data && typeof data === 'string'
                        ? data.replace(/<br\s*\/?>/gi, ' ')
                            .replace(/<[^>]+>/g, '')
                            .replace(/&nbsp;/g, ' ')
                        : data;
                }
            },
            // modifier: {
            //     order: 'current',
            //     page: 'all',
            //     selected: null,
            // },
        };

        var config = [
            {
                extend: 'copyHtml5',
                filename: filename,
                exportOptions: globalExportOptions,
            },
            {
                extend: 'csvHtml5',
                filename: filename,
                exportOptions: globalExportOptions,
            },
            {
                extend: 'excelHtml5',
                filename: filename,
                exportOptions: globalExportOptions,
                messageTop: ' ',
                autoFilter: true,
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var stylesheet = xlsx.xl['styles.xml'];

                    if (!sheet || !stylesheet) {
                        console.error('Sheet or stylesheet not found.');
                        return;
                    }

                    // ADD TABLE BORDER
                    // Add a new border style
                    var borderStyle = '<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>';
                    $('borders', stylesheet).append(borderStyle);
                    var borderId = $('borders border', stylesheet).length - 1;

                    // Add a new fill style for the header
                    var fillStyle = '<fill><patternFill patternType="solid"><fgColor rgb="FFFF00"/><bgColor indexed="64"/></patternFill></fill>';
                    $('fills', stylesheet).append(fillStyle);
                    var fillId = $('fills fill', stylesheet).length - 1;

                    // Add a new cellXf style for the border and fill
                    var borderCellXf = `<xf xfId="0" borderId="${borderId}" applyBorder="1"/>`;
                    var fillCellXf = `<xf xfId="0" fillId="${fillId}" applyFill="1"/>`;
                    $('cellXfs', stylesheet).append(borderCellXf);
                    $('cellXfs', stylesheet).append(fillCellXf);

                    // Get the newly created style ids
                    var borderCellXfId = $('cellXfs xf', stylesheet).length - 2;
                    var fillCellXfId = $('cellXfs xf', stylesheet).length - 1;

                    // Apply border to all cells except messageTop and messageBottom
                    $(sheet).find('row c').each(function () {
                        var cellRef = $(this).attr('r');
                        // Skip messageTop and messageBottom cells if applicable
                        if (cellRef !== 'A1' && cellRef !== 'A2') { // Adjust based on actual references
                            $(this).attr('s', borderCellXfId); // Apply the border style
                        }
                    });

                    // STYLING HEADER
                    // Add a new font to the font list
                    $('fonts', stylesheet).append('<font><b/><sz val="14"/><color rgb="008000"/><name val="Arial"/></font>');
                    // Add a new cellXfs (cell format) to the list, with alignment
                    $('cellXfs', stylesheet).append(`<xf xfId="0" fontId="${$('fonts font', stylesheet).length - 1}" applyFont="1" applyAlignment="1">
                                                        <alignment horizontal="center" vertical="center"/>
                                                    </xf>`);
                    // Apply the new style to the A1 cell
                    $('c[r=A1]', sheet).attr('s', $('cellXfs xf', stylesheet).length - 1);

                    // // Selector to add a border
                    // sheet.querySelectorAll('row c[r*="10"]').forEach((el) => {
                    //     el.setAttribute('s', '25');
                    // });

                    // // set bold
                    // sheet.querySelectorAll('row c[r^="C"]').forEach((el) => {
                    //     el.setAttribute('s', '2');
                    // });

                    // // Loop over the cells in column `C` - set background
                    // sheet.querySelectorAll('row c[r^="C"]').forEach((row) => {
                    //     // Get the value
                    //     let cell = row.querySelector('is t');

                    //     if (cell && cell.textContent === 'New York') {
                    //         row.setAttribute('s', '20');
                    //     }
                    // });

                    // // Center align the header cells in the table
                    // $('thead th', sheet).each(function() {
                    //     var cellRef = $(this).attr('r');
                    //     // Set the cell format style (s attribute) to the new style
                    //     $(this).attr('s', $('cellXfs xf', stylesheet).length - 1);
                    // });

                    // // Insert a new empty row after the header
                    // $($('row:first', sheet)).after('<row r="2"></row>');
                    // // Adjust the row number for all subsequent rows
                    // $('row', sheet).each(function() {
                    //     var rowIndex = parseInt($(this).attr('r'));
                    //     if (rowIndex >= 2) {
                    //         $(this).attr('r', rowIndex + 1);
                    //     }
                    // });
                    // // Adjust the cell references
                    // $('c', sheet).each(function() {
                    //     var cellRef = $(this).attr('r');
                    //     var rowIndex = parseInt(cellRef.match(/\d+/)[0]);
                    //     if (rowIndex >= 2) {
                    //         var newCellRef = cellRef.replace(rowIndex, rowIndex + 1);
                    //         $(this).attr('r', newCellRef);
                    //     }
                    // });
                }
            },
            {
                extend: 'pdfHtml5',
                filename: filename,
                exportOptions: globalExportOptions,
                orientation: 'landscape',
                pageSize: 'A4', // Legal
                text: 'PDF',
                customize: function (doc) {
                    doc.styles = {
                        title: {
                            fontSize: 18,
                            bold: true,
                            alignment: 'center'
                        },
                        tableHeader: {
                            // alignment: 'center',
                            bold: true,
                            fontSize: 12,
                            color: 'black'
                        },
                        // tableBody: {
                        //     alignment: 'center',
                        //     fontSize: 10
                        // },
                        // defaultStyle: {
                        //     alignment: 'center'
                        // }
                    };

                    // doc.defaultStyle.alignment = 'center';

                    // doc.content.splice(1, 0, {
                    //     margin: [0, 0, 0, 12],
                    //     alignment: 'center',
                    //     image:
                    //         'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAIAAAD/gAIDAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA9lpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcFJpZ2h0czpNYXJrZWQ9IkZhbHNlIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDoxN2FlYzk4Yy0zMjgzLTExZGEtYTIzOC1lM2UyZmFmNmU5NjkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QUYzODU5RTYxNDNCMTFFNTlBNjVCOTY4NjAwQzY5QkQiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QUYzODU5RTUxNDNCMTFFNTlBNjVCOTY4NjAwQzY5QkQiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowODgwMTE3NDA3MjA2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'
                    // });

                    // doc.styles.tableHeader = {
                    //     fontSize: 18,
                    //     bold: true,
                    //     alignment: 'center'
                    // };

                    // doc.content.splice(0, 0, {
                    //     text: title,
                    //     style: 'header'
                    // });
                    // doc.styles.header = {
                    //     fontSize: 18,
                    //     bold: true,
                    //     alignment: 'center'
                    // };

                    // doc.content[1].table.widths = '*'.repeat(doc.content[1].table.body[0].length).split('');
                    // doc.styles.tableHeader.fontSize = 10; // Customize table header font size
                    // doc.defaultStyle.fontSize = 8; // Customize content font size
                    // doc.content[1].layout = {
                    //     hLineWidth: function(i, node) {
                    //         return (i === 0 || i === node.table.body.length) ? 1 : 0.5;
                    //     },
                    //     vLineWidth: function(i, node) {
                    //         return (i === 0 || i === node.table.widths.length) ? 1 : 0.5;
                    //     },
                    //     hLineColor: function(i, node) {
                    //         return (i === 0 || i === node.table.body.length) ? 'black' : 'gray';
                    //     },
                    //     vLineColor: function(i, node) {
                    //         return (i === 0 || i === node.table.widths.length) ? 'black' : 'gray';
                    //     },
                    //     paddingLeft: function(i, node) {
                    //         return 4;
                    //     },
                    //     paddingRight: function(i, node) {
                    //         return 4;
                    //     },
                    //     paddingTop: function(i, node) {
                    //         return 2;
                    //     },
                    //     paddingBottom: function(i, node) {
                    //         return 2;
                    //     },
                    //     fillColor: function(rowIndex, node, columnIndex) {
                    //         return (rowIndex % 2 === 0) ? '#CCCCCC' : null;
                    //     }
                }
            },
            {
                extend: 'print',
                filename: filename,
                exportOptions: globalExportOptions,
                // autoPrint: false,
                customize: function (win) {
                    // Center the title
                    $(win.document.body).find('h1').css({
                        'text-align': 'center',
                        'margin': '0 auto'
                    }).html(title);

                    $(win.document.body)
                        .find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');

                    // Attempt to remove headers and footers
                    var style = '<style>@page { size: auto;  margin-top: 5mm; margin-bottom: 10mm; }</style>';
                    $(win.document.head).append(style);

                    // $(win.document.body)
                    //     .css('font-size', '10pt')
                    //     .prepend(
                    //         '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'
                    //     );

                    // // Add striped color to rows
                    // $(win.document.body).find('table tr:odd').css('background-color', '#f2f2f2'); // Light grey for odd rows
                    // $(win.document.body).find('table tr:even').css('background-color', 'white'); // White for even rows

                    // // Ensure the entire document has a white background
                    // $(win.document.body).css({
                    //     'background-color': 'white',
                    //     'color': 'black'
                    // });
                },
                // customScripts: [
                //     'https://unpkg.com/pagedjs/dist/paged.polyfill.js'
                // ],
                // messageTop: function() {
                //     printCounter++;

                //     if (printCounter === 1) {
                //         return 'This is the first time you have printed this document.';
                //     } else {
                //         return (
                //             'You have printed this document ' + printCounter + ' times'
                //         );
                //     }
                // },
                // messageBottom: null
            },
        ];

        if (exportAllRows) {
            config.forEach(function (button) {
                if (button.extend !== 'print') {
                    button.action = globalExportAction;
                }
            });
        }

        return config;
    }

    table = $(tableId).DataTable($.extend(true, configs, customConfigs));

    // table.on('init.dt', function (e, settings) {
    //     console.log('DataTable ' + tableId + 'initialized:', settings);
    // });

    table.on('order.dt search.dt', function () {
        let i = 1;
        table.cells(null, 0, {
            search: 'applied',
            order: 'applied'
        }).every(function (cell) {
            this.data(i++);
        });
    });

    // Handle row selection
    table.on('select', function (e, dt, type, indexes) {
        var selectedData = table.rows(indexes).data().pluck('row_id').toArray();
        selectedData.forEach(function (id) {
            // Check if the id is already in the selectedIds array
            if (!selectedIds.includes(id)) {
                selectedIds.push(id);
            }
        });
    });

    // Handle row deselection
    table.on('deselect', function (e, dt, type, indexes) {
        var deselectedData = table.rows(indexes).data().pluck('row_id').toArray();
        selectedIds = selectedIds.filter(function (id) {
            return deselectedData.indexOf(id) === -1;
        });
    });

    table.on('preXhr.dt', function (e, settings, data) {
        $.busyLoadFull("show");
    });

    table.on('xhr.dt', function (e, settings, json, xhr) {
        $.busyLoadFull("hide");
    });

    return table;
}
