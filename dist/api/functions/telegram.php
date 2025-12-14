<?php
class Telegram {

    private $dbFactory;
    private $system;
    private $token;
    private $Traffic;
    private $Curl;
    private $utilities;
    public function __construct() {

        $this->dbFactory = new DBConnectionFactory();
        $this->Traffic = new Traffic();
        $this->Curl = new Curl();
        $this->utilities = new Utilities();
        $this->system = new System;
        $this->token = $this->system->App->botToken;
    }

    private function getChatId($username) {
        $conn = $this->dbFactory->createConnection();
        $stmt = $conn->prepare("SELECT telegram_id FROM sys_users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['telegram_id'];
    }

    private function getAllChatId() {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT telegram_id FROM sys_users");
        $stmt->execute();
        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'telegram_id');

        return $result;
    }

    private function getChatIdByRole($role) {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT telegram_id FROM sys_users WHERE role_id = :role");
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'telegram_id');

        return $result;

    }

    private function getChatIdByDepartment($department) {
        $conn = $this->dbFactory->createConnection();

        if (in_array($department, ['registration', 'permit', 'project', 'survey', 'plan', 'charting', 'translation'])) {
            $stmt = $conn->prepare("SELECT id FROM sys_hr_employee WHERE sub_department = :value");
        } elseif (in_array($department, ['mapping', 'operation', 'geospatial', 'management', 'business', 'hr', 'finance'])) {
            $stmt = $conn->prepare("SELECT id FROM sys_hr_employee WHERE department = :value");
        }
        $stmt->bindParam(':value', $department);
        $stmt->execute();
        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        $users = '{' . implode(',', $result) . '}';

        $stmt = $conn->prepare("SELECT telegram_id FROM sys_users WHERE employee_id = ANY (:id)");
        $stmt->bindParam(':id', $users);
        $stmt->execute();
        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'telegram_id');

        return $result;
    }

    private function getChatIdByFlow($status) {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT flow_role_id FROM ls_statuses WHERE id = :status');
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $role = $stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT telegram_id FROM sys_users WHERE role_id = ANY (:role)");
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'telegram_id');

        return $result;

    }

    private function getChatIdByGroup($groupName) {
        $conn = $this->dbFactory->createConnection();
        $stmt = $conn->prepare("SELECT telegram_id FROM ls_telegram_group WHERE department = :groupName OR sub_department = :groupName");
        $stmt->bindParam(':groupName', $groupName);
        $stmt->execute();
        $result = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'telegram_id');

        return $result;
    }

    public function getMessageByFlow($status, $systemId=NULL, $refNo=NULL, $date=NULL, $item=NULL, $department=NULL, $progress=NULL, $reportType=NULL, $paymentMethod=NULL)
    {
        if ($systemId != NULL && $refNo != NULL) {
            $rujukan = $refNo;
            $permohonan = '#' . $systemId;
        } else if ($systemId != NULL) {
            $permohonan = '#' . $systemId;
        } else {
            $rujukan = $refNo;
        }

        switch ($status) {
            case 1:
                // $telegramMsg = "Permohonan Baru telah diterima. Sila sediakan Invois Caj Pendaftaran. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . " \n📆 Tarikh Permohonan : " . $date . "</strong>";
                $telegramMsg = "Pengesahan Permohonan telah dilakukan. Sila muat naik Invois Caj Pendaftaran bagi permohonan ini. \n\n<strong>🔗 No Permohonan : ".$permohonan."\n📝 Tajuk : " . $item . "</strong>";
                break;
            case 2:
                $telegramMsg = "Pembayaran Caj Pendaftaran telah dibuat oleh Pemohon. Sila semak resit pembayaran yang dimuat naik oleh pemohon. \n\n<strong>🔗 No Permohonan : " . $permohonan . " \n📆 Tarikh Bayaran : " . $date . "</strong>";
                break;
            case 3:
                $telegramMsg = "Resit Caj Pendaftaran Tidak Diterima. Sila semak permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . "</strong>";
                break;
            case 4:
                if ($paymentMethod == 1 || $paymentMethod == 4) {
                    $telegramMsg = "Permohonan Baru telah dihantar oleh pemohon menggunakan kaedah bayaran atas talian. Sila semak maklumat permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . " \n📆 Tarikh Permohonan : " . $date . "</strong>";
                } else if ($paymentMethod == 2) {
                    $telegramMsg = "Resit Bayaran Caj Pendaftaran telah disahkan. Sila semak maklumat permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . "</strong>";
                }  else if ($paymentMethod == 'POST') {
                    // Default message ke RO selepas pemohon buat permohonan
                    $telegramMsg = "Permohonan Baru telah dihantar oleh pemohon. Sila semak maklumat permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . " \n📆 Tarikh Permohonan : " . $date . "</strong>";
                } else {
                    $telegramMsg = "Maklumat Permohonan telah dikemaskini oleh pemohon. Sila semak maklumat terkini bagi permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . " \n 📆 Tarikh Kemaskini : " . $date . "</strong>";
                }

                break;
            case 5:
                $telegramMsg = "Maklumat Permohonan telah dikemaskini oleh pemohon. Sila semak maklumat terkini bagi permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan . "\n📝 Tajuk : " . $item . " \n 📆 Tarikh Kemaskini : " . $date . "</strong>";
                break;
            case 6:
                $telegramMsg = "Caj Pendaftaran telah disahkan bagi permohonan ini. \n\n<strong>🔗 No Permohonan : " . $permohonan .  " \n🔗 No Rujukan : " . $rujukan . " \n📝 Tajuk : " . $item . "</strong>";
                break;
            case 7:
                $telegramMsg = "Arahan Kerja telah dimuat naik. Sila Lantik Pegawai GIS bagi Penyediaan Pelan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n📝 Tajuk : " . $item . "</strong>";
                break;
            case 8:
                $telegramMsg = "Lantikan Pegawai GIS bagi penyediaan Pelan Cadangan Laluan Utiliti telah dilakukan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n👩‍💻 Pegawai GIS :  " . $item . "</strong>";
                break;
            case 9:
                $telegramMsg = "Arahan Kerja Operasi telah dimuat naik. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 10:
                if ($this->system->App->tenant == 'KUDRAT') {
                    $telegramMsg = "Arahan Kerja Operasi telah dimuat naik. Sila tetapkan Tarikh Lawatan Tapak. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                } else {
                    $telegramMsg = "PCL telah dimuat naik. Sila tetapkan Tarikh Lawatan Tapak. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                }
                break;
            case 11:
                $telegramMsg = "Tetapan Tarikh Lawatan Tapak: " . $reportType . " \n\n<strong>🔗 No Rujukan:  " . $rujukan . " \n👷‍♂️ Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 12:
                $telegramMsg = "";
                break;
            case 13:
                $telegramMsg = "Tarikh lawatan tapak telah disahkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n👷‍♂️ Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 14:
                $telegramMsg = "";
                break;
            case 15:
                $telegramMsg = "Laporan Lawatan Tapak telah disediakan. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n👷‍♂️ Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 16:
                $telegramMsg = "Laporan Lawatan Tapak telah diluluskan untuk proses Pindaan Cadangan Teknikal. Sila tunggu notifikasi selanjutnya untuk tindakan semakan Pindaan Cadangan Teknikal. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 17:
                $telegramMsg = "Dokumen Pindaan Cadangan Teknikal telah dimuat naik oleh pemohon. Sila buat semakan Pindaan Cadangan Teknikal. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 18:
                $telegramMsg = "";
                break;
            case 19:
                $telegramMsg = "";
                break;
            case 20:
                $telegramMsg = "";
                break;
            case 21:
                $telegramMsg = "";
                break;
            case 22:
                $telegramMsg = "";
                break;
            case 23:
                $telegramMsg = "";
                break;
            case 24:
                $telegramMsg = "";
                break;
            case 25:
                $telegramMsg = "";
                break;
            case 26:
                $telegramMsg = "";
                break;
            case 27:
                if ($department == 'finance') {
                    $telegramMsg = "Laporan Lawatan Tapak telah disahkan. Sila muat naik Sebut Harga. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                } else if ($department == 'operation') {
                    $telegramMsg = "Laporan Lawatan Tapak telah diluluskan untuk proses Permohonan Izin Lalu. Sila tunggu notifikasi selanjutnya untuk tindakan Penyediaan Ringkasan Projek dan Kiraan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                } else {
                    $telegramMsg = "Laporan Lawatan Tapak telah diluluskan untuk proses Permohonan Izin Lalu. Sila tunggu notifikasi selanjutnya untuk tindakan Penyediaan Ringkasan Projek dan Kiraan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                }
                break;
            case 28:
                $telegramMsg = "Sebut Harga telah di muatnaik oleh " . $item . ". Sila semak maklumat Sebut Harga ini. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 29:
                $telegramMsg = "Sebut Harga telah ditolak oleh " . $item . ". Sila semak maklumat Sebut Harga ini. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 30:
                $telegramMsg = "Persetujuan Sebut Harga telah disahkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 31:
                $telegramMsg = "Ringkasan Projek dan Kiraan Wang Cagaran telah Dimuat Naik. Sila muat naik Surat Permohonan Kelulusan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 32:
                $telegramMsg = "Surat Permohonan Kelulusan Izin Lalu telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 33:
                $telegramMsg = "Bukti Penghantaran Surat Permohonan KIL telah dimuat naik. Sila muat naik Surat Kelulusan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 34:
                $telegramMsg = "Surat Kelulusan Izin Lalu telah dimuat naik. Sila muat naik Surat Maklumbalas Kelulusan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 35:
                $telegramMsg = "Invois Caj Perkhidmatan telah dimuat naik. Sila muat naik Bukti Pembayaran Invois. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 36:
                $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu telah dimuat naik. Sila muat naik Invois Caj Perkhidmatan.\n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 37:
                $telegramMsg = "";
                break;
            case 38:
                $telegramMsg = "";
                break;
            case 39:
                $telegramMsg = "Permohonan Mula Kerja Ukur telah ditolak. \n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 40:
                $telegramMsg = "Permohonan Mula Kerja Ukur telah dihantar. \n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 41:
                $telegramMsg = "Permohonan Mula Kerja Ukur telah diluluskan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 42:
                $telegramMsg = "";
                break;
            case 43:
                $telegramMsg = "Pengesahan Kehadiran Berjaya. Sila teruskan dengan mengimbas QR Code Kehadiran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Nama Pengguna : " . $item . " </strong>";
                break;
            case 44:
                $telegramMsg = "Pengesahan Waktu Masuk Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan .  "\n👤 Nama Pengguna : " . $item . "</strong>";
                break;
            case 45:
                $telegramMsg = "Pengesahan Waktu Keluar Berjaya. Sila teruskan dengan Kemaskini Kemajuan Laporan Harian. \n\n<strong>🔗 No Rujukan : " . $rujukan . "   \n👤 Nama Pengguna : " . $item . "</strong>";
                break;
            case 46:
                $telegramMsg = "";
                break;
            case 47:
                $telegramMsg = "Serahan Data Kerja Ukur Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 48:
                $telegramMsg = "Laporan Penuh telah disemak. Sila teruskan dengan Pengesahan Laporan Penuh. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n👤 Nama Pengguna : " . $item . "  </strong>";
                break;
            case 49:
                $telegramMsg = "Laporan Penuh telah disahkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Nama Pengguna : " . $item . "  </strong>";
                break;
            case 50:
                $telegramMsg = "Perlantikan Pelukis PIU Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Pelukis Dilantik : " . $item . "</strong>";
                break;
            case 51:
                if ($progress <= 0) {
                    $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti Selesai. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak UDM : " . $item . "</strong>";
                } else {
                    $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti telah dikemaskini. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak UDM : " . $item ."</strong>";
                }
                break;
            case 52:
                $telegramMsg = "Perlantikan Pelukis TMP Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n👤 Pelukis Dilantik : " . $item . "</strong>";
                break;
            case 53:
                if ($progress <= 0) {
                    $telegramMsg = "Kemajuan Pelan Kawalan Trafik Selesai. \n Sila Teruskan dengan Penetapan Tarikh Kehadiran SR. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak TMP : " . $item . "</strong>";
                } else {
                    $telegramMsg = "Kemajuan Pelan Kawalan Trafik telah dikemaskini. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak TMP : " . $item . "</strong>";
                }
                break;
            case 54:
                $telegramMsg = "Tarikh Pengesahan Juru Ukur telah ditetapkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📅 Tarikh yang ditetapkan : " . $item . "</strong>";
                break;
            case 55:
                $telegramMsg = "";
                break;
            case 56:
                $telegramMsg = "";
                break;
            case 57:
                $telegramMsg = "";
                break;
            case 58:
                $telegramMsg = "";
                break;
            case 59:
                $telegramMsg = "";
                break;
            case 60:
                $telegramMsg = "";
                break;
            case 61:
                $telegramMsg = "";
                break;
            case 62:
                $telegramMsg = "";
                break;
            case 63:
                $telegramMsg = "";
                break;
            case 64:
                $telegramMsg = "";
                break;
            case 65:
                $telegramMsg = "";
                break;
            case 66:
                $telegramMsg = "";
                break;
            case 67:
                $telegramMsg = "Invois Caj Perkhidmatan telah dimuat naik. Sila Muat Naik Dokumen Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 68:
                $telegramMsg = "Caj Perkhidmatan telah Disahkan. Sila teruskan dengan menyemak Permohonan Permit. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 69:
                $telegramMsg = "Caj Perkhidmatan telah Ditolak. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 70:
                $telegramMsg = "Dokumen Permohonan Permit Kerja telah dimuat naik. Sila muat naik Surat Permohonan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 71:
                $telegramMsg = "Surat Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 72:
                $telegramMsg = "Akuan Serahan Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Surat Kelulusan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 73:
                $telegramMsg = "Surat Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Perakuan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 74:
                $telegramMsg = "Perakuan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 75:
                $telegramMsg = "";
                break;
            case 76:
                $telegramMsg = "";
                break;
            case 77:
                $telegramMsg = "";
                break;
            case 78:
                $telegramMsg = "";
                break;
            case 79:
                $telegramMsg = "Akuan Serahan Perakuan Permit Kerja telah dimuat naik. Sila muat naik Notis Mula Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 80:
                $telegramMsg = "Notis Mula Kerja telah dimuat naik. Sila muat naik Surat Makluman Mula Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 81:
                $telegramMsg = "Surat Makluman Mula Kerja telah dimuat naik. Sila muat naik Akuan Serahan Makluman Mula Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 82:
                $telegramMsg = "";
                break;
            case 83:
                $telegramMsg = "";
                break;
            case 84:
                $telegramMsg = "";
                break;
            case 85:
                $telegramMsg = "";
                break;
            case 86:
                $telegramMsg = "";
                break;
            case 87:
                $telegramMsg = "";
                break;
            case 88:
                $telegramMsg = "Akuan Serahan Makluman Mula Kerja telah dimuat naik. Sila muat naik Notis Siap Kerja setelah Kerja Selesai. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 89:
                $telegramMsg = "";
                break;
            case 90:
                $telegramMsg = "";
                break;
            case 91:
                $telegramMsg = "";
                break;
            case 92:
                $telegramMsg = "Dokumen Permohonan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Surat Permohonan Lanjutan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 93:
                $telegramMsg = "Surat Permohonan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Lanjutan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 94:
                $telegramMsg = "Akuan Serahan Permohonan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Surat Kelulusan Lanjutan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 95:
                $telegramMsg = "Surat Kelulusan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Perakuan Lanjutan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 96:
                $telegramMsg = "Perakuan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Lanjutan Perakuan Permit Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 97:
                $telegramMsg = "Tugasan Mula Kerja Ukur (PSB). \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 98:
                $telegramMsg = "Pembahagian Tugasan Pengukuran (PSB) Selesai. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📆 Tarikh : " . $date . " \n👥 Kumpulan : " . $item . "</strong>";
                break;
            case 99:
                $telegramMsg = "Pengesahan Kehadiran (PSB) Berjaya. Sila teruskan dengan mengimbas QR Code Kehadiran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Nama Pengguna : " . $item . " </strong>";
                break;
            case 100:
                $telegramMsg = "Pengesahan Waktu Masuk (PSB) Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n👤 Nama Pengguna : " . $item . "</strong>";
                break;
            case 101:
                $telegramMsg = "Pengesahan Waktu Keluar (PSB) Berjaya. Sila teruskan dengan Kemaskini Kemajuan Laporan Harian. \n\n<strong>🔗 No Rujukan : " . $rujukan . "   \n👤 Nama Pengguna : " . $item . "</strong>";
                break;
            case 102:
                $telegramMsg = "";
                break;
            case 103:
                $telegramMsg = "Serahan Data Kerja Ukur (PSB) Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 104:
                $telegramMsg = "Laporan Penuh (PSB) telah disemak. Sila teruskan dengan Pengesahan Laporan Penuh. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n👤 Nama Pengguna : " . $item . "  </strong>";
                break;
            case 105:
                $telegramMsg = "Laporan Penuh (PSB) telah disahkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Nama Pengguna : " . $item . "  </strong>";
                break;
            case 106:
                $telegramMsg = "Perlantikan Pelukis PSB Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "  \n👤 Pelukis Dilantik : " . $item . "</strong>";
                break;
            case 107:
                if ($progress <= 0) {
                    $telegramMsg = "Kemajuan Pelan Siap Bina Selesai. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak PSB : " . $item . "</strong>";
                } else {
                    $telegramMsg = "Kemajuan Pelan Siap Bina telah dikemaskini. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📏 Jarak PSB : " . $item . "</strong>";
                }
                break;
            case 108:
                $telegramMsg = "Tarikh Pengesahan Juru Ukur (PSB) telah ditetapkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n📅 Tarikh yang ditetapkan : " . $item . "</strong>";
                break;
            case 109:
                $telegramMsg = "Pengesahan Cop Bilangan Endorsement (PSB). \n\n<strong>🔗 No Rujukan : " . $rujukan . " \n🔗 Bilangan Set Pelan : " . $item . "</strong>";
                break;
            case 110:
                $telegramMsg = "Muatnaik PSB Berjaya. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 111:
                $telegramMsg = "PSB telah diserahkan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 112:
                $telegramMsg = "";
                break;
            case 113:
                $telegramMsg = "";
                break;
            case 114:
                $telegramMsg = "";
                break;
            case 115:
                $telegramMsg = "";
                break;
            case 116:
                $telegramMsg = "";
                break;
            case 117:
                $telegramMsg = "";
                break;
            case 118:
                $telegramMsg = "Notis Siap Kerja telah dimuat naik. Sila muat naik Dokumen Permohonan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 119:
                $telegramMsg = "";
                break;
            case 120:
                $telegramMsg = "Dokumen Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Surat Permohonan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 121:
                $telegramMsg = "Surat Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 122:
                $telegramMsg = "Akuan Serahan Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Surat Kelulusan Permohonan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 123:
                $telegramMsg = "Surat Kelulusan Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Perakuan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 124:
                $telegramMsg = "";
                break;
            case 125:
                $telegramMsg = "Perakuan Sijil Siap Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 126:
                $telegramMsg = "";
                break;
            case 127:
                $telegramMsg = "";
                break;
            case 128:
                $telegramMsg = "";
                break;
            case 129:
                $telegramMsg = "";
                break;
            case 130:
                $telegramMsg = "";
                break;
            case 131:
                $telegramMsg = "";
                break;
            case 132:
                $telegramMsg = "";
                break;
            case 133:
                $telegramMsg = "";
                break;
            case 134:
                $telegramMsg = "";
                break;
            case 135:
                $telegramMsg = "";
                break;
            case 136:
                $telegramMsg = "Dokumen Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Surat Permohonan Sijil Siap Memperbaiki Kecacatan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 137:
                $telegramMsg = "Surat Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 138:
                $telegramMsg = "Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Ulasan Laporan Sijil Siap Memperbaiki Kecacatan. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 139:
                $telegramMsg = "Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Surat Permohonan Sijil Sempurna Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 140:
                $telegramMsg = "Surat Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Sempurna Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 141:
                $telegramMsg = "Akuan Serahan Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Kelulusan Sijil Sempurna Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 142:
                $telegramMsg = "Surat Kelulusan Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Perakuan Sijil Sempurna Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 143:
                $telegramMsg = "Perakuan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Sijil Sempurna Kerja. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋Pihak Berkuasa : ". $item. "</strong>";
                break;
            case 144:
                $telegramMsg = "Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja telah dimuat naik. Sila muat naik  Permohonan Pemulangan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 145:
                $telegramMsg = "";
                break;
            case 146:
                $telegramMsg = "Dokumen Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik Surat Permohonan Pemulangan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "</strong>";
                break;
            case 147:
                $telegramMsg = "Surat Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik  Akuan Serahan Permohonan Pemulangan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 148:
                $telegramMsg = "Akuan Serahan Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik Baucer Pemulangan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 149:
                $telegramMsg = "Baucer Pemulangan Wang Cagaran telah dimuat naik. \n\n<strong>🔗 No Rujukan : " . $rujukan . "\n 📋 Pihak Berkuasa : " . $item . "</strong>";
                break;
            case 150:
                $telegramMsg = "";
                break;
            case 151:
                $telegramMsg = "";
                break;
            case 152:
                $telegramMsg = "";
                break;
            case 153:
                $telegramMsg = "";
                break;
            case 154:
                $telegramMsg = "";
                break;
            default:
                // Default case if none of the above match
                $telegramMsg = "";
                break;
        }

        return $telegramMsg;
    }


    public function sendMessage($method, $variable, $message = NULL, $parseMode = 'markdownV2') {

        $url = "https://api.telegram.org/$this->token/sendMessage";

        if($method == 'assignee' || $method == 'user') {
            $ids = $this->getChatId($variable);
        } else if ($method == 'role') {
            $ids = $this->getChatIdByRole($variable);
        } else if($method == 'flow') {
            $ids = $this->getChatIdByFlow($variable);
        } else if($method == 'department') {
            $ids = $this->getChatIdByDepartment($variable);
        } else if ($method == 'group') {
            $ids = $this->getChatIdByGroup($variable);
        } else if($method == 'all') {
            $ids = $this->getAllChatId();
        }

        $responses = [];

        // Assuming $ids is either a single ID or an array of IDs
        $chatIds = is_array($ids) ? array_unique($ids) : [$ids];

        foreach ($chatIds as $id) {
            $data = [
                'chat_id' => $id,
                'text' => $message,
                'parse_mode' => $parseMode,
            ];

            $query = http_build_query($data);
            $result = $this->Curl->request($url, $query, contentType:'urlencoded');
            $responses[] = $this->Traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);
        }
        return $responses;

    }

    public function sendDocument($method, $variable, $documentPath, $replyMsgId, $caption = NULL, $notification = false){

        $url = "https://api.telegram.org/$this->token/sendDocument";

        if($method == 'assignee' || $method == 'user') {
            $ids = $this->getChatId($variable);
        } else if ($method == 'role') {
            $ids = $this->getChatIdByRole($variable);
        } else if($method == 'flow') {
            $ids = $this->getChatIdByFlow($variable);
        } else if($method == 'department') {
            $ids = $this->getChatIdByDepartment($variable);
        } else if ($method == 'group') {
            $ids = $this->getChatIdByGroup($variable);
        } else if($method == 'all') {
            $ids = $this->getAllChatId();
        }

        $responses = [];

        // Assuming $ids is either a single ID or an array of IDs
        $chatIds = is_array($ids) ? array_unique($ids) : [$ids];

        foreach ($chatIds as $id) {
            $data = [
                'chat_id' => $id,
                'document' => $documentPath,
                'caption' => $caption,
                'disable_notification' => $notification,
                'reply_to_message_id' => $replyMsgId,
            ];

            $query = http_build_query($data);

            $result = $this->Curl->request($url, $query, contentType: 'urlencoded');
            $responses[] = $this->Traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);
        }

        return $responses;

    }

    public function sendPhoto($method, $variable, $photoPath, $replyMsgId, $caption = NULL, $notification = false){

        $url = "https://api.telegram.org/$this->token/sendPhoto";

        if($method == 'assignee' || $method == 'user') {
            $ids = $this->getChatId($variable);
        } else if ($method == 'role') {
            $ids = $this->getChatIdByRole($variable);
        } else if($method == 'flow') {
            $ids = $this->getChatIdByFlow($variable);
        } else if($method == 'department') {
            $ids = $this->getChatIdByDepartment($variable);
        } else if ($method == 'group') {
            $ids = $this->getChatIdByGroup($variable);
        } else if($method == 'all') {
            $ids = $this->getAllChatId();
        }

        $responses = [];

        // Assuming $ids is either a single ID or an array of IDs
        $chatIds = is_array($ids) ? array_unique($ids) : [$ids];

        foreach ($chatIds as $id) {
            $data = [
                'chat_id' => $id,
                'photo' => $photoPath,
                'caption' => $caption,
                'disable_notification' => $notification,
                'reply_to_message_id' => $replyMsgId,
            ];

            $query = http_build_query($data);

            $result = $this->Curl->request($url, $query, contentType: 'urlencoded');
            $responses[] = $this->Traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);
        }

        return $responses;
    }

    public function sendSticker($method, $variable, $stickerId){

        $url = "https://api.telegram.org/$this->token/sendSticker";

        if($method == 'assignee' || $method == 'user') {
            $ids = $this->getChatId($variable);
        } else if ($method == 'role') {
            $ids = $this->getChatIdByRole($variable);
        } else if($method == 'flow') {
            $ids = $this->getChatIdByFlow($variable);
        } else if($method == 'department') {
            $ids = $this->getChatIdByDepartment($variable);
        } else if ($method == 'group') {
            $ids = $this->getChatIdByGroup($variable);
        } else if($method == 'all') {
            $ids = $this->getAllChatId();
        }

        $responses = [];

        // Assuming $ids is either a single ID or an array of IDs
        $chatIds = is_array($ids) ? array_unique($ids) : [$ids];

        foreach ($chatIds as $id) {
            $data = [
                'chat_id' => $id,
                'sticker' => $stickerId,
            ];

            $query = http_build_query($data);

            $result = $this->Curl->request($url, $query);
            $responses[] = $this->Traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);

        }

        return $responses;
    }
}