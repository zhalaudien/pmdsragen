<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCabangCodeFromMtaApi extends Migration
{
    /**
     * Pemetaan resmi Kode Cabang dan UUID dari API Pusat MTA (api.mta.or.id)
     */
    protected array $cabangMta = [
        '04442a46-7c20-475c-bb90-b9169a17cf6e' => ['code' => '86.1',  'name' => 'Gemolong 1'],
        'b6250d27-6b4c-43f8-9bdf-64c578e6aa86' => ['code' => '86.2',  'name' => 'Gemolong 2'],
        '58557ab6-be68-4b4d-a867-4918e397c67c' => ['code' => '86.3',  'name' => 'Gemolong 3'],
        '6f834d29-a26b-41cb-8e0f-148e0242675b' => ['code' => '86.4',  'name' => 'Gemolong 4'],
        '05492929-86e0-4b86-81a0-ca3128d95b60' => ['code' => '86.5',  'name' => 'Gemolong 5'],
        '9f7ade17-70c6-4d3a-a4c5-5eefa60bdabe' => ['code' => '86.6',  'name' => 'Gesi'],
        '370c65c2-5816-4e3b-9c53-6f0bcdca1c4e' => ['code' => '86.7',  'name' => 'Gondang 1'],
        'd60d7135-c669-40ab-abd7-a5644850278d' => ['code' => '86.8',  'name' => 'Gondang 2'],
        '5dd73fb8-0353-471b-a25c-37971837b4f7' => ['code' => '86.9',  'name' => 'Gondang 3'],
        '4c64e3ff-77c3-41d6-bbbf-6df45d6ce34d' => ['code' => '86.10', 'name' => 'Jenar'],
        '99d964ae-5bc2-47ae-8284-7328deb3de90' => ['code' => '86.11', 'name' => 'Kalijambe 1'],
        '82cc0d53-2958-4c53-9ed7-3d44558ed1e4' => ['code' => '86.12', 'name' => 'Kalijambe 2'],
        '4bd8abe0-48ff-4582-99b6-748f8358bd3f' => ['code' => '86.13', 'name' => 'Karangmalang 1'],
        'f7f2c1a1-5086-4840-9677-20389105962f' => ['code' => '86.14', 'name' => 'Karangmalang 2'],
        '5aefe6f2-fe44-4f04-a1d6-96d3b04a6b8a' => ['code' => '86.15', 'name' => 'Karangmalang 3'],
        'f2a9ab6d-de50-4dea-a72f-445aa3b26732' => ['code' => '86.16', 'name' => 'Karangmalang 4'],
        '343efee2-9ed8-4d48-81db-c0368987498a' => ['code' => '86.17', 'name' => 'Kedawung 1'],
        '40f366db-f1f8-47a3-9ae6-b63114247774' => ['code' => '86.18', 'name' => 'Kedawung 2'],
        '7957fef7-f09e-4343-ac72-89b26493375b' => ['code' => '86.19', 'name' => 'Kedawung 3'],
        '6320667c-68aa-40be-9e1f-4a14f7486394' => ['code' => '86.20', 'name' => 'Kedawung 4'],
        '6fc86c75-5156-497e-b944-cb2494e7546e' => ['code' => '86.21', 'name' => 'Masaran 1'],
        '53292c5e-fa35-4d2a-a4af-8f4792602c1c' => ['code' => '86.22', 'name' => 'Masaran 2'],
        '6e14d879-d5d3-4982-8aff-a617b80bb2ce' => ['code' => '86.23', 'name' => 'Masaran 3'],
        'c6242522-6f26-403d-8b77-794f14d65461' => ['code' => '86.24', 'name' => 'Masaran 4'],
        '818e1cfb-1adf-4ea7-b924-7f90e483f511' => ['code' => '86.25', 'name' => 'Masaran 5'],
        'c79f90f1-70a5-4f21-ae3e-feed38dab4b8' => ['code' => '86.26', 'name' => 'Masaran 6'],
        '42dce2e4-3250-416d-a4ef-d32b4e450100' => ['code' => '86.27', 'name' => 'Miri 1'],
        '0751a380-08d5-4e78-909d-b13615bcc30c' => ['code' => '86.28', 'name' => 'Miri 2'],
        '62fecca1-af23-4f97-8e53-7bd29ba77003' => ['code' => '86.29', 'name' => 'Mondokan 1'],
        '7925be11-289c-4051-a6dc-f487d742b43e' => ['code' => '86.30', 'name' => 'Mondokan 2'],
        'a84976a4-b534-47a6-8e78-dd14f802d576' => ['code' => '86.31', 'name' => 'Mondokan 3'],
        '4003d421-88d9-4340-bdb9-8d4a47963bed' => ['code' => '86.32', 'name' => 'Ngrampal 1'],
        '39711581-2519-406c-a7a6-6b9b5f760117' => ['code' => '86.33', 'name' => 'Ngrampal 2'],
        'c71cab70-f154-46b7-8050-fa603275d8c6' => ['code' => '86.34', 'name' => 'Plupuh 1'],
        '91d366db-c4b9-46a1-ad9a-d03e3a99e2f9' => ['code' => '86.35', 'name' => 'Plupuh 2'],
        '7c87f754-3401-4625-bfb9-2cfc00594fa1' => ['code' => '86.36', 'name' => 'Plupuh 3'],
        'cf005c50-89b9-40d3-a742-c65f030c4b38' => ['code' => '86.37', 'name' => 'Plupuh 4'],
        '6c465474-c967-4dd4-987e-64b31dafc4eb' => ['code' => '86.38', 'name' => 'Plupuh 5'],
        '7edaa422-fe88-46f5-b28d-320145132b6e' => ['code' => '86.39', 'name' => 'Sambirejo 1'],
        '4050cd44-72e0-4dab-bd99-dc7a35ee52fd' => ['code' => '86.40', 'name' => 'Sambirejo 2'],
        '2c6c5033-e242-48aa-8b0b-d0382e15d33e' => ['code' => '86.41', 'name' => 'Sambungmacan 1'],
        'd081acdd-1af7-462c-b8a6-b2a44c0d969f' => ['code' => '86.42', 'name' => 'Sambungmacan 2'],
        '463c9579-bcf2-497f-a3a5-14af686f9b46' => ['code' => '86.43', 'name' => 'Sidoharjo 1'],
        '6fd79428-46b9-4cf3-b40a-ed1eae638b56' => ['code' => '86.44', 'name' => 'Sidoharjo 2'],
        'fb3e5ecd-0370-4801-b1c7-8d5ee6da6d86' => ['code' => '86.45', 'name' => 'Sidoharjo 3'],
        '6d8cb227-3720-488c-ba22-8acb77201dc6' => ['code' => '86.46', 'name' => 'Sidoharjo 4'],
        '550d3726-dc32-48c6-b31e-d0c726cbafee' => ['code' => '86.47', 'name' => 'Sragen 1'],
        'f0a0ffc2-70d0-489a-b6ae-135cb7499d6f' => ['code' => '86.48', 'name' => 'Sragen 2'],
        '36dbcb9b-202c-4a00-bfb1-8bf4a3b76454' => ['code' => '86.49', 'name' => 'Sukodono 1'],
        '1488671c-3974-479f-a825-4549bc8c159c' => ['code' => '86.50', 'name' => 'Sukodono 2'],
        '7d2f56d5-9628-401a-82c7-369b35055bb8' => ['code' => '86.51', 'name' => 'Sukodono 3'],
        '4199fee0-2c36-4a3d-a037-672f45c1340b' => ['code' => '86.52', 'name' => 'Sumberlawang 1'],
        '9e2347c4-045a-45bf-9dcd-11a262b4669d' => ['code' => '86.53', 'name' => 'Sumberlawang 2'],
        '18599313-38cd-4c9a-8385-2e8187d9167b' => ['code' => '86.54', 'name' => 'Sumberlawang 3'],
        '5aaefa3f-6893-4316-8b6c-f891d8af30ac' => ['code' => '86.55', 'name' => 'Tangen 1'],
        '8964f353-c628-412b-ab91-25fc0f2305f7' => ['code' => '86.56', 'name' => 'Tanon 1'],
        'd52f13b5-1d1b-4041-8f01-1aa4d288b05d' => ['code' => '86.57', 'name' => 'Tanon 2'],
        'ed5e631c-5ded-48cc-aec6-c0a05128b1ed' => ['code' => '86.58', 'name' => 'Tanon 3'],
        '0bad09f9-7692-4bf9-b511-d20d6f713653' => ['code' => '86.59', 'name' => 'Kalijambe 3'],
        'ac576b67-8297-4bc2-ad28-7e047922bdef' => ['code' => '86.60', 'name' => 'Tangen 2'],
        '1de8ea2b-b1a5-4f19-81e5-6644daf0b788' => ['code' => '86.61', 'name' => 'Ngrampal 3'],
        '0f3bcf6b-9dde-4bd6-a176-afbd69212a13' => ['code' => '86.62', 'name' => 'Sambungmacan 3'],
        'df547f3f-24df-43f0-803f-96f08cd6c072' => ['code' => '86.63', 'name' => 'Kalijambe 4'],
        '7fe11ef8-cede-477d-a33f-f2d43c5b5042' => ['code' => '86.64', 'name' => 'Sukodono 4'],
        '51454eb5-b84a-49a6-a594-afe8c113b5a3' => ['code' => '86.65', 'name' => 'Kedawung 5'],
        '1da48f1c-0782-4c94-b80c-022f4604e8a7' => ['code' => '86.66', 'name' => 'Karangmalang 5'],
        '05c85557-8513-48cf-a06e-5b70b505fdbf' => ['code' => '86.67', 'name' => 'Sumberlawang 4'],
        'd06ec130-fd9f-44a3-a7fc-3b2d03aaca88' => ['code' => '86.68', 'name' => 'Gondang 4'],
        '9f18c25e-9e44-4db6-9fec-4081192b1b95' => ['code' => '86.69', 'name' => 'Plupuh 6'],
        'e8a82b2c-77ec-41c8-b40e-c6ede00fd8b5' => ['code' => '86.0',  'name' => 'Sragen Perwakilan'],
    ];

    public function up()
    {
        $builder = $this->db->table('cabang');

        foreach ($this->cabangMta as $uuid => $info) {
            // 1. Cari berdasarkan mta_uuid jika sudah ada
            $cabang = $builder->where('mta_uuid', $uuid)->get()->getRowArray();

            // 2. Jika belum ada mta_uuid, cari berdasarkan kesamaan nama
            if (!$cabang) {
                $cabang = $builder->where('LOWER(TRIM(name))', strtolower(trim($info['name'])))->get()->getRowArray();
            }

            if ($cabang) {
                $builder->where('id', $cabang['id'])->update([
                    'code'     => $info['code'],
                    'mta_uuid' => $uuid,
                ]);
            }
        }
    }

    public function down()
    {
        // Opsional: kembalikan kode format CBG-xxx berurutan berdasarkan id
        $builder = $this->db->table('cabang');
        $rows    = $builder->orderBy('id', 'ASC')->get()->getResultArray();

        $idx = 1;
        foreach ($rows as $row) {
            $builder->where('id', $row['id'])->update([
                'code' => 'CBG-' . str_pad((string) $idx++, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
