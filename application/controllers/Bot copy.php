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
        // print_r($this->db->get('fitur')->result());
        die();
        $fitur = $this->db->get('fitur')->result();
        $bot_reply = 'Berikut adalah informasi yang dapat anda akses';
        foreach ($fitur as $f) {
            $pilih_fitur[] = [
                ['text' => $f->text, 'callback_data' => $f->callback_data, 'url' => $f->url],
            ];
        }
        // $default_inline = [
        //     'inline_keyboard' => [
        //         [
        //             ['text' => 'panduan skripsi', 'callback_data' => 'tombol panduan skripsi', 'url' => 'https://drive.google.com/u/0/uc?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n&export=download']
        //         ],
        //         [
        //             ['text' => 'panduan kkl', 'callback_data' => 'tombol panduan skripsi', 'url' => 'https://drive.google.com/u/0/uc?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n&export=download']
        //         ]
        //     ],
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true
        // ];
        // $default_inlinee = [
        //     'inline_keyboard' =>
        //     $pilih_fitur,
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true
        // ];

        $default_inline = [
            'inline_keyboard' =>
            $pilih_fitur,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $default_inlinee = [
            'inline_keyboard' => [
                [
                    ['text' => 'info', 'callback_query' => 'anjay'],
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        print_r($default_inline);
        echo '</br></br>';
        print_r($default_inlinee);
    }

    public function index()
    {
        $token = '1761592706:AAHIDS7P5XeFEtDhMyOTMqsA9jIFk60k3ow';
        $bot_receive = json_decode(file_get_contents('php://input'));
        $fitur = $this->db->where(['type' => 'menu-utama'])->get('fitur')->result();
        $informasi_covid19_menu = $this->db->where(['type' => 'informasi covid-19'])->get('fitur')->result();
        // $update = file_get_contents('php://input');
        // $update = json_decode($update);
        // die();
        if ($bot_receive->callback_query) {
            $chat_id = $bot_receive->callback_query->from->id;
            $hear = $bot_receive->callback_query->message->text;
            $callback_data = $bot_receive->callback_query->data;
            $callback_description = $this->db->where(['callback_data' => $callback_data])->get('fitur')->row()->description;
            $callback_source = $this->db->where(['callback_data' => $callback_data])->get('fitur')->row()->source;
            $message_id = $bot_receive->callback_query->message->message_id;
        } else {
            $chat_id = $bot_receive->message->from->id;
            $hear = $bot_receive->message->text;
            $message_id = $bot_receive->message->message_id;
        }

        print_r($callback_description);
        // inline keyboard menu utama
        foreach ($fitur as $f) {
            $pilih_fitur[] = [
                ['text' => $f->text, 'callback_data' => $f->callback_data, 'url' => $f->url],
            ];
        }
        $default_inline = [
            'inline_keyboard' =>
            $pilih_fitur,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // inline keyboard menu info covid
        foreach ($informasi_covid19_menu as $ic) {
            $informasi_covid19_data[] =
                ['text' => $ic->text, 'callback_data' => $ic->callback_data, 'url' => $ic->url];
        }
        $informasi_covid19 = [
            'inline_keyboard' => [
                $informasi_covid19_data,
                [
                    ['text' => '« Kembali ke menu utama', 'callback_data' => 'menu-utama']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // kembali ke menu info covid
        $informasi_covid19_kembali = [
            'inline_keyboard' => [
                [
                    ['text' => '« Kembali ke menu Informasi Covid-19', 'callback_data' => 'Informasi Covid-19']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        // remove keyboard
        $rm_keyboard = [
            'remove_keyboard' => true
        ];

        // kondisi
        switch ($callback_data) {
            case true:
                $method = '/editMessageText?';
                switch ($callback_data) {
                    case 'Informasi Covid-19':
                        $bot_reply = '<b>' . $callback_data . "</b> \n\n" . $callback_description . "\n\n<em>- " . $callback_source . '</em>';
                        $reply_markup = json_encode($informasi_covid19);
                        break;

                    case 'Hotline Covid-19':
                        $bot_reply = $callback_data;
                        $reply_markup = json_encode($rm_keyboard);
                        break;

                    case 'menu-utama':
                        $bot_reply = 'Berikut adalah informasi yang dapat diakses :';
                        $reply_markup = json_encode($default_inline);
                        break;

                    case $callback_data:
                        $bot_reply = '<b>' . $callback_data . "</b> \n\n" . $callback_description . "\n\n<em>- " . $callback_source . '</em>';
                        $reply_markup = json_encode($informasi_covid19_kembali);
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
            'parse_mode' => 'html'
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
