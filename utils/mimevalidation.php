<?php
/**
 * mimevalidation.php
 *
 * @package Hasarius
 * @category utils
 * @author Takahiro Kotake
 * @license Teleios Development
 */

namespace Hasarius\utils;

/**
 * MIME判定ユーティリティ
 *
 * @package hasarius
 * @category utility
 * @author tkotake
 */
class MimeValidation {

    /**
     * MIME 定義リスト
     * @var array
     */
    private static $mimeList = [
        "application" => [
            "a" => [
                "acad",
                "activemessage",
                "andrew-inset",
                "applefile",
                "arj",
                "astound",
                "asx",
                "atomicmail",
            ],
            "b" => [
                "bld",
                "bld2",
            ],
            "c" => [
                "cals-1840",
                "ccv",
                "clariscad",
                "commonground",
                "cprplayer",
                "cybercash",
            ],
            "d" => [
                "dca-rft",
                "dec-dx",
                "directry",
                "drafting",
                "dsptype",
                "dxf",
            ],
            "E" => [
                "EDI-consent",
                "EDIFACT",
            ],
            "e" => [
                "editor",
            ],
            "E" => [
                "EDI-X12",
            ],
            "e" => [
                "eshop",
                "excel",
            ],
            "f" => [
                "fastman",
                "file-mirror-list",
                "font-tdpfr",
                "fractals",
                "futuresplash",
                "futuresplash",
            ],
            "g" => [
                "gcwin",
                "gzip",
            ],
            "h" => [
                "hta",
            ],
            "i" => [
                "i-deas",
                "idp",
                "ie",
                "iges",
            ],
            "j" => [
                "java-archive",
                "java-vm",
                "jwc",
                "jxw",
            ],
            "l" => [
                "lgh",
                "lha",
                "listenup",
            ],
            "m" => [
                "mac-binhex40",
                "mac-compactpro",
                "macwriteii",
                "marc",
                "marche",
                "mathematica",
                "mbedlet",
                "metastream",
                "msaccess",
                "msexcel",
                "mspowerpoint",
                "ms-tnef",
                "msword",
                "mswrite",
            ],
            "n" => [
                "naplps",
                "naplps-audio",
                "news-message-id",
                "news-transmission",
            ],
            "o" => [
                "octet-stream",
                "oda",
            ],
            "p" => [
                "pbautomation",
                "pdf",
                "pgp",
                "pgp-encrypted",
                "pgp-keys",
                "pgp-signature",
                "photobubble",
                "pics-labels",
                "pics-rules",
                "pics-service",
                "pkcs10",
                "pkcs7-mime",
                "pkcs7-signature",
                "pkix-cert",
                "pkix-crl",
                "postscript",
                "postscript",
                "pot",
                "powerpoint",
                "pps",
                "ppt",
                "pre-encrypted",
                "presentations",
                "pro_eng",
            ],
            "r" => [
                "remote-printing",
                "riscos",
                "rtf",
            ],
            "s" => [
                "sdp",
                "set",
                "set-payment",
                "set-payment-initiation",
                "set-registration",
                "set-registration-initiation",
                "sgml-open-catalog",
                "sla",
                "slate",
                "smil",
                "sns",
                "solids",
            ],
            "S" => [
                "STEP",
            ],
            "s" => [
                "streamingmedia",
                "studiom",
            ],
            "T" => [
                "TestFontStream",
            ],
            "t" => [
                "timbuktu",
                "toc",
                "t-time",
            ],
            "u" => [
                "uwi_bin",
                "uwi_form",
                "uwi_nothing",
            ],
            "v" => [
                "vda",
                "vemmi",
                "vnd.adobe.pdf",
                "vnd.adobe.pdfxml",
                "vnd.adobe.xdp+xml",
                "vnd.adobe.xfd+xml",
                "vnd.adobe.xfdf",
                "vnd.adobe.x-mars",
                "vnd.fdf",
                "vnd.fujixerox.docuworks",
                "vnd.lotus-1-2-3",
                "vnd.lotus-approach",
                "vnd.lotus-freelance",
                "vnd.lotus-organizer",
                "vnd.lotus-screencam",
                "vnd.lotus-wordpro",
                "vnd.ms-access",
                "vnd.ms-artgalry",
                "vnd.ms-excel",
                "vnd.ms-pki.certstore",
                "vnd.ms-pki.pko",
                "vnd.ms-pki.seccat",
                "vnd.ms-pki.stl",
                "vnd.ms-powerpoint",
                "vnd.ms-project",
                "vnd.ms-schedule",
                "vnd.rn-realmedia",
                "vnd.rn-realplayer",
                "vnd.rn-realsystem-rjs",
                "vnd.rn-realsystem-rmj",
                "vnd.rn-realsystem-rmx",
                "vnd.rn-rn_music_package",
                "vnd.roland-rns0",
                "vocaltec-ips",
                "vocaltec-media-desc",
                "vocaltec-media-file",
            ],
            "w" => [
                "wcz",
                "winhlp",
                "wita",
                "wordperfect5.1",
            ],
            "x" => [
                "x400-bp",
                "x-adobeaamdetect",
                "x-aim",
                "x-alternatiff",
                "x-anm",
                "x-arc",
                "x-asap",
                "x-att-a2bmusic",
                "x-att-a2bmusic-purchase",
                "x-autherware-map",
                "x-bananacad",
                "x-bcpio",
                "x-cabri2",
                "x-caramel",
                "x-cdf",
                "x-cdlink",
                "x-cmx",
                "x-cnc",
                "x-cnet-vsl",
                "x-cocoa",
                "x-compress",
                "x-conference",
                "x-cpio",
                "x-cprplayer",
                "x-csh",
                "x-d96",
                "x-director",
                "x-dot",
                "x-dvi",
                "x-earthtime",
                "x-envoy",
                "x-excel",
                "x-exe",
                "x-expandedbook",
                "x-fortezza-ckl",
                "x-gcwin",
                "x-google-chrome-pdf",
                "x-go-sgf",
                "x-gps",
                "x-gsp",
                "x-gtar",
                "x-gzip",
                "x-hdf",
                "x-httpd-cgi",
                "x-icq",
                "x-idp",
                "x-internet-signup",
                "x-iphone",
                "x-ipix",
                "x-ipscript",
                "x-java-applet",
                "x-java-bean",
                "x-javascript",
                "x-javascript-config",
                "x-js-forum-post",
                "x-js-hana",
                "x-js-homepage-post",
                "x-js-inforunner",
                "x-js-jxw",
                "x-js-news",
                "x-js-sns",
                "x-js-taro",
                "x-klaunch",
                "x-Koan",
                "x-laplayer-reg",
                "x-latex",
                "xlc",
                "x-lha",
                "x-lha-compressed",
                "x-lk-rlestream",
                "x-lzh",
                "x-macbinary",
                "x-maker",
                "x-mapserver",
                "x-mascot",
                "x-midi",
                "x-mif",
                "xml",
                "x-mocha",
                "x-mplayer2",
                "x-msaccess",
                "x-mscardfile",
                "x-msclip",
                "x-msdownload",
                "x-msexcel",
                "x-msmediaview",
                "x-msmetafile",
                "x-msmoney",
                "x-mspublisher",
                "x-msschedule",
                "x-msterminal",
                "x-mswrite",
                "x-nacl",
                "x-netcdf",
                "x-netfpx",
                "x-NET-Install",
                "x-nif",
                "x-ns-proxy-autoconfig",
                "x-nvi",
                "x-nyp",
                "x-pcn-connection",
                "x-pcvan",
                "x-pdf",
                "x-perl",
                "x-pkcs12",
                "x-pkcs7-certificates",
                "x-pkcs7-certreqresp",
                "x-pkcs7-crl",
                "x-pkcs7-mime",
                "x-pkcs7-signature",
                "x-pnacl",
                "x-pn-npistream",
                "x-pointplus",
                "x-postpet",
                "x-qplus",
                "x-rar-compressed",
                "x-rasmol",
                "x-richlink",
                "x-rtsl",
                "x-rtsp",
                "x-salsa",
                "x-sch",
                "x-SCREAM",
                "x-sdp",
                "x-sh",
                "x-shar",
                "x-sharepoint",
                "x-sharepoint-protocolhandler",
                "x-shockwave-flash",
                "x-sprite",
                "x-spt",
                "x-stuffit",
                "x-sv4cpio",
                "x-sv4crc",
                "x-tar",
                "x-tcl",
                "x-tex",
                "x-texinfo",
                "x-timbuktu",
                "x-tkined",
                "x-trendjavascan-plugin",
                "x-troff",
                "x-troff-man",
                "x-troff-me",
                "x-troff-ms",
                "x-twinvq",
                "x-unknown-content-type-LhasaArchive",
                "x-unknown-content-type-vsc88.mid",
                "x-ustar",
                "x-uuencode",
                "x-vcon-command",
                "x-vcon-data",
                "x-wacomtabletplugin",
                "x-wais-source",
                "x-winhelp",
                "x-world",
                "x-worldgroup",
                "x-www-form-encoded",
                "x-www-form-urlencoded",
                "x-www-pem-reply",
                "x-www-pem-request",
                "x-www-pgm-reply",
                "x-www-pgm-request",
                "x-x509-ca-cert",
                "x-xdma",
                "x-xfdl",
                "x-yz1",
                "x-zaurus-zac",
                "x-zip-compressed",
            ],
            "z" => [
                "zip",
            ],
        ],
        "audio" => [
            "3" => [
                "32kadpcm",
            ],
            "a" => [
                "aiff",
            ],
            "b" => [
                "basic",
            ],
            "e" => [
                "echospeech",
            ],
            "m" => [
                "mid",
                "midi",
                "mp3",
                "mpeg",
                "mpegurl",
                "mpg",
            ],
            "n" => [
                "nspaudio",
            ],
            "r" => [
                "rmf",
            ],
            "t" => [
                "tsplayer",
            ],
            "v" => [
                "vnd.qcelp",
                "vnd.rn-realaudio",
                "voxware",
            ],
            "w" => [
                "wav",
            ],
            "x" => [
                "x-aiff",
                "x-dspeech",
                "x-epac",
                "x-karaoke",
                "x-liquid",
                "x-liquid-file",
                "x-liquid-secure",
                "x-liveaudio",
                "x-mid",
                "x-midi",
                "x-mio",
                "x-mp3",
                "x-mpeg",
                "x-mpegurl",
                "x-mpg",
                "x-ms-wma",
                "x-nspaudio",
                "x-pac",
                "x-pn-aiff",
                "x-pn-au",
                "x-pn-realaudio",
                "x-pn-realaudio-plugin",
                "x-pn-wav",
                "x-pn-windows-acm",
                "x-pn-windows-pcm",
                "x-realaudio",
                "x-rmf",
                "x-scpls",
                "x-sd2",
                "x-twinvq",
                "x-twinvq-plugin",
                "x-wav",
            ],
        ],
        "binary" => [
            "l" => [
                "lzh",
            ],
        ],
        "chemical" => [
            "x" => [
                "x-csml",
                "x-embl-dl-nucleotide",
                "x-gaussian-cube",
                "x-gaussian-input",
                "x-jcamp-dx",
                "x-mdl-molfile",
                "x-mdl-rxnfile",
                "x-mdl-tgf",
                "x-mopac-input",
                "x-pdb",
                "x-spt",
                "x-xyz",
            ],
        ],
        "drawing" => [
            "x" => [
                "x-dwf",
            ],
        ],
        "image" => [
            "b" => [
                "bmp",
            ],
            "c" => [
                "cgm",
                "cis-cod",
            ],
            "f" => [
                "fif",
            ],
            "g" => [
                "g3fax",
                "gif",
            ],
            "i" => [
                "ief",
                "ifs",
                "imagn",
            ],
            "j" => [
                "jpeg",
            ],
            "n" => [
                "naplps",
            ],
            "p" => [
                "pict",
                "pjpeg",
                "png",
            ],
            "r" => [
                "rast",
            ],
            "s" => [
                "svh",
            ],
            "t" => [
                "tiff",
            ],
            "v" => [
                "vasa",
                "vnd",
                "vnd.dwg",
                "vnd.dxf",
                "vnd.fpx",
                "vnd.net-fpx",
                "vnd.rn-realflash",
                "vnd.rn-realpix",
                "vnd.svf",
                "vnd.xiff",
            ],
            "w" => [
                "wavelet",
            ],
            "x" => [
                "x-bmp",
                "x-cals",
                "x-cmu-raster",
                "x-dcx",
                "x-dxf",
                "x-fpx",
                "x-freehand",
                "x-gzip",
                "x-icon",
                "x-jg",
                "x-macpaint",
                "x-MS-bmp",
                "x-pcx",
                "x-photo-cd",
                "x-photoshop",
                "x-pict",
                "x-png",
                "x-portable-anymap",
                "x-portable-bitmap",
                "x-portable-graymap",
                "x-portable-pixmap",
                "x-quicktime",
                "x-rgb",
                "x-sgi",
                "x-targa",
                "x-tiff",
                "x-xbitmap",
                "x-xbm",
                "x-xpixmap",
                "x-xwindowdump",
            ],
        ],
        "internal" => [
            "d" => [
                "draft",
            ],
        ],
        "i-world" => [
            "i" => [
                "i-vrml",
            ],
        ],
        "magnus-internal" => [
            "c" => [
                "cgi",
            ],
            "h" => [
                "headers",
            ],
            "i" => [
                "imagemap",
            ],
            "p" => [
                "parsed-html",
            ],
        ],
        "manual-action" => [
            "M" => [
                "MDN-sent-manually",
            ],
        ],
        "message" => [
            "d" => [
                "delivery-status",
                "disposition-notification",
            ],
            "e" => [
                "external-body",
            ],
            "h" => [
                "html",
            ],
            "n" => [
                "news",
            ],
            "p" => [
                "partial",
            ],
            "r" => [
                "rfc822",
            ],
        ],
        "model" => [
            "i" => [
                "iges",
            ],
            "m" => [
                "mesh",
            ],
            "v" => [
                "vrml",
            ],
        ],
        "multipart" => [
            "a" => [
                "alternative",
                "appledouble",
            ],
            "b" => [
                "byteranges",
            ],
            "d" => [
                "digest",
            ],
            "e" => [
                "encrypted",
            ],
            "f" => [
                "form-data",
            ],
            "h" => [
                "header-set",
            ],
            "m" => [
                "mixed",
            ],
            "p" => [
                "parallel",
            ],
            "r" => [
                "related",
                "report",
            ],
            "s" => [
                "signed",
            ],
            "v" => [
                "voice-message",
            ],
            "x" => [
                "x-gzip",
                "x-mixed-replace",
                "x-zip",
            ],
        ],
        "music" => [
            "c" => [
                "crescendo",
                "crescendo-encrypted",
            ],
            "x" => [
                "x-crescendo-encrypted",
            ],
        ],
        "Netscape" => [
            "S" => [
                "Source",
            ],
            "T" => [
                "Telnet",
            ],
            "t" => [
                "tn3270",
            ],
        ],
        "plugin" => [
            "t" => [
                "talker",
            ],
        ],
        "Security" => [
            "R" => [
                "Remote-Passphrase",
            ],
        ],
        "text" => [
            "a" => [
                "act",
            ],
            "c" => [
                "c",
                "cas",
                "cmif",
                "comma-separated-values",
                "css",
            ],
            "d" => [
                "directory",
                "download",
                "dsssl",
            ],
            "e" => [
                "enriched",
            ],
            "h" => [
                "h323",
                "html",
            ],
            "i" => [
                "iuls",
            ],
            "j" => [
                "javascript",
                "js",
            ],
            "m" => [
                "mathml",
                "mathml-renderer",
                "mathml-rendererB",
            ],
            "p" => [
                "pdf",
                "plain",
            ],
            "r" => [
                "rfc822-headers",
                "richtext",
                "rtf",
            ],
            "s" => [
                "scriptlet",
                "sgml",
                "smil-basic",
                "smil-basic-layout",
            ],
            "t" => [
                "tab-separated-values",
                "tcl",
            ],
            "v" => [
                "vbscript",
                "vcard",
                "vnd.latex-z",
                "vnd.rn-realtext",
            ],
            "w" => [
                "webviewhtml",
            ],
            "x" => [
                "x-component",
                "x-hdml",
                "x-imagemap",
                "xml",
                "x-mrm",
                "x-mrml",
                "x-nif",
                "x-rat",
                "x-server-parsed-html",
                "x-server-parsed-html3",
                "x-setext",
                "x-sgml",
                "xsl",
                "x-speech",
                "x-vcalender",
                "x-vcard",
            ],
        ],
        "unknown" => [
            "u" => [
                "unknown",
            ],
        ],
        "video" => [
            "a" => [
                "avi",
                "avs",
            ],
            "f" => [
                "flc",
            ],
            "i" => [
                "isivideo",
            ],
            "m" => [
                "mpeg",
                "mpg",
                "msvideo",
            ],
            "o" => [
                "olivr",
            ],
            "q" => [
                "quicktime",
            ],
            "v" => [
                "vdo",
                "vivo",
                "vnd.motorola.video",
                "vnd.motorola.videop",
                "vnd.rn-realvideo",
                "vnd.vivo",
            ],
            "w" => [
                "wavelet",
            ],
            "x" => [
                "x-dv",
                "x-fli",
                "x-ivf",
                "x-la-asf",
                "x-mpeg",
                "x-mpeg2",
                "x-mpeg2a",
                "x-ms-asf",
                "x-ms-asf-plugin",
                "x-msvideo",
                "x-qtc",
                "x-sgi-movie",
            ],
        ],
        "workbook" => [
            "f" => [
                "formulaone",
            ],
        ],
        "www" => [
            "u" => [
                "unknown",
            ],
        ],
        "x-application" => [
            "f" => [
                "file-mirror-list",
            ],
        ],
        "x-conference" => [
            "x" => [
                "x-cooltalk",
            ],
        ],
        "x-lml" => [
            "x" => [
                "x-evm",
                "x-gdb",
                "x-gps",
                "x-lak",
                "x-lml",
                "x-lmlpack",
                "x-ndb",
            ],
        ],
        "x-music" => [
            "x" => [
                "x-midi",
            ],
        ],
        "X-PmailDX" => [
            "X" => [
                "X-BANDAI",
            ],
        ],
        "x-world" => [
            "x" => [
                "x-svr",
                "x-vream",
                "x-vrml",
                "x-vrt",
            ],
        ],
    ];

    /**
     * MIME判定
     * @param  string $mime MIME文字列
     * @return bool         MIMEの場合は真を、そうでない場合は偽を返す
     */
    public static function validatieMime(string $mime): bool
    {
        list($key, $value) = explode('/', trim($mime));
        if (!array_key_exists($key, self::$mimeList)) {
            return false;
        }
        $firstChar = substr($value, 0, 1);
        if (!array_key_exists($firstChar, self::$mimeList[$key])) {
            return false;
        }
        return in_array($value, self::$mimeList[$key][$firstChar]);
    }
}
