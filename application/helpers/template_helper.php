<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_notifs')) {
    function get_notifs()
    {
        return [
            'antrian' => NOTIF_H_MIN_1,
            'sidang' => NOTIF_JADWAL_SIDANG,
            'calendar' => NOTIF_CALENDAR,
            'jurnal' => NOTIF_JOURNAL,
            'ac' => NOTIF_AKTA_CERAI,
        ];
    }
}

if (!function_exists('text_alamat_kantor')) {
    function text_alamat_kantor()
    {
        return '📌 Alamat Kantor :
' . SATKER_NAME . '. ' . SATKER_ADDRESS;
    }
}

if (!function_exists('text_footer')) {
    function text_footer()
    {
        return 'ℹ️ _*Pesan ini dikirim oleh sistem secara otomatis. Balas OK untuk informasi lebih lanjut*_';
    }
}

if (!function_exists('text_antrian')) {
    function text_antrian()
    {
        return '🏛️ *INFORMASI ' . strtoupper(SATKER_NAME) . '*

*Nomor Perkara* : %s
*Tanggal Sidang* : %s
*Agenda* : %s
%s

%s

%s';
    }
}

if (!function_exists('text_sidang')) {
    function text_sidang()
    {
        return '🏛️ *INFORMASI ' . strtoupper(SATKER_NAME) . '*

*Nomor Perkara* : %s
*Tanggal Sidang* : %s
*Agenda* : %s
%s

%s';
    }
}

if (!function_exists('text_calendar')) {
    function text_calendar()
    {
        return '🏛️ *INFORMASI COURT CALENDAR ' . strtoupper(SATKER_NAME) . '*

*Nomor Perkara* : %s
*Agenda* : %s
*Batas waktu : %s pukul %s*
%s

%s';
    }
}

if (!function_exists('text_psp')) {
    function text_psp()
    {
        return '🏛️ *INFORMASI SISA PANJAR ' . strtoupper(SATKER_NAME) . '*

*Nomor Perkara* : %s
*Tanggal Putus* : %s
%s
*Sisa Panjar : %s*

Anda dapat melakukan pengambilan sisa panjar pada hari dan jam kerja di Kantor ' . SATKER_NAME . ' dengan *membawa KTP asli dan diambil sendiri (tidak bisa diwakilkan)*

_Abaikan notifikasi ini jika Anda sudah melakukan pengambilan sisa panjar._

%s

%s';
    }
}

if (!function_exists('text_ac')) {
    function text_ac()
    {
        return '🏛️ *INFORMASI AKTA CERAI ' . strtoupper(SATKER_NAME) . '*

*Nomor Perkara* : %s
*Tanggal Akta Cerai : %s*
%s

Anda dapat melakukan pengambilan Akta Cerai pada hari dan jam kerja di Kantor ' . SATKER_NAME . ' dengan *membawa KTP asli dan diambil sendiri (tidak bisa diwakilkan)*

_Abaikan notifikasi ini jika Anda sudah melakukan pengambilan Akta Cerai._

%s

%s';
    }
}

if (!function_exists('get_text')) {
    function get_text($type)
    {
        $text = [
            'antrian' => text_antrian(),
            'sidang' => text_sidang(),
            'calendar' => text_calendar(),
            'jurnal' => text_psp(),
            'ac' => text_ac(),
        ];
        return isset($text[$type]) ? $text[$type] : '';
    }
}

if (!function_exists('get_template')) {
    function get_template($type, $item)
    {
        if ($type == 'antrian' && $item->sidang_keliling == 'Y') {
            $type = 'sidang';
        }

        switch ($type) {
            case 'sidang':
                return sprintf(get_text($type), $item->nomor_perkara, formatDate($item->tanggal_sidang, "%A, %d %b %Y"), $item->agenda, pihak_pattern($item->para_pihak), text_footer());
            case 'antrian':
                return sprintf(get_text($type), $item->nomor_perkara, formatDate($item->tanggal_sidang, "%A, %d %b %Y"), $item->agenda, pihak_pattern($item->para_pihak), text_alamat_kantor(), text_footer());
            case 'keliling':
                return sprintf(get_text($type), $item->nomor_perkara, formatDate($item->tanggal_sidang, "%A, %d %b %Y"), $item->agenda, pihak_pattern($item->para_pihak), text_footer());
            case 'calendar':
                return sprintf(get_text($type), $item->nomor_perkara, $item->rencana_agenda, formatDate($item->rencana_tanggal, "%A, %d %b %Y"), date('H:i', strtotime($item->rencana_jam)), pihak_pattern($item->para_pihak), text_footer());
            case 'jurnal':
                return sprintf(get_text(intval($item->sisa_panjar) < 0 ? 'jurnal_minus' : $type), $item->nomor_perkara, formatDate($item->tanggal_putusan, "%d %b %Y"), pihak_pattern($item->para_pihak), add_currency_symbol($item->sisa_panjar), text_alamat_kantor(), text_footer());
            case 'ac':
                return sprintf(get_text($type), $item->nomor_perkara, formatDate($item->tgl_akta_cerai, "%d %b %Y"), pihak_pattern($item->para_pihak), text_alamat_kantor(), text_footer());
        }
        return '';
    }
}

if (!function_exists('replace_pattern')) {
    function replace_pattern($string)
    {
        return str_replace(['Penggugat:', 'Pemohon:', 'Tergugat:', 'Termohon:', '<br />'], ['*Penggugat* :', '*Pemohon* :', '*Tergugat* :', '*Termohon* :', substr_count($string, '<br />') > 1 ? '
▪️ ' : ' '], $string);
    }
}

if (!function_exists('pihak_pattern')) {
    function pihak_pattern($string)
    {
        $pos_termohon = strpos($string, 'Termohon:');
        $pos_tergugat = strpos($string, 'Tergugat:');
        if ($pos_termohon !== false || $pos_tergugat !== false) {
            $pos = min($pos_termohon !== false ? $pos_termohon : PHP_INT_MAX, $pos_tergugat !== false ? $pos_tergugat : PHP_INT_MAX);

            return replace_pattern(preg_replace('/<br \/>(?=[^<]*$)/', '', substr($string, 0, $pos))) . '
' . replace_pattern(substr($string, $pos));
        }
        return replace_pattern($string);
    }
}

if (!function_exists('get_notif_criteria')) {
    function get_notif_criteria($filter = '')
    {
        $range = get_notif_range($filter);
        switch ($filter) {
            case 'antrian':
            case 'sidang':
                return "tanggal_sidang BETWEEN CURDATE() + INTERVAL {$range[0]} DAY AND CURDATE() + INTERVAL {$range[1]} DAY";
            case 'calendar':
                return "((rencana_tanggal BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL {$range} DAY) OR (rencana_tanggal = CURRENT_DATE() AND rencana_jam > CURRENT_TIME())) AND rencana_agenda REGEXP 'perbaikan|jawaban|replik|duplik|kesimpulan'";
            case 'jurnal':
                // jenis perkara 346 = cerai talak
                // status putusan 62 = Dikabulkan
                return "(tahapan_id = 10 AND tanggal_putusan > CURRENT_DATE() + INTERVAL {$range} DAY AND (pemasukan - pengeluaran) > 0 AND (CASE WHEN (perkara.jenis_perkara_id = 346 AND status_putusan_id = 62) THEN (tgl_ikrar_talak IS NOT NULL) ELSE (1=1) END))";
            case 'ac':
                return "tgl_akta_cerai IS NOT NULL AND (tgl_penyerahan_akta_cerai IS NOT NULL AND tgl_penyerahan_akta_cerai_pihak2 IS NULL) AND tgl_akta_cerai > CURRENT_DATE() + INTERVAL {$range} DAY";
            default:
                return '';
        }
    }
}

if (!function_exists('get_notif_range')) {
    function get_notif_range($filter = '')
    {
        $range = [
            'antrian' => [DAY_START_ANTRIAN, DAY_END_ANTRIAN], //start-end antrian
            'sidang' => [DAY_START_SIDANG, DAY_END_SIDANG], //start-end sidang
            'calendar' => DAY_END_CALENDAR, //agenda n hari ke depan
            'jurnal' => DAY_START_JURNAL, //putus bersaldo n hari ke belakang
            'ac' => DAY_START_AC, //terbit AC n hari ke belakang
        ];
        return isset($range[$filter]) ? $range[$filter] : $range['antrian'];
    }
}
