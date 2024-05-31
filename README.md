# NOISY
Whatsapp Notification System

####  Daftar Kirim
![alt text](https://dialogwa.id/images/1/1_noisy_daftar_kirim.png)

####  Riwayat Notifikasi
![alt text](https://dialogwa.id/images/1/2_noisy_riwayat_notif.png)

####  Progress Kirim
![alt text](https://dialogwa.id/images/1/3_noisy_sent.png)

-------------------------------



## INSTALASI


###  FILE INDEX.PHP
Duplikasi file index.example.php, file hasil duplikat rename menjadi index.php


###  MODIFIKASI KONFIGURASI FILE
1. Masuk ke folder application\config\
2. Duplikasi file constants.example.php, file hasil duplikat rename menjadi constants.php
3. Pada baris paling bawah cari baris berikut dan modifikasi value sesuai environment Anda :

```
defined('DB_HOST') or define('DB_HOST', 'ip_local');
defined('DB_USER') or define('DB_USER', 'nama_user_db');
defined('DB_PASSWORD') or define('DB_PASSWORD', 'password_db');
defined('DB_SIPP') or define('DB_SIPP', 'nama_db_sipp');
defined('DB_NOTIFIKASI') or define('DB_NOTIFIKASI', 'noisy');
```


###  SQL
File: sql\noisy.sql

Buat database dengan cara menjalankan noisy.sql


###  MODIFIKASI KONFIGURASI DATABASE
Table: configs

```
* APP_VERSION
* APP_NAME 
* APP_SHORT_NAME
* SATKER_NAME
* SATKER_ADDRESS
* DIALOGWA_API_URL --string. url api dialogwa.id
* DIALOGWA_TOKEN --string. token dialogwa.id
* DIALOGWA_SESSION --string. sesi online dialogwa.id
* WA_TEST_TARGET --string. nomor WA untuk tes penerima notifikasi
* DAY_START_ANTRIAN --int. start hari sidang. sistem akan mencari jadwal sidang mulai tanggal ini
* DAY_END_ANTRIAN --int. end hari sidang. sistem akan mencari jadwal sidang sampai tanggal ini
* DAY_START_SIDANG --int. start hari sidang. sistem akan mencari jadwal sidang mulai tanggal ini
* DAY_END_SIDANG --int. end hari sidang. sistem akan mencari jadwal sidang sampai tanggal ini
* DAY_END_CALENDAR --int. end hari calendar. sistem akan mencari jadwal agenda sampai tanggal ini
* DAY_START_JURNAL --int. start hari jurnal. sistem akan mencari perkara putus sejak tanggal ini (NEGATIVE VALUE)
* DAY_START_AC --int. start hari ac. sistem akan mencari ac terbit sejak tanggal ini (NEGATIVE VALUE)
```


###  DISABLE / ENABLE JENIS NOTIFIKASI
File: application\helpers\template_helper.php

Pada function get_notifs(), hapus / jadikan comment kategori notifikasi yang tidak akan digunakan


###  MODIFIKASI TEMPLATE NOTIFIKASI
File: application\helpers\template_helper.php

```
* text_alamat_kantor()
* text_footer()
* text_antrian()
* text_sidang()
* text_calendar()
* text_psp()
* text_ac()
```


###  OTOMATISASI KIRIM NOTIFIKASI [OPSIONAL]
Pengiriman notifikasi dapat dilakukan manual melalui aplikasi menggunakan tombol [KIRIM NOTIFIKASI], atau bisa juga diotomatisasi sesuai kebutuhan. Otomatisasi bersifat opsional, tidak perlu dilakukan bila pengiriman hanya akan dilakukan secara manual oleh operator aplikasi.

Bila diperlukan otomatisasi, contoh script ada di : folder cron\notif_sidang.sh
Pada file tersebut, modifikasi variabel :

```
TOKEN="isi_dengan_token_dialogwa.id"
NOTIF_URL="isi_dengan_url_aplikasi_noisy" 
```

NOTIF_URL adalah url notifikasi sesuai kategori notifikasi yang akan diotomatisasi
contoh : 
```
NOTIF_URL="http://IP_LOKAL_ANDA/noisy/api/send_notif/sidang"
```

Kategori notifikasi dapat berupa :
```
* http://IP_LOKAL_ANDA/noisy/api/send_notif/antrian
* http://IP_LOKAL_ANDA/noisy/api/send_notif/sidang
* http://IP_LOKAL_ANDA/noisy/api/send_notif/calendar
dll
```


> TEST SCRIPT:
> 1. Masuk ke folder cron di mana script tersebut berada
> 2. Jalankan script tersebut pada terminal untuk mengecek apakah proses pengiriman notifikasi berjalan.
> 3. Bila script tidak running, pastikan script tersebut executable :
>     $ sudo chmod +x notif_sidang.sh
> 4. Coba jalankan script kembali
> 5. Setelah script dipastikan works, anda dapat mengotomatisasi script tersebut.


Untuk mengotomatisasi script, buat cron dengan contoh kirim notifikasi sidang setiap jam 09.00 dan 18.00 :
```
0 9,18 * * * /var/www/html/noisy/cron/notif_sidang.sh
```

Satu file script tersebut adalah untuk 1 kategori notifikasi.
Untuk mengotomatisasi notifikasi yang lain, duplikasi file tersebut lalu modifikasi sesuai kategori notifikasinya


###  DEPLOY PRODUCTION
> [!CAUTION]
> STEP INI HANYA DILAKUKAN BILA:
> 1. Fungsi pengiriman notifikasi berhasil dilakukan
> 2. Notifikasi yang terkirim data dan teks sudah benar
> 3. Pengujian sistem notifikasi sudah dilakukan secara menyeluruh dan sesuai harapan
>
> 
> Setelah aplikasi siap untuk digunakan LIVE, berikut yang harus dilakukan :
> 1. Pada folder project, buka file index.php
> 2. Pada baris 57, ubah :
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
> ```
> 
> menjadi
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
> ```
