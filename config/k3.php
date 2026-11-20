<?php
return [
 'modules'=>['inspections'=>'Inspeksi rutin','schedule'=>'Jadwal inspeksi','reports'=>'Safety report','incidents'=>'Riwayat kecelakaan','documents'=>'Dokumen & SOP'],
 'areas'=>['Produksi A','Produksi B','Gudang','Kantor','Utility'],
 'types'=>['APAR','APAB','Hidran','Safety Cabinet','Emergency Exit','Kotak P3K','Ruang P3K'],
 'categories'=>['inspections'=>['Sesuai','Tidak sesuai'],'schedule'=>['Inspeksi bulanan','Inspeksi khusus'],'reports'=>['Unsafe condition','Unsafe action'],'incidents'=>['Nearmiss','FAC','RWC','LTA','Fatality'],'documents'=>['Kebijakan','SOP','Instruksi kerja','Formulir']],
 'checklists'=>[
 'APAR'=>['Identitas dan label terbaca','Akses tidak terhalang','Segel dan pin tersedia','Kondisi tabung dan selang baik','Indikator tekanan diperiksa'],
 'APAB'=>['Identitas terbaca','Roda dan pegangan baik','Selang dan nozzle baik','Segel tersedia','Indikator tekanan diperiksa'],
 'Hidran'=>['Akses bebas hambatan','Selang tersedia dan baik','Nozzle tersedia','Valve dalam kondisi baik','Box dan penanda baik'],
 'Safety Cabinet'=>['Akses bebas hambatan','Daftar isi tersedia','Peralatan lengkap','Peralatan tidak rusak'],
 'Emergency Exit'=>['Jalur bebas hambatan','Pintu dapat dibuka','Penanda terlihat','Penerangan diperiksa'],
 'Kotak P3K'=>['Kotak bersih dan mudah dijangkau','Daftar isi tersedia','Jumlah persediaan diperiksa','Tanggal kedaluwarsa diperiksa'],
 'Ruang P3K'=>['Ruangan bersih','Akses mudah','Fasilitas diperiksa','Persediaan diperiksa']],
];