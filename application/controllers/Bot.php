<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bot extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('download');
    }

    public function coba()
    {
        // $covid = json_decode(file_get_contents('https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/COVID19_Indonesia_per_Provinsi/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=json'));
        // print_r($covid);
        // die();
        $menu_1 = $this->db->where(['menu' => 'menu_1'])->get('tbl_fitur')->result();
        foreach ($menu_1 as $m1) {
            $menu_1_data[] = [
                ['text' => $m1->fitur, 'callback_data' => $m1->callback_data, 'url' => $m1->url]
            ];
        }
        $menu_1 = [
            'inline_keyboard' =>
            $menu_1_data,
            [
                ['text' => '« Kembali ke menu utama', 'callback_data' => 'menu_utama']
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        print_r($menu_1);
    }

    public function index()
    {
        $token = '1761592706:AAHIDS7P5XeFEtDhMyOTMqsA9jIFk60k3ow';
        $bot_receive = json_decode(file_get_contents('php://input'));
        // $update = file_get_contents('php://input');
        // $update = json_decode($update);
        // die();
        if ($bot_receive->callback_query) {
            $chat_id = $bot_receive->callback_query->from->id;
            $hear = $bot_receive->callback_query->message->text;
            $callback_data = $bot_receive->callback_query->data;
            $callback_description = $this->db->where(['callback_data' => $callback_data])->get('tbl_fitur')->row()->description;
            $callback_source = $this->db->where(['callback_data' => $callback_data])->get('tbl_fitur')->row()->source;
            $message_id = $bot_receive->callback_query->message->message_id;
        } else {
            $chat_id = $bot_receive->message->from->id;
            $hear = $bot_receive->message->text;
            $message_id = $bot_receive->message->message_id;
        }

        print_r($callback_description);
        // inline keyboard menu utama
        $fitur = $this->db->where(['menu' => 'menu_utama'])->get('tbl_fitur')->result();
        foreach ($fitur as $f) {
            $pilih_fitur[] = [
                ['text' => $f->fitur, 'callback_data' => $f->callback_data, 'url' => $f->url],
            ];
        }
        $default_inline = [
            'inline_keyboard' =>
            $pilih_fitur,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // inline keyboard menu info covid
        $menu_1 = $this->db->where(['menu' => 'menu_1'])->get('tbl_fitur')->result();
        foreach ($menu_1 as $m1) {
            $menu_1_data[] = [
                ['text' => $m1->fitur, 'callback_data' => $m1->callback_data, 'url' => $m1->url]
            ];
        }
        $menu_1 = [
            'inline_keyboard' => $menu_1_data,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // kembali ke menu info covid
        $menu_1_kembali = [
            'inline_keyboard' => [
                [
                    ['text' => '« Kembali ke menu sebelumnya', 'callback_data' => 'Informasi Covid-19']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // kembali ke menu utama
        $menu_utama_kembali = [
            'inline_keyboard' => [
                [
                    ['text' => '« Kembali ke menu utama', 'callback_data' => 'menu_utama']
                ]
            ]
        ];

        // remove keyboard
        $rm_keyboard = [
            'remove_keyboard' => true
        ];
        // [
        //     ['text' => '« Kembali ke menu utama', 'callback_data' => 'menu_utama']
        // ]

        // kondisi
        switch ($callback_data) {
            case true:
                $method = '/editMessageText?';
                switch ($callback_data) {
                    case 'Informasi Covid-19':
                        $bot_reply = '<b>' . $callback_data . "</b> \n\n" . $callback_description . "\n\n<em>" . $callback_source . '</em>';
                        $reply_markup = json_encode($menu_1);
                        break;

                    case 'Pengaduan Covid-19':
                        $method = '/sendContact?';
                        // $bot_reply = '<b>' . $callback_data . "</b> \n\n" . $callback_description . "\n\n<em>" . $callback_source . '</em>';
                        // $reply_markup = json_encode($menu_utama_kembali);
                        break;

                    case 'menu_utama':
                        $bot_reply = 'Berikut adalah informasi yang dapat diakses :';
                        $reply_markup = json_encode($default_inline);
                        break;

                    case 'Update COVID-19 Hari ini':
                        $covid = file_get_contents('https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/COVID19_Indonesia_per_Provinsi/FeatureServer/0/query?where=1%3D1&outFields=*&outSR=4326&f=json');
                        $covid_decode = json_decode($covid)->features;
                        $provinsi = '';
                        foreach ($covid_decode as $c) {
                            $provinsi .= '/' . $c->attributes->Provinsi . ', ';
                        }
                        //data dari array attributes
                        foreach ($covid_decode as $c) {
                            // untuk keyboard
                            $pilih_provinsi[] = [
                                ['text' => $c->attributes->Provinsi]
                            ];

                            // untuk ngambil key dari tiap provinsi
                            $data_provinsi[] = $c->attributes;
                        }
                        $keys = array_keys(array_column($data_provinsi, 'Provinsi'), 'Jawa Tengah');
                        $data_perprov = $data_provinsi[$keys[0]];

                        // print_r($data_perprov);
                        $bot_reply = '<b>Informasi Covid Provinsi di Indonesia' . "\n" .
                            date("l, d/m/Y") . '</b>' . "\n\n" .
                            '<b>' . $data_perprov->Provinsi . "</b>\n" .
                            'Positif : ' . $data_perprov->Kasus_Posi . "\n" .
                            'Sembuh : ' . $data_perprov->Kasus_Semb . "\n" .
                            'Meninggal : ' . $data_perprov->Kasus_Meni . "\n\n" . '<em>- bnpb-inacovid19.hub.arcgis.com/</em>';
                        $reply_markup = json_encode($menu_1_kembali);
                        break;

                    case $callback_data:
                        $bot_reply = '<b>' . $callback_data . "</b> \n\n" . $callback_description . "\n\n<em>" . $callback_source . '</em>';
                        $reply_markup = json_encode($menu_1_kembali);
                        break;

                    default:
                        exit;
                        break;
                }
                break;

            default:
                $method = '/sendMessage?';
                switch ($hear) {
                    case '/start':
                        $bot_reply = 'Berikut adalah informasi yang dapat diakses :';
                        $reply_markup = json_encode($default_inline);
                        break;

                    case '/help':
                        $bot_reply = 'Berikut adalah informasi yang dapat diakses :';
                        $reply_markup = json_encode($default_inline);
                        break;

                    default:
                        $bot_reply = 'Maaf, perintah yang anda masukkan tidak tersedia..' . "\n" . 'silahkan akses informasi di bawah : ';
                        $reply_markup = json_encode($default_inline);
                        break;
                }
                break;
        }
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $bot_reply,
            'reply_markup' => $reply_markup,
            'parse_mode' => 'html',
            'phone_number' => '119',
            'first_name' => 'Hotline COVID-19'
        ];

        // $data = file_get_contents('https://saintek.unisnu.ac.id/');
        // print_r(htmlentities($data));
        $url = 'https://api.telegram.org/bot' . $token . $method;
        $ch = curl_init();

        if ($ch) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $ce =  curl_exec($ch);
            curl_close($ch);
            // print_r($ce);
            return $ce;
        } else {
            exit;
        }
    }
}

/* End of file Bot.php */
