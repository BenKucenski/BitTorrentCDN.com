<?php require_once 'index.code.php'; ?>

<html>
<head>
    <title>BitTorrentCDN.com: Distributed Internet</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
          integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>

<div class="row">
    <div class="col-xs-12" style="margin-left: 15px;">
        <img src="/images/logo.png" style="width: 200px;"/>
    </div>
</div>
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">Generate Torrent (12MB Max)</div>
            <div class="card-body">
                <form action="/" method="post" enctype="multipart/form-data">
                    Select media to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload Image" name="submit">
                </form>
            </div>
        </div>

        <?php if (isset($_SESSION['torrent_link'])) { ?>

            <div class="card">
                <div class="card-header">Generated Torrent Links</div>
                <div class="card-body">
                    <ul>
                        <li>Magnet Link: <a
                                    href="<?php echo $_SESSION['magnet_link']; ?>"><?php echo $_SESSION['magnet_link']; ?></a>
                        </li>
                        <li>Torrent Link: <a
                                    href="<?php echo $_SESSION['torrent_link']; ?>"><?php echo $_SESSION['torrent_link']; ?></a>
                        </li>
                        <li>Direct Link: <a
                                    href="<?php echo $_SESSION['direct_link']; ?>"><?php echo $_SESSION['direct_link']; ?></a>
                        </li>
                    </ul>
                </div>
            </div>


        <?php } ?>



        <?php echo $html; ?>

    </div>
    <div class="col-4">
        <div class="card">
            <div class="card-header">Step 1: Download a BitTorrent Client</div>
            <div class="card-body">
                <ul>
                    <li><a href="http://www.utorrent.com/">http://www.utorrent.com/</a> - Free for Windows, Mac, Linux
                    </li>
                    <li><a href="https://www.qbittorrent.org/">https://www.qbittorrent.org/</a> - Free for Windows, Mac,
                        Linux
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Step 2: Upload a File</div>
            <div class="card-body">
                BitTorrentCDN.com is intended to push files that you want publicly available to the distributed BitTorrent network.
                SHA512 is used to generate a signature of the file and the signature is then exclusively used for the file name.
                If the SHA512 of the file does not match the name of the file then the file has been manipulated and should be deleted
                immediately.  BitTorrentCDN.com will not serve BitTorrent files which are not named to match their SHA512 hash.
            </div>
        </div>

        <div class="card">
            <div class="card-header">Step 3: Download the Torrent</div>
            <div class="card-body">
                After you upload a file, you will get a link to the Torrent file and the Magnet URL.  Use these to download your file into your local
                BitTorrent Client.  Now you're part of the network that will provide this file to end users.  Share this torrent with your friends so they
                will help keep your files available to the public.
            </div>
        </div>

        <div class="card">
            <div class="card-header">Step 4: Share the Direct Link</div>
            <div class="card-body">
                Share the direct link on Social Media.  BitTorrentCDN.com will find your file on the distributed network and serve it directly to the
                end user.
            </div>
        </div>

        <div class="card text-white bg-danger">
            <div class="card-header">Privacy Notice</div>
            <div class="card-body">
                BitTorrentCDN.com is not about privacy, it is about removing centralized control of your media.  Do not post illegal content.
                BitTorrentCDN.com regularly deletes files from its BitTorrent node.  You must share the Torrent or your files will eventually
                disappear.
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div style="text-align: center">
            <a href="https://benkucenski.github.io/BitTorrentCDN.com/">https://benkucenski.github.io/BitTorrentCDN.com/</a>
        </div>
    </div>
</div>
</body>
</html>
