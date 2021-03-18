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
        print_r($this->db->get('command')->result());
    }

    public function index()
    {
        $token = '1625231823:AAGHkRp3BG3Q-x7yvHWbGjuTabSH1P_ej94';
        $bot_receive = json_decode(file_get_contents('php://input'));
        $fitur = $this->db->get('fitur')->result();
        // $update = file_get_contents('php://input');
        // $update = json_decode($update);
        print_r($bot_receive);
        if ($bot_receive->callback_query->data) {
            $chat_id = $bot_receive->callback_query->from->id;
            $hear = $bot_receive->callback_query->message->text;
        } else {
            $chat_id = $bot_receive->message->from->id;
            $hear = $bot_receive->message->text;
        }

        // $download = file_get_contents('https://drive.google.com/open?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n');
        // $download = file_get_contents('https://drive.google.com/u/0/uc?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n&export=download');
        // $download_name = 'pandian skripsi.pdf';
        // $default_keyboard = [
        //     'keyboard' => [
        //         [
        //             ['text' => 'download anu']
        //         ]
        //     ],
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true
        // ];
        // $downloadanu = force_download($download_name, $download);
        $bot_reply = 'silahkan tekan tombol';
        foreach ($fitur as $f) {
            $pilih_fitur[] = [
                ['text' => $f->text, 'callback_data' => $f->callback_data, 'url' => $f->url]
            ];
        }
        $default_inline = [
            'inline_keyboard' =>
            $pilih_fitur,
            // [
            //     [
            //         ['text' => 'panduan skripsi', 'callback_data' => 'tombol panduan skripsi', 'url' => 'https://drive.google.com/u/0/uc?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n&export=download'],
            //         ['text' => 'panduan kkl', 'callback_data' => 'tombol panduan skripsi', 'url' => 'https://drive.google.com/u/0/uc?id=0B1euPCdEA6bncmdpV2RTUzJWeldMekFXVEQtbURCTjc4NC1n&export=download'],
            //     ]
            // ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        $reply_markup = json_encode($default_inline);

        // kondisi
        // switch () {
        //     case 'value':
        //         # code...
        //         break;

        //     default:
        //         # code...
        //         break;
        // }
        $data = [
            'chat_id' => $chat_id,
            'text' => $bot_reply,
            'parse_mode' => 'html',
            'reply_markup' => $reply_markup
        ];
        $method = '/sendMessage?';
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
