--
-- 
--

SET FOREIGN_KEY_CHECKS=0;

-- Set up known OSes
INSERT INTO `oses` (`name`) VALUES
    ('*'),
    ('win'),
    ('windows nt 6.0'),
    ('mac os x'),
    ('ppc mac os x'),
    ('intel mac os x'),
    ('linux'),
    ('linux x86'),
    ('linux x86_64'),
    ('sunos'),
    ('sunos sun4u');

SET @any_os = ( SELECT `id` FROM `oses` WHERE `name`='*');
SET @win =    ( SELECT `id` FROM `oses` WHERE `name`='win');
SET @linux =  ( SELECT `id` FROM `oses` WHERE `name`='linux');
SET @mac =    ( SELECT `id` FROM `oses` WHERE `name`='mac os x');

-- Set up known platforms
SET @fx_guid = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';

INSERT INTO `platforms` (`app_id`, `app_release`, `app_version`, `locale`) VALUES
    ('*', '*', '*', '*'),
    (@fx_guid, '*', '*', '*'),
    (@fx_guid, '*', '*', 'ja-JP'),
    (@fx_guid, '3.0', '*', '*');

SET @any_platform_id = (
    SELECT `id` FROM `platforms` WHERE `app_id`='*'
);

SET @any_fx_platform_id = (
    SELECT `id` FROM `platforms` WHERE 
        `app_id`=@fx_guid AND 
        `app_release`='*' AND `app_version`='*' AND `locale`='*'
);

--
-- Adobe Flash Player plugin
--

SET @name = 
    'Adobe Flash Player';
SET @vendor =
    'Adobe';
SET @version = 
    '10.0.22.87';
SET @icon_url =
    '???';
SET @url = 
    'http://www.adobe.com/go/getflashplayer';
SET @license_url = 
    'http://www.adobe.com/go/eula_flashplayer';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Flash for WinNT 6.0
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}', @version, '', '', '',
        'false', @url, '', 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` 
    (`plugin_release_id`,`os_id`) 
    VALUES
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='windows nt 6.0'));

-- Flash for Windows in general
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}', @version,
        'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- Flash for Linux
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{7a646d7b-0202-4491-9151-cf66fa0722b2}', @version,
        'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @linux);

-- Flash for Solaris Sparc
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{0ae66efd-e183-431a-ab51-3af2c278a3dd}', @version,
        'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES 
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='sunos sun4u'));

-- Flash for Solaris x86
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{0ae66efd-e183-431a-ab51-3af2c278a3dd}', @version,
        'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES 
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='sunos'));

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-shockwave-flash', 'Shockwave Flash', 'swf'),
    ('application/futuresplash', 'FutureSplash Player', 'spl');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/x-shockwave-flash', 'application/futuresplash'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @any_fx_platform_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Adobe Flash Player plugin (ja-JP)
--

SET @name = 
    'Adobe Flash Player';
SET @vendor =
    'Adobe';
SET @version = 
    '10.0.22.87';
SET @icon_url =
    '???';
SET @url = 
    'http://www.adobe.com/go/getflashplayer';
SET @license_url = 
    'http://www.adobe.com/go/eula_flashplayer_jp';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Flash for WinNT 6.0
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}', @version, '', '',
        '', 'false', @url, '', 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` 
    (`plugin_release_id`,`os_id`) 
    VALUES
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='windows nt 6.0'));

-- Flash for Windows in general
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{4cfaef8a-a6c9-41a0-8e6f-967eb8f49143}', @version,
        'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- Flash for Linux
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{7a646d7b-0202-4491-9151-cf66fa0722b2}', @version,
        'http://fpdownload.macromedia.com/get/flashplayer/xpi/current/flashplayer-win.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @linux);

-- Flash for Solaris Sparc
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{0ae66efd-e183-431a-ab51-3af2c278a3dd}', @version,
        'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-sparc.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES 
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='sunos sun4u'));

-- Flash for Solaris x86
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{0ae66efd-e183-431a-ab51-3af2c278a3dd}', @version,
        'http://download.macromedia.com/pub/flashplayer/xpi/current/flashplayer-solaris-x86.xpi',
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES 
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='sunos'));

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-shockwave-flash', 'Shockwave Flash', 'swf'),
    ('application/futuresplash', 'FutureSplash Player', 'spl');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/x-shockwave-flash', 'application/futuresplash'
    );

-- Associate all the releases with the appropriate platform
SET @fx_jp_id = (
    SELECT `id` FROM `platforms` WHERE 
        `app_id`=@fx_guid AND 
        `app_release`='*' AND `app_version`='*' AND 
        `locale`='ja-JP'
);
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @fx_jp_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Adobe / Macromedia Shockwave plugin
--

SET @name = 
    'Adobe Shockwave Player';
SET @vendor =
    'Adobe';
SET @version = 
    '10.1';
SET @icon_url =
    '???';
SET @url = 
    'http://www.adobe.com/go/getshockwave';
SET @license_url = 
    'http://www.adobe.com/go/eula_shockwaveplayer';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Shockwave for Windows
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{45f2a22c-4029-4209-8b3d-1421b989633f}', @version, 
        'https://www.macromedia.com/go/xpi_shockwaveplayer_win', 
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- Shockwave for Mac
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{49141640-b629-4d57-a539-b521c4a99eff}', @version, 
        'https://www.macromedia.com/go/xpi_shockwaveplayer_macosx', 
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @mac);

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-director', 'Adobe Shockwave Player', 'dcr,dxr');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/x-director'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @any_fx_platform_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Adobe / Macromedia Shockwave plugin (jp-JP)
--

SET @name = 
    'Adobe Shockwave Player';
SET @vendor =
    'Adobe';
SET @version = 
    '10.1';
SET @icon_url =
    '???';
SET @url = 
    'http://www.adobe.com/go/getshockwave';
SET @license_url = 
    'http://www.adobe.com/go/eula_shockwaveplayer_jp';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Shockwave for Windows
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{45f2a22c-4029-4209-8b3d-1421b989633f}', @version, 
        'https://www.macromedia.com/go/xpi_shockwaveplayerj_win', 
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- Shockwave for Mac
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{49141640-b629-4d57-a539-b521c4a99eff}', @version, 
        'https://www.macromedia.com/go/xpi_shockwaveplayerj_macosx', 
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @mac);

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-director', 'Adobe Shockwave Player', 'dcr,dxr');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/x-director'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @fx_jp_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Real Player
--

SET @name = 
    'Real Player';
SET @vendor =
    'Real Networks';
SET @version = 
    '10.5';
SET @icon_url =
    '???';
SET @url = 
    'http://www.real.com';
SET @license_url = 
    '';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Real Player for Windows
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{d586351c-cb55-41a7-8e7b-4aaac5172d39}', @version, 
        'http://forms.real.com/real/player/download.html?type=firefox', 
        '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- Real Player for Non-Windows
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{269eb771-59de-4702-9209-ca97ce522f6d}', @version, 
        '', '', '', 'false', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @any_os);

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('audio/x-pn-realaudio-plugin', '', ''),
    ('audio/x-pn-realaudio', '', '');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'audio/x-pn-realaudio-plugin',
        'audio/x-pn-realaudio'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @any_fx_platform_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Quicktime
--

SET @name = 
    'Apple Quicktime';
SET @vendor =
    'Apple';
SET @version = 
    '';
SET @icon_url =
    '???';
SET @url = 
    'http://www.apple.com/quicktime/download/';
SET @license_url = 
    '';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- Quicktime for Windows & Mac
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{a42bb825-7eee-420f-8ee7-834062b6fefd}', @version, 
        '', '', '', 'false', @url, @license_url, 'true', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);
INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @mac);

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/sdp', 'SDP stream descriptor', 'sdp'),
    ('application/x-sdp', 'SDP stream descriptor', 'sdp'),
    ('application/x-rtsp', 'RTSP stream descriptor', 'rtsp,rts'),
    ('video/quicktime', 'QuickTime Movie', 'mov,qt,mqv'),
    ('video/flc', 'AutoDesk Animator', 'flc,fli,cel'),
    ('audio/x-wav', 'WAVE audio', 'wav,bwf'),
    ('audio/wav', 'WAVE audio', 'wav,bwf'),
    ('audio/aiff', 'AIFF audio', 'aiff,aif,aifc,cdda'),
    ('audio/x-aiff', 'AIFF audio', 'aiff,aif,aifc,cdda'),
    ('audio/basic', 'uLaw/AU audio', 'au,snd,ulw'),
    ('audio/mid', 'MIDI', 'mid,midi,smf,kar'),
    ('audio/x-midi', 'MIDI', 'mid,midi,smf,kar'),
    ('audio/midi', 'MIDI', 'mid,midi,smf,kar'),
    ('audio/vnd.qcelp', 'QUALCOMM PureVoice audio', 'qcp'),
    ('audio/x-gsm', 'GSM audio', 'gsm'),
    ('audio/AMR', 'AMR audio', 'AMR'),
    ('audio/aac', 'AAC audio', 'aac,adts'),
    ('audio/x-aac', 'AAC audio', 'aac,adts'),
    ('audio/x-caf', 'CAF audio', 'caf'),
    ('audio/ac3', 'AC3 audio', 'ac3'),
    ('audio/x-ac3', 'AC3 audio', 'ac3'),
    ('audio/vnd.qcelp', 'QUALCOMM PureVoice audio', 'qcp'),
    ('video/x-mpeg', 'MPEG media', 'mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),
    ('video/mpeg', 'MPEG media', 'mpeg,mpg,m1s,m1v,m1a,m75,m15,mp2,mpm,mpv,mpa'),
    ('audio/mpeg', 'MPEG audio', 'mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),
    ('audio/x-mpeg', 'MPEG audio', 'mpeg,mpg,m1s,m1a,mp2,mpm,mpa,m2a'),
    ('video/3gpp', '3GPP media', '3gp,3gpp'),
    ('audio/3gpp', '3GPP media', '3gp,3gpp'),
    ('video/3gpp2', '3GPP2 media', '3g2,3gp2'),
    ('audio/3gpp2', '3GPP2 media', '3g2,3gp2'),
    ('video/sd-video', 'SD video', 'sdv'),
    ('application/x-mpeg', 'AMC media', 'amc'),
    ('video/mp4', 'MPEG-4 media', 'mp4'),
    ('audio/mp4', 'MPEG-4 media', 'mp4'),
    ('audio/x-m4a', 'AAC audio', 'm4a'),
    ('audio/x-m4p', 'AAC audio', 'm4p'),
    ('audio/x-m4b', 'AAC audio book', 'm4b'),
    ('video/x-m4v', 'Video', 'm4v'),
    ('audio/mpeg', 'MP3 audio', 'mp3,swa'),
    ('audio/x-mpeg', 'MP3 audio', 'mp3,swa'),
    ('audio/mp3', 'MP3 audio', 'mp3,swa'),
    ('audio/x-mp3', 'MP3 audio', 'mp3,swa'),
    ('audio/mpeg3', 'MP3 audio', 'mp3,swa'),
    ('audio/x-mpeg3', 'MP3 audio', 'mp3,swa'),
    ('image/x-bmp', 'BMP image', 'bmp,dib'),
    ('image/x-macpaint', 'MacPaint image', 'pntg,pnt,mac'),
    ('image/pict', 'PICT image', 'pict,pic,pct'),
    ('image/x-pict', 'PICT image', 'pict,pic,pct'),
    ('image/png', 'PNG image', 'png'),
    ('image/x-png', 'PNG image', 'png'),
    ('image/x-quicktime', 'QuickTime image', 'qtif,qti'),
    ('image/x-sgi', 'SGI image', 'sgi,rgb'),
    ('image/x-targa', 'TGA image', 'targa,tga'),
    ('image/tiff', 'TIFF image', 'tif,tiff'),
    ('image/x-tiff', 'TIFF image', 'tif,tiff'),
    ('image/jp2', 'JPEG2000 image', 'jp2'),
    ('image/jpeg2000', 'JPEG2000 image', 'jp2'),
    ('image/jpeg2000-image', 'JPEG2000 image', 'jp2'),
    ('image/x-jpeg2000-image', 'JPEG2000 image', 'jp2');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/sdp', 'application/x-sdp', 'application/x-rtsp',
        'video/quicktime', 'video/flc', 'audio/x-wav', 'audio/wav',
        'audio/aiff', 'audio/x-aiff', 'audio/basic', 'audio/mid',
        'audio/x-midi', 'audio/midi', 'audio/vnd.qcelp', 'audio/x-gsm',
        'audio/AMR', 'audio/aac', 'audio/x-aac', 'audio/x-caf',
        'audio/ac3', 'audio/x-ac3', 'audio/vnd.qcelp', 'video/x-mpeg',
        'video/mpeg', 'audio/mpeg', 'audio/x-mpeg', 'video/3gpp',
        'audio/3gpp', 'video/3gpp2', 'audio/3gpp2', 'video/sd-video',
        'application/x-mpeg', 'video/mp4', 'audio/mp4', 'audio/x-m4a',
        'audio/x-m4p', 'audio/x-m4b', 'video/x-m4v', 'audio/mpeg',
        'audio/x-mpeg', 'audio/mp3', 'audio/x-mp3', 'audio/mpeg3',
        'audio/x-mpeg3', 'image/x-bmp', 'image/x-macpaint', 'image/pict',
        'image/x-pict', 'image/png', 'image/x-png', 'image/x-quicktime',
        'image/x-sgi', 'image/x-targa', 'image/tiff', 'image/x-tiff',
        'image/jp2', 'image/jpeg2000', 'image/jpeg2000-image',
        'image/x-jpeg2000-image'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @any_fx_platform_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
-- Java
--

SET @name = 
    'Java Runtime Environment';
SET @vendor =
    'Sun Microsystems';
SET @version = 
    '';
SET @icon_url =
    '???';
SET @url = 
    'http://java.com/firefoxjre';
SET @license_url = 
    '';

INSERT INTO plugins 
    (`name`, `description`, `latest_version`, `vendor`, `url`, `icon_url`, 
        `license_url`) 
    VALUES
    (@name, @vendor, @version, @vendor, @url, @icon_url, @license_url);
SET @p_id = last_insert_id();

-- JRE for Windows
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{92a550f2-dfd2-4d2f-a35d-a98cfda73595}', @version, 
        'http://java.com/jre-install.xpi',
        'http://java.com/firefoxjre_exe', 
        'sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a', 
        'true', @url, @license_url, 'true', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @win);

-- JRE for Windows NT 6.0
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{fbe640ef-4375-4f45-8d79-767d60bf75b8}', @version, '',
        'http://java.com/firefoxjre_exe', 
        'sha1:89a78d34a36d7e25cc32b1a507a2cd6fb87dd40a', 
        'true', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` 
    (`plugin_release_id`,`os_id`) 
    VALUES
    (@pr_id, (SELECT `id` FROM `oses` WHERE `name`='windows nt 6.0'));

-- JRE for other
INSERT INTO `plugin_releases` 
    (`plugin_id`, `guid`, `version`, `xpi_location`,
        `installer_location`, `installer_hash`, `installer_shows_ui`,
        `manual_installation_url`, `license_url`, `needs_restart`,
        `min`, `max`, `xpcomabi`) 
    VALUES
    (@p_id, '{fbe640ef-4375-4f45-8d79-767d60bf75b8}', @version, '',
        '', '', 'true', @url, @license_url, 'false', '', '', '');
SET @pr_id = last_insert_id();

INSERT INTO `plugin_releases_oses` (`plugin_release_id`,`os_id`) 
    VALUES (@pr_id, @any_os);

-- Set up MIME types for this plugin
INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-java-vm', '', ''),
    ('application/x-java-applet;jpi-version=1.5', '', ''),
    ('application/x-java-bean;jpi-version=1.5', '', ''),
    ('application/x-java-applet;version=1.3', '', ''),
    ('application/x-java-bean;version=1.3', '', ''),
    ('application/x-java-applet;version=1.2.2', '', ''),
    ('application/x-java-bean;version=1.2.2', '', ''),
    ('application/x-java-applet;version=1.2.1', '', ''),
    ('application/x-java-bean;version=1.2.1', '', ''),
    ('application/x-java-applet;version=1.4.2', '', ''),
    ('application/x-java-bean;version=1.4.2', '', ''),
    ('application/x-java-applet;version=1.5', '', ''),
    ('application/x-java-bean;version=1.5', '', ''),
    ('application/x-java-applet;version=1.3.1', '', ''),
    ('application/x-java-bean;version=1.3.1', '', ''),
    ('application/x-java-applet;version=1.4', '', ''),
    ('application/x-java-bean;version=1.4', '', ''),
    ('application/x-java-applet;version=1.4.1', '', ''),
    ('application/x-java-bean;version=1.4.1', '', ''),
    ('application/x-java-applet;version=1.2', '', ''),
    ('application/x-java-bean;version=1.2', '', ''),
    ('application/x-java-applet;version=1.1.3', '', ''),
    ('application/x-java-bean;version=1.1.3', '', ''),
    ('application/x-java-applet;version=1.1.2', '', ''),
    ('application/x-java-bean;version=1.1.2', '', ''),
    ('application/x-java-applet;version=1.1.1', '', ''),
    ('application/x-java-bean;version=1.1.1', '', ''),
    ('application/x-java-applet;version=1.1', '', ''),
    ('application/x-java-bean;version=1.1', '', ''),
    ('application/x-java-applet', '', ''),
    ('application/x-java-bean', '', '');

-- Associate all the releases with the appropriate MIME types
INSERT INTO `plugins_mimes` (`plugin_id`,`mime_id`) 
    SELECT @p_id, `mimes`.`id` 
    FROM `mimes` 
    WHERE `mimes`.`name` IN ( 
        'application/x-java-vm',
        'application/x-java-applet;jpi-version=1.5',
        'application/x-java-bean;jpi-version=1.5',
        'application/x-java-applet;version=1.3',
        'application/x-java-bean;version=1.3',
        'application/x-java-applet;version=1.2.2',
        'application/x-java-bean;version=1.2.2',
        'application/x-java-applet;version=1.2.1',
        'application/x-java-bean;version=1.2.1',
        'application/x-java-applet;version=1.4.2',
        'application/x-java-bean;version=1.4.2',
        'application/x-java-applet;version=1.5',
        'application/x-java-bean;version=1.5',
        'application/x-java-applet;version=1.3.1',
        'application/x-java-bean;version=1.3.1',
        'application/x-java-applet;version=1.4',
        'application/x-java-bean;version=1.4',
        'application/x-java-applet;version=1.4.1',
        'application/x-java-bean;version=1.4.1',
        'application/x-java-applet;version=1.2',
        'application/x-java-bean;version=1.2',
        'application/x-java-applet;version=1.1.3',
        'application/x-java-bean;version=1.1.3',
        'application/x-java-applet;version=1.1.2',
        'application/x-java-bean;version=1.1.2',
        'application/x-java-applet;version=1.1.1',
        'application/x-java-bean;version=1.1.1',
        'application/x-java-applet;version=1.1',
        'application/x-java-bean;version=1.1',
        'application/x-java-applet',
        'application/x-java-bean'
    );

-- Associate all the releases with the appropriate platform
INSERT INTO `plugin_releases_platforms` (`plugin_release_id`,`platform_id`) 
    SELECT `id`, @any_fx_platform_id FROM `plugin_releases` WHERE `plugin_id`=@p_id;

--
--
--

INSERT INTO `mimes` (`name`, `description`, `suffixes`) VALUES
    ('application/x-silverlight', 'Microsoft Silverlight', 'xaml'),
    ('application/x-silverlight-2', 'Microsoft Silverlight', 'xaml'),
    ('video/x-msvideo', 'Video For Windows', 'avi,vfw'),
    ('video/msvideo', 'Video For Windows', 'avi,vfw'),
    ('video/avi', 'Video For Windows', 'avi,vfw'),
    ('application/x-mplayer2', 'Windows Media Plugin', ''),
    ('video/x-ms-asf-plugin', 'Windows Media Plugin', ''),
    ('application/asx', 'Windows Media Plugin', ''),
    ('video/x-ms-asf', 'Windows Media Video', 'asf'),
    ('video/x-ms-asx', 'Windows Media Playlist', 'asx'),
    ('video/x-ms-wmv', 'Windows Media Video', 'wmv'),
    ('video/x-ms-wvx', 'Windows Media Playlist', 'wvx'),
    ('audio/x-ms-wma', 'Windows Media Audio', 'wma'),
    ('audio/x-ms-wax', 'Windows Media Playlist', 'wax'),
    ('video/x-ms-wm', 'Windows Media Video', 'wm'),
    ('video/x-ms-wmp', 'Windows Media Video', 'wmp'),
    ('video/x-ms-wmx', 'Windows Media Playlist', 'wmx'),
    ('application/x-director', '', ''),
    ('application/pdf', '', ''),
    ('application/vnd.fdf', '', ''),
    ('application/vnd.adobe.xfdf', '', ''),
    ('application/vnd.adobe.xdp+xml', '', ''),
    ('application/vnd.adobe.xfd+xml', '', ''),
    ('application/x-mtx', '', ''),
    ('application/x-xstandard', '', ''),
    ('application/x-dnl', '', ''),
    ('application/x-videoegg-loader', '', ''),
    ('video/divx', '', '');

-- Update the timestamps on the releases
UPDATE `plugin_releases` SET `created`=now(), `modified`=now();

