#!/bin/bash

# contoh cron kirim notifikasi sidang setiap jam 09.00 dan 18.00
# 0 9,18 * * * /var/www/html/noisy/cron/notif_sidang.sh

TOKEN="isi_dengan_token_dialogwa.web.id"
NOTIF_URL="isi_dengan_url_aplikasi_noisy" 

# NOTIF_URL adalah url notifikasi sesuai kategori notifikasi yang akan diotomatisasi
# contoh : 
# NOTIF_URL="http://IP_LOKAL_ANDA/noisy/api/send_notif/sidang"
#
#
# Kategori notifikasi dapat berupa :
# http://IP_LOKAL_ANDA/noisy/api/send_notif/antrian
# http://IP_LOKAL_ANDA/noisy/api/send_notif/sidang
# http://IP_LOKAL_ANDA/noisy/api/send_notif/calendar
# dll

curl -X POST -H "Authorization: Bearer ${TOKEN}" ${NOTIF_URL} 

# Satu file script ini adalah untuk 1 kategori notifikasi.
# Untuk mengotomatisasi notifikasi yang lain, duplikasi file ini lalu modifikasi sesuai kategori notifikasinya