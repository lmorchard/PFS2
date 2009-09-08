<?php
$data = array();

$fx_guid = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';

$mimes_map = create_function('$a', '
    return array_combine(
        array("name","description","suffixes"), $a
    );
');

$data[] = array (
    'meta' => array (
        'name' => 'Adobe Flash Player',
        'vendor' => 'Adobe',
        'version' => '10.0.22.87',
        'installer_shows_ui' => 'false',
        'url' => 'http://www.adobe.com/go/getflashplayer',
        'manual_installation_url' => 'http://www.adobe.com/go/getflashplayer',
        'license_url' => 'http://www.adobe.com/go/eula_flashplayer',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array (
        array (
            'name' => 'application/x-shockwave-flash',
            'description' => 'Shockwave Flash',
            'suffixes' => 'swf',
        ),
        array (
            'name' => 'application/futuresplash',
            'description' => 'FutureSplash Player',
            'suffixes' => 'spl',
        ),
    ),
    'releases' => array (
        array (
            'guid' => '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}',
            'os_name' => 'windows nt 6.0',
        ),
        array (
            'guid' => '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{7a646d7b-0202-4491-9151-cf66fa0722b2}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-linux.xpi',
            'os_name' => 'linux',
        ),
        array (
            'guid' => '{89977581-9028-4be0-b151-7c4f9bcd3211}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi',
            'os_name' => 'mac',
        ),
        array (
            'guid' => '{0ae66efd-e183-431a-ab51-3af2c278a3dd}',
            'xpi_location' => 
                'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi',
            'os_name' => 'sunos sun4u',
        ),
        array (
            'guid' => '{0ae66efd-e183-431a-ab51-3af2c278a3dd}',
            'xpi_location' => 
                'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi',
            'os_name' => 'sunos',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Adobe Flash Player',
        'vendor' => 'Adobe',
        'version' => '10.0.22.87',
        'installer_shows_ui' => 'false',
        'url' => 'http://www.adobe.com/go/getflashplayer',
        'manual_installation_url' => 'http://www.adobe.com/go/getflashplayer',
        'license_url' => 'http://www.adobe.com/go/eula_flashplayer_jp',
        'platform' => array( 'app_id' => $fx_guid, 'locale' => 'ja-JP' ),
    ),
    'mimes' => array (
        array (
            'name' => 'application/x-shockwave-flash',
            'description' => 'Shockwave Flash',
            'suffixes' => 'swf',
        ),
        array (
            'name' => 'application/futuresplash',
            'description' => 'FutureSplash Player',
            'suffixes' => 'spl',
        ),
    ),
    'releases' => array (
        array (
            'guid' => '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}',
            'os_name' => 'windows nt 6.0',
        ),
        array (
            'guid' => '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{7a646d7b-0202-4491-9151-cf66fa0722b2}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-linux.xpi',
            'os_name' => 'linux',
        ),
        array (
            'guid' => '{89977581-9028-4be0-b151-7c4f9bcd3211}',
            'xpi_location' => 
                'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-mac.xpi',
            'os_name' => 'mac',
        ),
        array (
            'guid' => '{0ae66efd-e183-431a-ab51-3af2c278a3dd}',
            'xpi_location' => 
                'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi',
            'os_name' => 'sunos sun4u',
        ),
        array (
            'guid' => '{0ae66efd-e183-431a-ab51-3af2c278a3dd}',
            'xpi_location' => 
                'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi',
            'os_name' => 'sunos',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Adobe Shockwave Player',
        'vendor' => 'Adobe',
        'version' => '10.1',
        'url' => 'http://www.adobe.com/go/getshockwave/',
        'manual_installation_url' => 'http://www.adobe.com/go/getshockwave/',
        'license_url' => 'http://www.adobe.com/go/eula_shockwaveplayer',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array (
        array (
            'name' => 'application/x-director',
            'description' => 'Adobe Shockwave Player',
            'suffixes' => 'dcr,dxr',
        ),
    ),
    'releases' => array (
        array (
            'guid' => '{45f2a22c-4029-4209-8b3d-1421b989633f}',
            'xpi_location' => 
                'https://www.macromedia.com/go/xpi_shockwaveplayer_win',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{49141640-b629-4d57-a539-b521c4a99eff}',
            'xpi_location' => 
                'https://www.macromedia.com/go/xpi_shockwaveplayer_macosx',
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Adobe Shockwave Player',
        'vendor' => 'Adobe',
        'version' => '10.1',
        'icon_url' => '???',
        'url' => 'http://www.adobe.com/go/getshockwave/',
        'manual_installation_url' => 'http://www.adobe.com/go/getshockwave/',
        'license_url' => 'http://www.adobe.com/go/eula_shockwaveplayer_jp',
        'platform' => array( 'app_id' => $fx_guid, 'locale' => 'ja-JP' ),
    ),
    'mimes' => array (
        array (
            'name' => 'application/x-director',
            'description' => 'Adobe Shockwave Player',
            'suffixes' => 'dcr,dxr',
        ),
    ),
    'releases' => array (
        array (
            'guid' => '{45f2a22c-4029-4209-8b3d-1421b989633f}',
            'xpi_location' => 
                'https://www.macromedia.com/go/xpi_shockwaveplayerj_win',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{49141640-b629-4d57-a539-b521c4a99eff}',
            'xpi_location' => 
                'https://www.macromedia.com/go/xpi_shockwaveplayerj_macosx',
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Real Player',
        'vendor' => 'Real Networks',
        'version' => '10.5',
        'url' => 'http://www.real.com',
        'manual_installation_url' => 'http://www.real.com/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array (
        array (
            'name' => 'audio/x-pn-realaudio-plugin/',
            'description' => '',
            'suffixes' => '',
        ),
        array (
            'name' => 'audio/x-pn-realaudio',
            'description' => '',
            'suffixes' => '',
        ),
    ),
    'releases' => array (
        array (
            'guid' => '{d586351c-cb55-41a7-8e7b-4aaac5172d39}',
            'xpi_location' => 
                'http://forms.real.com/real/player/download.html?type=firefox',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{269eb771-59de-4702-9209-ca97ce522f6d}',
            'os_name' => '*',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Apple Quicktime',
        'vendor' => 'Apple',
        'url' => 'http://www.apple.com/quicktime/download/',
        'manual_installation_url' => 'http://www.apple.com/quicktime/download/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/sdp', 'SDP stream descriptor', 'sdp'),
        array('application/x-sdp', 'SDP stream descriptor', 'sdp'),
        array('application/x-rtsp', 'RTSP stream descriptor', 'rtsp,rts'),
        array('video/quicktime', 'QuickTime Movie', 'mov,qt,mqv'),
        array('video/flc', 'AutoDesk Animator', 'flc,fli,cel'),
        array('audio/x-wav', 'WAVE audio', 'wav,bwf'),
        array('audio/wav', 'WAVE audio', 'wav,bwf'),
        array('audio/aiff', 'AIFF audio', 'aiff,aif,aifc,cdda'),
        array('audio/x-aiff', 'AIFF audio', 'aiff,aif,aifc,cdda'),
        array('audio/basic', 'uLaw/AU audio', 'au,snd,ulw'),
        array('audio/mid', 'MIDI', 'mid,midi,smf,kar'),
        array('audio/x-midi', 'MIDI', 'mid,midi,smf,kar'),
        array('audio/midi', 'MIDI', 'mid,midi,smf,kar'),
        array('audio/vnd.qcelp', 'QUALCOMM PureVoice audio', 'qcp'),
        array('audio/x-gsm', 'GSM audio', 'gsm'),
        array('audio/AMR', 'AMR audio', 'AMR'),
        array('audio/aac', 'AAC audio', 'aac,adts'),
        array('audio/x-aac', 'AAC audio', 'aac,adts'),
        array('audio/x-caf', 'CAF audio', 'caf'),
        array('audio/ac3', 'AC3 audio', 'ac3'),
        array('audio/x-ac3', 'AC3 audio', 'ac3'),
        array('audio/vnd.qcelp', 'QUALCOMM PureVoice audio', 'qcp'),
        array('video/x-mpeg', 'MPEG media', 'mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),
        array('video/mpeg', 'MPEG media', 'mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),
        array('audio/mpeg', 'MPEG audio', 'mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),
        array('audio/x-mpeg', 'MPEG audio', 'mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),
        array('video/3gpp', '3GPP media', '3gp,3gpp'),
        array('audio/3gpp', '3GPP media', '3gp,3gpp'),
        array('video/3gpp2', '3GPP2 media', '3g2,3gp2'),
        array('audio/3gpp2', '3GPP2 media', '3g2,3gp2'),
        array('video/sd-video', 'SD video', 'sdv'),
        array('application/x-mpeg', 'AMC media', 'amc'),
        array('video/mp4', 'MPEG-4 media', 'mp4'),
        array('audio/mp4', 'MPEG-4 media', 'mp4'),
        array('audio/x-m4a', 'AAC audio', 'm4a'),
        array('audio/x-m4p', 'AAC audio', 'm4p'),
        array('audio/x-m4b', 'AAC audio book', 'm4b'),
        array('video/x-m4v', 'Video', 'm4v'),
        array('audio/mpeg', 'MP3 audio', 'mp3,swa'),
        array('audio/x-mpeg', 'MP3 audio', 'mp3,swa'),
        array('audio/mp3', 'MP3 audio', 'mp3,swa'),
        array('audio/x-mp3', 'MP3 audio', 'mp3,swa'),
        array('audio/mpeg3', 'MP3 audio', 'mp3,swa'),
        array('audio/x-mpeg3', 'MP3 audio', 'mp3,swa'),
        array('image/x-bmp', 'BMP image', 'bmp,dib'),
        array('image/x-macpaint', 'MacPaint image', 'pntg,pnt,mac'),
        array('image/pict', 'PICT image', 'pict,pic,pct'),
        array('image/x-pict', 'PICT image', 'pict,pic,pct'),
        array('image/png', 'PNG image', 'png'),
        array('image/x-png', 'PNG image', 'png'),
        array('image/x-quicktime', 'QuickTime image', 'qtif,qti'),
        array('image/x-sgi', 'SGI image', 'sgi,rgb'),
        array('image/x-targa', 'TGA image', 'targa,tga'),
        array('image/tiff', 'TIFF image', 'tif,tiff'),
        array('image/x-tiff', 'TIFF image', 'tif,tiff'),
        array('image/jp2', 'JPEG2000 image', 'jp2'),
        array('image/jpeg2000', 'JPEG2000 image', 'jp2'),
        array('image/jpeg2000-image', 'JPEG2000 image', 'jp2'),
        array('image/x-jpeg2000-image', 'JPEG2000 image', 'jp2'),
    )),
    'releases' => array (
        array (
            'guid' => '{a42bb825-7eee-420f-8ee7-834062b6fefd}',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{a42bb825-7eee-420f-8ee7-834062b6fefd}',
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Java Runtime Environment',
        'vendor' => 'Sun Microsystems',
        'url' => 'http://java.com/firefoxjre',
        'manual_installation_url' => 'http://java.com/firefoxjre',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/x-java-vm', '', ''),
        array('application/x-java-applet;jpi-version=1.5', '', ''),
        array('application/x-java-bean;jpi-version=1.5', '', ''),
        array('application/x-java-applet;version=1.3', '', ''),
        array('application/x-java-bean;version=1.3', '', ''),
        array('application/x-java-applet;version=1.2.2', '', ''),
        array('application/x-java-bean;version=1.2.2', '', ''),
        array('application/x-java-applet;version=1.2.1', '', ''),
        array('application/x-java-bean;version=1.2.1', '', ''),
        array('application/x-java-applet;version=1.4.2', '', ''),
        array('application/x-java-bean;version=1.4.2', '', ''),
        array('application/x-java-applet;version=1.5', '', ''),
        array('application/x-java-bean;version=1.5', '', ''),
        array('application/x-java-applet;version=1.3.1', '', ''),
        array('application/x-java-bean;version=1.3.1', '', ''),
        array('application/x-java-applet;version=1.4', '', ''),
        array('application/x-java-bean;version=1.4', '', ''),
        array('application/x-java-applet;version=1.4.1', '', ''),
        array('application/x-java-bean;version=1.4.1', '', ''),
        array('application/x-java-applet;version=1.2', '', ''),
        array('application/x-java-bean;version=1.2', '', ''),
        array('application/x-java-applet;version=1.1.3', '', ''),
        array('application/x-java-bean;version=1.1.3', '', ''),
        array('application/x-java-applet;version=1.1.2', '', ''),
        array('application/x-java-bean;version=1.1.2', '', ''),
        array('application/x-java-applet;version=1.1.1', '', ''),
        array('application/x-java-bean;version=1.1.1', '', ''),
        array('application/x-java-applet;version=1.1', '', ''),
        array('application/x-java-bean;version=1.1', '', ''),
        array('application/x-java-applet', '', ''),
        array('application/x-java-bean', '', '')
    )),
    'releases' => array (
        array (
            'guid' => '{fbe640ef-4375-4f45-8d79-767d60bf75b8}',
            'installer_location' => 'http://java.com/firefoxjre_exe',
            'installer_hash' => 'sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a',
            'needs_restart' => 'false',
            'os_name' => 'windows nt 6.0',
        ),
        array (
            'guid' => '{92a550f2-dfd2-4d2f-a35d-a98cfda73595}',
            'installer_location' => 'http://java.com/firefoxjre_exe',
            'installer_hash' => 'sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a',
            'xpi_location' => 'http://java.com/jre-install.xpi',
            'os_name' => 'win',
        ),
        array (
            'guid' => '{fbe640ef-4375-4f45-8d79-767d60bf75b8}',
            'os_name' => '*',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Adobe Acrobat Plug-In',
        'vendor' => 'Adobe',
        'url' => 'http://www.adobe.com/products/acrobat/readstep.html',
        'manual_installation_url' => 
            'http://www.adobe.com/products/acrobat/readstep.html',
        'guid' => '{d87cd824-67cb-4547-8587-616c70318095}',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/pdf', '', ''),
        array('application/vnd.fdf', '', ''),
        array('application/vnd.adobe.xfdf', '', ''),
        array('application/vnd.adobe.xdp+xml', '', ''),
        array('application/vnd.adobe.xfd+xml', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
        array (
            'os_name' => 'mac',
        ),
        array (
            'os_name' => 'linux',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Viewpoint Media Player',
        'vendor' => 'Viewpoint',
        'url' => 'http://www.viewpoint.com/pub/products/vmp.html',
        'manual_installation_url' => 
            'http://www.viewpoint.com/pub/products/vmp.html',
        'guid' => '{03f998b2-0e00-11d3-a498-00104b6eb52e}',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/x-mtx', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
        array (
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'Windows Media Player',
        'vendor' => 'Microsoft',
        'url' => 'http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx',
        'manual_installation_url' => 
            'http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx',
        'guid' => '{cff1240a-fd24-4b9f-8183-ccd96e5300d0}',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/asx', '', ''),
        array('application/x-mplayer2', '', ''),
        array('audio/x-ms-wax', '', ''),
        array('audio/x-ms-wma', '', ''),
        array('video/x-ms-asf', '', ''),
        array('video/x-ms-asf-plugin', '', ''),
        array('video/x-ms-wm', '', ''),
        array('video/x-ms-wmp', '', ''),
        array('video/x-ms-wmv', '', ''),
        array('video/x-ms-wmx', '', ''),
        array('video/x-ms-wvx', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
        array (
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'XStandard XHTML WYSIWYG Editor',
        'guid' => '{3563d917-2f44-4e05-8769-47e655e92361}',
        'icon_url' => 'http://xstandard.com/images/xicon32x32.gif',
        'xpi_location' => 'http://xstandard.com/download/xstandard.xpi',
        'installer_shows_ui' => 'false',
        'manual_installation_url' => 'http://xstandard.com/download/',
        'license_url' => 'http://xstandard.com/license/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/x-xstandard', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
        array (
            'os_name' => 'mac',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'DNL Reader',
        'guid' => '{ce9317a3-e2f8-49b9-9b3b-a7fb5ec55161}',
        'version' => '5.5',
        'icon_url' => 'http://digitalwebbooks.com/reader/dwb16.gif',
        'xpi_location' => 'http://digitalwebbooks.com/reader/xpinst.xpi',
        'installer_shows_ui' => 'false',
        'manual_installation_url' => 'http://digitalwebbooks.com/reader/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/x-dnl', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'VideoEgg Publisher',
        'guid' => '{b8b881f0-2e07-11db-a98b-0800200c9a66}',
        'icon_url' => 'http://videoegg.com/favicon.ico',
        'xpi_location' => 
            'http://update.videoegg.com/Install/Windows/Initial/VideoEggPublisher.xpi',
        'installer_shows_ui' => 'true',
        'manual_installation_url' => 'http://www.videoegg.com/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('application/x-videoegg-loader', '', ''),
    )),
    'releases' => array (
        array (
            'os_name' => 'win',
        ),
    ),
);

$data[] = array (
    'meta' => array (
        'name' => 'DivX Web Player',
        'guid' => '{a8b771f0-2e07-11db-a98b-0800200c9a66}',
        'icon_url' => 'http://images.divx.com/divx/player/webplayer.png',
        'installer_shows_ui' => 'false',
        'license_url' => 'http://go.divx.com/plugin/license/',
        'manual_installation_url' => 'http://go.divx.com/plugin/download/',
        'platform' => array( 'app_id' => $fx_guid ),
    ),
    'mimes' => array_map($mimes_map, array (
        array('video/divx', '', ''),
    )),
    'releases' => array (
        array (
            'xpi_location' => 'http://download.divx.com/player/DivXWebPlayer.xpi',
            'os_name' => 'win',
        ),
        array (
            'xpi_location' => 'http://download.divx.com/player/DivXWebPlayerMac.xpi',
            'os_name' => 'mac',
        ),
    ),
);

foreach ($data as $idx=>$plugin) {
    $fn = $idx . '-' .$plugin['meta']['name'];
    $fn = str_replace(' ', '-', strtolower($fn));
    $plugin['meta']['pfs_id'] = $fn;
    $data[$idx] = $plugin;
    file_put_contents($fn.'.json', json_encode($plugin));
}
