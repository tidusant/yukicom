<?php

namespace MyCommon;

class Common {

    

   

    /**
     * Creat list of call log status
     */
    public static function listCountryFlag(){
        return array(
            "af"=>"za",
            "ak"=>"gh",
            "am"=>"et",
            "ar"=>"ar",
            "as"=>"in",
            "ay"=>"bo",
            "az"=>"az",
            "be"=>"by",
            "bg"=>"bg",
            "bn"=>"in",
            "br"=>"fr",
            "bs"=>"ba",
            "ca"=>"es",
            "cb"=>"iq",
            "ck"=>"us",
            "co"=>"fr",
            "cs"=>"cz",
            "cx"=>"ph",
            "cy"=>"gb",
            "da"=>"dk",
            "de"=>"de",
            "el"=>"gr",
            "en"=>"gb",            
            "eo"=>"eo",
            "es"=>"cl",
            
            "es"=>"es",
           
            "et"=>"ee",
            "eu"=>"es",
            "fa"=>"ir",
            "fb"=>"lt",
            "ff"=>"ng",
            "fi"=>"fi",
            "fo"=>"fo",
            
            "fr"=>"fr",
            "fy"=>"nl",
            "ga"=>"ie",
            "gl"=>"es",
            "gn"=>"py",
            "gu"=>"in",
            "gx"=>"gr",
            "ha"=>"ng",
            "he"=>"il",
            "hi"=>"in",
            "hr"=>"hr",
            "ht"=>"ht",
            "hu"=>"hu",
            "hy"=>"am",
            "id"=>"id",
            "ig"=>"ng",
            "is"=>"is",
            "it"=>"it",
            "ja"=>"jp",
            
            "jv"=>"id",
            "ka"=>"ge",
            "kk"=>"kz",
            "km"=>"kh",
            "kn"=>"in",
            "ko"=>"kr",
            "ku"=>"tr",
            "ky"=>"kg",
            "la"=>"va",
            "lg"=>"ug",
            "li"=>"nl",
            "ln"=>"cd",
            "lo"=>"la",
            "lt"=>"lt",
            "lv"=>"lv",
            "mg"=>"mg",
            "mi"=>"nz",
            "mk"=>"mk",
            "ml"=>"in",
            "mn"=>"mn",
            "mr"=>"in",
            "ms"=>"my",
            "mt"=>"mt",
            "my"=>"mm",
            "nb"=>"no",
            "nd"=>"zw",
            "ne"=>"np",
            "nl"=>"nl",
            "nn"=>"no",
            "ny"=>"mw",
            "or"=>"in",
            "pa"=>"in",
            "pl"=>"pl",
            "ps"=>"af",
          
            "pt"=>"pt",
            "qc"=>"gt",
            "qu"=>"pe",
            "rm"=>"ch",
            "ro"=>"ro",
            "ru"=>"ru",
            "rw"=>"rw",
            "sa"=>"in",
            "sc"=>"it",
            "se"=>"no",
            "si"=>"lk",
            "sk"=>"sk",
            "sl"=>"si",
            "sn"=>"zw",
            "so"=>"so",
            "sq"=>"al",
            "sr"=>"rs",
            "sv"=>"se",
            "sw"=>"ke",
            "sy"=>"sy",
            "sz"=>"pl",
            "ta"=>"in",
            "te"=>"in",
            "tg"=>"tj",
            "th"=>"th",
            "tk"=>"tm",
            
            "tl"=>"st",
            "tr"=>"tr",
            "tt"=>"ru",
            "tz"=>"ma",
            "uk"=>"ua",
            "ur"=>"pk",
            "uz"=>"uz",
            "vi"=>"vn",
            "wo"=>"sn",
            "xh"=>"za",
            "yi"=>"de",
            "yo"=>"ng",
            "zh"=>"cn",
           
            "zu"=>"za",
            "zz"=>"tr"
            );
    }
    public static function listLocale(){
        return array(
            "af"=>"af_ZA",
            "ak"=>"ak_GH",
            "am"=>"am_ET",
            "ar"=>"ar_AR",
            "as"=>"as_IN",
            "ay"=>"ay_BO",
            "az"=>"az_AZ",
            "be"=>"be_BY",
            "bg"=>"bg_BG",
            "bn"=>"bn_IN",
            "br"=>"br_FR",
            "bs"=>"bs_BA",
            "ca"=>"ca_ES",
            "cb"=>"cb_IQ",
            "ck"=>"ck_US",
            "co"=>"co_FR",
            "cs"=>"cs_CZ",
            "cx"=>"cx_PH",
            "cy"=>"cy_GB",
            "da"=>"da_DK",
            "de"=>"de_DE",
            "el"=>"el_GR",
            "en"=>"en_GB",
            "eo"=>"eo_EO",
           
            "es"=>"es_ES",
            
            "et"=>"et_EE",
            "eu"=>"eu_ES",
            "fa"=>"fa_IR",
            "fb"=>"fb_LT",
            "ff"=>"ff_NG",
            "fi"=>"fi_FI",
            "fo"=>"fo_FO",
            "fr"=>"fr_FR",
            "fy"=>"fy_NL",
            "ga"=>"ga_IE",
            "gl"=>"gl_ES",
            "gn"=>"gn_PY",
            "gu"=>"gu_IN",
            "gx"=>"gx_GR",
            "ha"=>"ha_NG",
            "he"=>"he_IL",
            "hi"=>"hi_IN",
            "hr"=>"hr_HR",
            "ht"=>"ht_HT",
            "hu"=>"hu_HU",
            "hy"=>"hy_AM",
            "id"=>"id_ID",
            "ig"=>"ig_NG",
            "is"=>"is_IS",
            "it"=>"it_IT",
            "ja"=>"ja_JP",
            "jv"=>"jv_ID",
            "ka"=>"ka_GE",
            "kk"=>"kk_KZ",
            "km"=>"km_KH",
            "kn"=>"kn_IN",
            "ko"=>"ko_KR",
            "ku"=>"ku_TR",
            "ky"=>"ky_KG",
            "la"=>"la_VA",
            "lg"=>"lg_UG",
            "li"=>"li_NL",
            "ln"=>"ln_CD",
            "lo"=>"lo_LA",
            "lt"=>"lt_LT",
            "lv"=>"lv_LV",
            "mg"=>"mg_MG",
            "mi"=>"mi_NZ",
            "mk"=>"mk_MK",
            "ml"=>"ml_IN",
            "mn"=>"mn_MN",
            "mr"=>"mr_IN",
            "ms"=>"ms_MY",
            "mt"=>"mt_MT",
            "my"=>"my_MM",
            "nb"=>"nb_NO",
            "nd"=>"nd_ZW",
            "ne"=>"ne_NP",
          
            "nl"=>"nl_NL",
            "nn"=>"nn_NO",
            "ny"=>"ny_MW",
            "or"=>"or_IN",
            "pa"=>"pa_IN",
            "pl"=>"pl_PL",
            "ps"=>"ps_AF",            
            "pt"=>"pt_PT",
            "qc"=>"qc_GT",
            "qu"=>"qu_PE",
            "rm"=>"rm_CH",
            "ro"=>"ro_RO",
            "ru"=>"ru_RU",
            "rw"=>"rw_RW",
            "sa"=>"sa_IN",
            "sc"=>"sc_IT",
            "se"=>"se_NO",
            "si"=>"si_LK",
            "sk"=>"sk_SK",
            "sl"=>"sl_SI",
            "sn"=>"sn_ZW",
            "so"=>"so_SO",
            "sq"=>"sq_AL",
            "sr"=>"sr_RS",
            "sv"=>"sv_SE",
            "sw"=>"sw_KE",
            "sy"=>"sy_SY",
            "sz"=>"sz_PL",
            "ta"=>"ta_IN",
            "te"=>"te_IN",
            "tg"=>"tg_TJ",
            "th"=>"th_TH",
            "tk"=>"tk_TM",
           
            "tl"=>"tl_ST",
            "tr"=>"tr_TR",
            "tt"=>"tt_RU",
            "tz"=>"tz_MA",
            "uk"=>"uk_UA",
            "ur"=>"ur_PK",
            "uz"=>"uz_UZ",
            "vi"=>"vi_VN",
            "wo"=>"wo_SN",
            "xh"=>"xh_ZA",
            "yi"=>"yi_DE",
            "yo"=>"yo_NG",
            "zh"=>"zh_CN",
            "zu"=>"zu_ZA",
            "zz"=>"zz_TR"
            );
    }
    public static function listCountry(){
        return array(
            "af"=>"Afrikaans",
            "ak"=>"Akan",
            "am"=>"Amharic",
            "ar"=>"Arabic",
            "as"=>"Assamese",
            "ay"=>"Aymara",
            "az"=>"Azerbaijani",
            "be"=>"Belarusian",
            "bg"=>"Bulgarian",
            "bn"=>"Bengali",
            "br"=>"Breton",
            "bs"=>"Bosnian",
            "ca"=>"Catalan",
            "cb"=>"Sorani Kurdish",
            "ck"=>"Cherokee",
            "co"=>"Corsican",
            "cs"=>"Czech",
            "cx"=>"Cebuano",
            "cy"=>"Welsh",
            "da"=>"Danish",
            "de"=>"German",
            "el"=>"Greek",
            "en"=>"English (US)",
            "eo"=>"Esperanto",
            "es"=>"Spanish (Venezuela)",
            "et"=>"Estonian",
            "eu"=>"Basque",
            "fa"=>"Persian",
            "fb"=>"Leet Speak",
            "ff"=>"Fulah",
            "fi"=>"Finnish",
            "fo"=>"Faroese",
            "fr"=>"French",
            "fy"=>"Frisian",
            "ga"=>"Irish",
            "gl"=>"Galician",
            "gn"=>"Guarani",
            "gu"=>"Gujarati",
            "gx"=>"Classical Greek",
            "ha"=>"Hausa",
            "he"=>"Hebrew",
            "hi"=>"Hindi",
            "hr"=>"Croatian",
            "ht"=>"Haitian Creole",
            "hu"=>"Hungarian",
            "hy"=>"Armenian",
            "id"=>"Indonesian",
            "ig"=>"Igbo",
            "is"=>"Icelandic",
            "it"=>"Italian",
            "ja"=>"Japanese",
            "jv"=>"Javanese",
            "ka"=>"Georgian",
            "kk"=>"Kazakh",
            "km"=>"Khmer",
            "kn"=>"Kannada",
            "ko"=>"Korean",
            "ku"=>"Kurdish (Kurmanji)",
            "ky"=>"Kyrgyz",
            "la"=>"Latin",
            "lg"=>"Ganda",
            "li"=>"Limburgish",
            "ln"=>"Lingala",
            "lo"=>"Lao",
            "lt"=>"Lithuanian",
            "lv"=>"Latvian",
            "mg"=>"Malagasy",
            "mi"=>"Māori",
            "mk"=>"Macedonian",
            "ml"=>"Malayalam",
            "mn"=>"Mongolian",
            "mr"=>"Marathi",
            "ms"=>"Malay",
            "mt"=>"Maltese",
            "my"=>"Burmese",
            "nb"=>"Norwegian (bokmal)",
            "nd"=>"Ndebele",
            "ne"=>"Nepali",
          
            "nl"=>"Dutch",
            "nn"=>"Norwegian (nynorsk)",
            "ny"=>"Chewa",
            "or"=>"Oriya",
            "pa"=>"Punjabi",
            "pl"=>"Polish",
            "ps"=>"Pashto",
            "pt"=>"Portuguese (Brazil)",
            "qc"=>"Quiché",
            "qu"=>"Quechua",
            "rm"=>"Romansh",
            "ro"=>"Romanian",
            "ru"=>"Russian",
            "rw"=>"Kinyarwanda",
            "sa"=>"Sanskrit",
            "sc"=>"Sardinian",
            "se"=>"Northern Sámi",
            "si"=>"Sinhala",
            "sk"=>"Slovak",
            "sl"=>"Slovenian",
            "sn"=>"Shona",
            "so"=>"Somali",
            "sq"=>"Albanian",
            "sr"=>"Serbian",
            "sv"=>"Swedish",
            "sw"=>"Swahili",
            "sy"=>"Syriac",
            "sz"=>"Silesian",
            "ta"=>"Tamil",
            "te"=>"Telugu",
            "tg"=>"Tajik",
            "th"=>"Thai",
            "tk"=>"Turkmen",
           
            "tl"=>"Klingon",
            "tr"=>"Turkish",
            "tt"=>"Tatar",
            "tz"=>"Tamazight",
            "uk"=>"Ukrainian",
            "ur"=>"Urdu",
            "uz"=>"Uzbek",
            "vi"=>"Tiếng Việt",
            "wo"=>"Wolof",
            "xh"=>"Xhosa",
            "yi"=>"Yiddish",
            "yo"=>"Yoruba",
            "zh"=>"Simplified Chinese (China)",
            "zu"=>"Zulu",
            "zz"=>"Zazaki"
            );
    }
    // public static function listCountry(){
    //     return array(
    //         'ad'=>'ad',
    //         'ae'=>'ae',       
    //         'af'=>'af',
    //         'ag'=>'ag',
    //         'ai'=>'ai',
    //         'al'=>'al',
    //         'am'=>'am',
    //         'an'=>'an',
    //         'ao'=>'ao',
    //         'ar'=>'ar',
    //         'as'=>'as',
    //         'at'=>'at',
    //         'au'=>'au',
    //         'aw'=>'aw',
    //         'ax'=>'ax',
    //         'az'=>'az',
    //         'ba'=>'ba',
    //         'bb'=>'bb',
    //         'bd'=>'bd',
    //         'be'=>'be',
    //         'bf'=>'bf',
    //         'bg'=>'bg',
    //         'bh'=>'bh',
    //         'bi'=>'bi',
    //         'bj'=>'bj',
    //         'bm'=>'bm',
    //         'bn'=>'bn',
    //         'bo'=>'bo',
    //         'br'=>'br',
    //         'bs'=>'bs',
    //         'bt'=>'bt',
    //         'bv'=>'bv',
    //         'bw'=>'bw',
    //         'by'=>'by',
    //         'bz'=>'bz',
    //         'ca'=>'ca',
    //         'catalonia'=>'catalonia',
    //         'cc'=>'cc',
    //         'cd'=>'cd',
    //         'cf'=>'cf',
    //         'cg'=>'cg',
    //         'ch'=>'ch',
    //         'ci'=>'ci',
    //         'ck'=>'ck',
    //         'cl'=>'cl',
    //         'cm'=>'cm',
    //         'cn'=>'cn',
    //         'co'=>'co',
    //         'cr'=>'cr',
    //         'cs'=>'cs',
    //         'cu'=>'cu',
    //         'cv'=>'cv',
    //         'cx'=>'cx',
    //         'cy'=>'cy',
    //         'cz'=>'cz',
    //         'de'=>'de',
    //         'dj'=>'dj',
    //         'dk'=>'dk',
    //         'dm'=>'dm',
    //         'do'=>'do',
    //         'dz'=>'dz',
    //         'ec'=>'ec',
    //         'ee'=>'ee',
    //         'eg'=>'eg',
    //         'eh'=>'eh',
    //         'en_uk'=>'English',
    //         'en_us'=>'English',
    //         'er'=>'er',
    //         'es'=>'es',
    //         'et'=>'et',
    //         'fam'=>'fam',
    //         'fi'=>'fi',
    //         'fj'=>'fj',
    //         'fk'=>'fk',
    //         'fm'=>'fm',
    //         'fo'=>'fo',
    //         'fr'=>'fr',
    //         'ga'=>'ga',
    //         'gb'=>'gb',
    //         'gd'=>'gd',
    //         'ge'=>'ge',
    //         'gf'=>'gf',
    //         'gh'=>'gh',
    //         'gi'=>'gi',
    //         'gl'=>'gl',
    //         'gm'=>'gm',
    //         'gn'=>'gn',
    //         'gp'=>'gp',
    //         'gq'=>'gq',
    //         'gr'=>'gr',
    //         'gs'=>'gs',
    //         'gt'=>'gt',
    //         'gu'=>'gu',
    //         'gw'=>'gw',
    //         'gy'=>'gy',
    //         'hk'=>'hk',
    //         'hm'=>'hm',
    //         'hn'=>'hn',
    //         'hr'=>'hr',
    //         'ht'=>'ht',
    //         'hu'=>'hu',
    //         'id'=>'id',
    //         'ie'=>'ie',
    //         'il'=>'il',
    //         'in'=>'in',
    //         'io'=>'io',
    //         'iq'=>'iq',
    //         'ir'=>'ir',
    //         'is'=>'is',
    //         'it'=>'it',
    //         'jm'=>'jm',
    //         'jo'=>'jo',
    //         'jp'=>'日本語',
    //         'ke'=>'ke',
    //         'kg'=>'kg',
    //         'kh'=>'kh',
    //         'ki'=>'ki',
    //         'km'=>'km',
    //         'kn'=>'kn',
    //         'kp'=>'kp',
    //         'kr'=>'kr',
    //         'kw'=>'kw',
    //         'ky'=>'ky',
    //         'kz'=>'kz',
    //         'la'=>'la',
    //         'lb'=>'lb',
    //         'lc'=>'lc',
    //         'li'=>'li',
    //         'lk'=>'lk',
    //         'lr'=>'lr',
    //         'ls'=>'ls',
    //         'lt'=>'lt',
    //         'lu'=>'lu',
    //         'lv'=>'lv',
    //         'ly'=>'ly',
    //         'ma'=>'ma',
    //         'mc'=>'mc',
    //         'md'=>'md',
    //         'me'=>'me',
    //         'mg'=>'mg',
    //         'mh'=>'mh',
    //         'mk'=>'mk',
    //         'ml'=>'ml',
    //         'mm'=>'mm',
    //         'mn'=>'mn',
    //         'mo'=>'mo',
    //         'mp'=>'mp',
    //         'mq'=>'mq',
    //         'mr'=>'mr',
    //         'ms'=>'ms',
    //         'mt'=>'mt',
    //         'mu'=>'mu',
    //         'mv'=>'mv',
    //         'mw'=>'mw',
    //         'mx'=>'mx',
    //         'my'=>'my',
    //         'mz'=>'mz',
    //         'na'=>'na',
    //         'nc'=>'nc',
    //         'ne'=>'ne',
    //         'nf'=>'nf',
    //         'ng'=>'ng',
    //         'ni'=>'ni',
    //         'nl'=>'nl',
    //         'no'=>'no',
    //         'np'=>'np',
    //         'nr'=>'nr',
    //         'nu'=>'nu',
    //         'nz'=>'nz',
    //         'om'=>'om',
    //         'pa'=>'pa',
    //         'pe'=>'pe',
    //         'pf'=>'pf',
    //         'pg'=>'pg',
    //         'ph'=>'ph',
    //         'pk'=>'pk',
    //         'pl'=>'pl',
    //         'pm'=>'pm',
    //         'pn'=>'pn',
    //         'pr'=>'pr',
    //         'ps'=>'ps',
    //         'pt'=>'pt',
    //         'pw'=>'pw',
    //         'py'=>'py',
    //         'qa'=>'qa',
    //         're'=>'re',
    //         'ro'=>'ro',
    //         'rs'=>'rs',
    //         'ru'=>'ru',
    //         'rw'=>'rw',
    //         'sa'=>'sa',
    //         'sb'=>'sb',
    //         'sc'=>'sc',
    //         'scotland'=>'scotland',
    //         'sd'=>'sd',
    //         'se'=>'se',
    //         'sg'=>'sg',
    //         'sh'=>'sh',
    //         'si'=>'si',
    //         'sj'=>'sj',
    //         'sk'=>'sk',
    //         'sl'=>'sl',
    //         'sm'=>'sm',
    //         'sn'=>'sn',
    //         'so'=>'so',
    //         'sr'=>'sr',
    //         'st'=>'st',
    //         'sv'=>'sv',
    //         'sy'=>'sy',
    //         'sz'=>'sz',
    //         'tc'=>'tc',
    //         'td'=>'td',
    //         'tf'=>'tf',
    //         'tg'=>'tg',
    //         'th'=>'th',
    //         'tj'=>'tj',
    //         'tk'=>'tk',
    //         'tl'=>'tl',
    //         'tm'=>'tm',
    //         'tn'=>'tn',
    //         'to'=>'to',
    //         'tr'=>'tr',
    //         'tt'=>'tt',
    //         'tv'=>'tv',
    //         'tw'=>'tw',
    //         'tz'=>'tz',
    //         'ua'=>'ua',
    //         'ug'=>'ug',
    //         'um'=>'um',
    //         'us'=>'English',
    //         'uy'=>'uy',
    //         'uz'=>'uz',
    //         'va'=>'va',
    //         'vc'=>'vc',
    //         've'=>'ve',
    //         'vg'=>'vg',
    //         'vi'=>'vi',
    //         'vie'=>'Tiếng Việt',
    //         'vu'=>'vu',
    //         'wales'=>'wales',
    //         'wf'=>'wf',
    //         'ws'=>'ws',
    //         'ye'=>'ye',
    //         'yt'=>'yt',
    //         'za'=>'za',
    //         'zm'=>'zm',
    //         'zw'=>'zw',
    //     );
    
    // }



}
