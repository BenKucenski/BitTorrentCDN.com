<?php
session_start();

// https://raw.githubusercontent.com/adriengibrat/torrent-rw/
require_once 'Torrent.php';

class index
{
    public static function Debug($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        exit;
    }

    public function Get()
    {
        $t = $_SERVER['REQUEST_URI'];
        if (!strlen($t)) {
            return;
        }

        if ($t[0] == '/') {
            $t = substr($t, 1, strlen($t) - 1);
        }
        if (strlen($t) != 128) {
            return;
        }

        if (!is_dir('downloads')) {
            mkdir('downloads');
        }

        if (file_exists('downloads/' . $t)) {
            $this->Present('downloads/' . $t);
        }

        // https://github.com/aria2/aria2/releases/tag/release-1.33.1
        // https://aria2.github.io/
        $torrent_url = 'http://bittorrentcdn.com/torrents/' . $t[0] . '/' . $t[1] . '/' . $t . '.torrent';
        exec('aria2c --seed-time=0 -d"downloads" ' . $torrent_url);

        $it = new RecursiveDirectoryIterator('downloads');
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (stristr($file->getFilename(), $t) !== false) {
                if (stristr($file->getFilename(), '.torrent') !== false) {
                    unlink($file->getPathname());
                    continue;
                }
                $hash = hash_file('sha512', $file->getPathname());

                if ($hash !== $t) {
                    unlink($file->getPathname());
                    continue;
                }

                $c = explode('.', $file->getFilename());
                rename($file->getPathname(), 'downloads/' . $c[0]);

            }
        }
        $this->Present('downloads/' . $t);
    }

    public function Present($file)
    {
        if (!file_exists($file)) {
            return;
        }

        // http://php.net/manual/en/function.exif-imagetype.php
        // https://stackoverflow.com/questions/1851849/output-an-image-in-php
        if (exif_imagetype($file)) {
            $size = getimagesize($file);
            $fp = fopen($file, 'rb');

            if ($size && $fp) {
                header('Content-Type: ' . $size['mime']);
                header('Content-Length: ' . filesize($file));
                fpassthru($fp);
                exit;
            }
        }
    }

    public function Post()
    {
        // 12 meg limit
        if ($_FILES['fileToUpload']['error'] || !$_FILES['fileToUpload']['size'] || !$_FILES['fileToUpload']['size'] > 1024 * 1024 * 12) {
            header('location: /');
            exit;
        }

        $hash = hash_file('sha512', $_FILES['fileToUpload']['tmp_name']);
        $dir0 = $hash[0];
        $dir1 = $hash[1];

        $info = pathinfo($_FILES['fileToUpload']['name']);

        if (!is_dir('logs')) {
            mkdir('logs');
        }

        $fp = fopen('logs/' . $hash . '.txt','w');
        fwrite($fp, print_r($_REQUEST, true) . PHP_EOL . PHP_EOL);
        fwrite($fp, print_r($_SERVER, true) . PHP_EOL . PHP_EOL);
        fclose($fp);

        if (!is_dir('files')) {
            mkdir('files');
        }

        if (!is_dir('files/' . $dir0)) {
            mkdir('files/' . $dir0);
        }
        if (!is_dir('files/' . $dir0 . '/' . $dir1)) {
            mkdir('files/' . $dir0 . '/' . $dir1);
        }


        if (!is_dir('torrents')) {
            mkdir('torrents');
        }

        if (!is_dir('torrents/' . $dir0)) {
            mkdir('torrents/' . $dir0);
        }
        if (!is_dir('torrents/' . $dir0 . '/' . $dir1)) {
            mkdir('torrents/' . $dir0 . '/' . $dir1);
        }

        if (!is_dir('torrent_server')) {
            mkdir('torrent_server');
        }

        $dest = 'files/' . $dir0 . '/' . $dir1 . '/' . $hash . '.' . (isset($info['extension']) ? $info['extension'] : 'txt');
        $dest_torrent = 'torrents/' . $dir0 . '/' . $dir1 . '/' . $hash . '.torrent';
        $dest_server = 'torrent_server/' . $hash . '.torrent';
        move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dest);


        $local_dest = 'http://bittorrentcdn.com/' . $dest;
        // exit($local_dest);

        $torrent = new Torrent($local_dest);
        $torrent->save($dest_torrent);
        $torrent->name($hash . '.' . (isset($info['extension']) ? $info['extension'] : 'txt'));
        $torrent->is_private(false);
        $torrent->announce([
            'http://bittorrentcdn.com:8080/announce',
        ]);
        $torrent->url_list('http://bittorrentcdn.com/' . $dest_torrent);
        $data = $torrent->encode($torrent);

        $fp = fopen($dest_server, 'w');
        fwrite($fp, $data);
        fclose($fp);

        if ($errors = $torrent->errors()) {
            self::Debug($errors);
        }

        //$torrent = new Torrent($dest_torrent);

        $_SESSION['torrent_link'] = 'http://bittorrentcdn.com/' . $dest_torrent;
        $_SESSION['magnet_link'] = $torrent->magnet(false);
        $_SESSION['direct_link'] = 'http://bittorrentcdn.com/' . $hash;
        header('location: /');
        exit();
    }
}

ob_start();

$index = new index();
switch(strtolower($_SERVER['REQUEST_METHOD'])) {
    case 'get':
        $index->get();
        break;
    case 'post':
        $index->post();
        break;
}

$html = ob_get_clean();