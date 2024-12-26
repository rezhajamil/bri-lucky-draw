<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Winners extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save_winners()
    {
        // Ensure the request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(['message' => 'Method not allowed']));
        }

        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data[0]['winners'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'Invalid JSON format']));
        }

        $latest_batch = $this->db->select_max('batch')->get('winners')->row();
        $batch_number = ($latest_batch && $latest_batch->batch) ? $latest_batch->batch + 1 : 1;

        // Iterate through winners and insert into DB
        foreach ($data as $key => $batch) {
            foreach ($batch['winners'] as $winner) {
                $this->db->insert('winners', [
                    'batch' => $batch_number,
                    'personal_number' => $winner['Personal Number'],
                    'unit_kerja' => $winner['Unit Kerja'],
                    'jabatan' => $winner['Jabatan'],
                    'total_amount' => $winner['totalAmount'],
                    'prize' => $winner['prize'],
                    'prize_id' => $winner['prizeID'],
                    'kode_hadiah' => $winner['Kode Hadiah'],
                    'merk' => $winner['Merk'],
                    'kategori' => $winner['Kategori'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            $batch_number++;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(201)
            ->set_output(json_encode(['message' => 'Winners saved successfully']));
    }
}
