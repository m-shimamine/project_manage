<?php

namespace App\Controllers;

use App\Models\ProcessMasterModel;

class SettingsController extends BaseController
{
    /**
     * 設定トップ画面表示
     */
    public function index()
    {
        // 工程マスタの件数を取得
        $processModel = new ProcessMasterModel();
        $processStats = $processModel->getStats();

        $data = [
            'pageTitle'    => '設定',
            'processCount' => $processStats['total'],
        ];

        return view('settings/index', $data);
    }
}
