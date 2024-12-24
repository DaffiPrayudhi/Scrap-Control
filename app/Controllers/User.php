<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserModelFA;
use App\Models\MesinSMTModel;
use App\Models\MesinFAModel;
use App\Models\PartNumberSMTModel;
use App\Models\PartNumberFAModel;
use App\Models\PartNumberKompModel;
use App\Models\ScrapTypeSMTModel;
use App\Models\ScrapTypeFAModel;
use CodeIgniter\Controller;
use CodeIgniter\DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class User extends Controller
{
    protected $UserModel;
    protected $UserModelFA;
    protected $PartNumberSMTModel;
    protected $PartNumberFAModel;
    protected $PartNumberKompModel;
    protected $MesinSMTModel;
    protected $MesinFAModel;
    protected $ScrapTypeSMTModel;
    protected $ScrapTypeFAModel;
    
    public function __construct()
    {
        $this->UserModel = new UserModel();
        $this->UserModelFA = new UserModelFA();
        $this->PartNumberSMTModel = new PartNumberSMTModel();
        $this->PartNumberFAModel = new PartNumberFAModel();
        $this->PartNumberKompModel = new PartNumberKompModel();
        $this->MesinSMTModel = new MesinSMTModel();
        $this->MesinFAModel = new MesinFAModel();
        $this->ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $this->ScrapTypeFAModel = new ScrapTypeFAModel();
        helper('form');
    }

    public function admnsmtDashboard()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $mesin = $this->request->getGet('mesin');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');
        

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $currentMonthData = $this->UserModel->getPieChartData($currentMonthStart, $currentMonthEnd, $model, $mesin, $tipe_ng, $line);
        $previousMonthData = $this->UserModel->getPieChartData($previousMonthStart, $previousMonthEnd, $model, $mesin, $tipe_ng, $line);

        $data['scrap_control'] = $this->UserModel->orderBy('tgl_bln_thn', 'desc')->findAll();
        $data['scrap_chart_data'] = $this->UserModel->getFilteredScrapData($startDate, $endDate, $model, $mesin, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin SMT Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'mesin' => $mesin,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['models'] = $this->UserModel->where('model is not null')->distinct()->findColumn('model');
        $data['mesins'] = $this->UserModel->where('mesin is not null')->distinct()->findColumn('mesin');
        $data['tipe_ngs'] = $this->UserModel->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModel->where('line is not null')->distinct()->findColumn('line');

        $totalQty = $this->UserModel->getTotalQty($startDate, $endDate, $model, $mesin, $tipe_ng, $line);

        $colors = [
            'K0JG' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2P'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K59_K60' => 'rgba(255, 159, 64, 1)',
            'SIIX' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)' 	
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
                
            }
        }

        $data['colors'] = $colors;
        $data['totalQty'] = $totalQty;
        $data['current_month_data'] = $currentMonthData;
        $data['previous_month_data'] = $previousMonthData;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;

        return view('admnsmt/dashboardsmt', $data);
    }

    
    public function admnfaDashboard()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $komponen = $this->request->getGet('komponen');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');
        

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $currentMonthData = $this->UserModelFA->getPieChartData($currentMonthStart, $currentMonthEnd, $model, $komponen, $tipe_ng, $line);
        $previousMonthData = $this->UserModelFA->getPieChartData($previousMonthStart, $previousMonthEnd, $model, $komponen, $tipe_ng, $line);

        $data['scrap_control'] = $this->UserModelFA->findAll();
        $data['scrap_chart_data'] = $this->UserModelFA->getFilteredScrapData($startDate, $endDate, $model, $komponen, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin FA Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'komponen' => $komponen,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['models'] = $this->UserModelFA->where('model is not null')->distinct()->findColumn('model');
        $data['komponens'] = $this->UserModelFA->where('komponen is not null')->distinct()->findColumn('komponen');
        $data['tipe_ngs'] = $this->UserModelFA->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModelFA->where('line is not null')->distinct()->findColumn('line');

        $totalQty = $this->UserModelFA->getTotalQty($startDate, $endDate, $model, $komponen, $tipe_ng, $line);

        $colors = [
            'K0J' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2PG'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K60_K2VG' => 'rgba(255, 159, 64, 1)',
            'K45R' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)',	
            'K15P' => 'rgba(160, 82, 82, 1)' 
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
                
            }
        }

        $data['colors'] = $colors;
        $data['totalQty'] = $totalQty;
        $data['current_month_data'] = $currentMonthData;
        $data['previous_month_data'] = $previousMonthData;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;

        return view('admnfa/dashboardfa', $data);
    }

    public function admnscrapDashboard()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $mesin = $this->request->getGet('mesin');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');
        

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $currentMonthData = $this->UserModel->getPieChartData($currentMonthStart, $currentMonthEnd, $model, $mesin, $tipe_ng, $line);
        $previousMonthData = $this->UserModel->getPieChartData($previousMonthStart, $previousMonthEnd, $model, $mesin, $tipe_ng, $line);

        $data['scrap_control'] = $this->UserModel->orderBy('tgl_bln_thn', 'desc')->findAll();
        $data['scrap_chart_data'] = $this->UserModel->getFilteredScrapData($startDate, $endDate, $model, $mesin, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin Scrap SMT Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'mesin' => $mesin,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['models'] = $this->UserModel->where('model is not null')->distinct()->findColumn('model');
        $data['mesins'] = $this->UserModel->where('mesin is not null')->distinct()->findColumn('mesin');
        $data['tipe_ngs'] = $this->UserModel->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModel->where('line is not null')->distinct()->findColumn('line');

        $totalQty = $this->UserModel->getTotalQty($startDate, $endDate, $model, $mesin, $tipe_ng, $line);

        $colors = [
            'K0JG' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2P'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K59_K60' => 'rgba(255, 159, 64, 1)',
            'SIIX' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)' 	
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
                
            }
        }

        $data['colors'] = $colors;
        $data['totalQty'] = $totalQty;
        $data['current_month_data'] = $currentMonthData;
        $data['previous_month_data'] = $previousMonthData;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;

        return view('admnscrap/dashboardscrap_smt', $data);
    }

    public function admnscrapDashboardFA()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $komponen = $this->request->getGet('komponen');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');
        

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $currentMonthData = $this->UserModelFA->getPieChartData($currentMonthStart, $currentMonthEnd, $model, $komponen, $tipe_ng, $line);
        $previousMonthData = $this->UserModelFA->getPieChartData($previousMonthStart, $previousMonthEnd, $model, $komponen, $tipe_ng, $line);

        $data['scrap_control'] = $this->UserModelFA->findAll();
        $data['scrap_chart_data'] = $this->UserModelFA->getFilteredScrapData($startDate, $endDate, $model, $komponen, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin Scrap FA Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'komponen' => $komponen,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['models'] = $this->UserModelFA->where('model is not null')->distinct()->findColumn('model');
        $data['komponens'] = $this->UserModelFA->where('komponen is not null')->distinct()->findColumn('komponen');
        $data['tipe_ngs'] = $this->UserModelFA->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModelFA->where('line is not null')->distinct()->findColumn('line');

        $totalQty = $this->UserModelFA->getTotalQty($startDate, $endDate, $model, $komponen, $tipe_ng, $line);

        $colors = [
            'K0J' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2PG'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K60_K2VG' => 'rgba(255, 159, 64, 1)',
            'K45R' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)',	
            'K15P' => 'rgba(160, 82, 82, 1)' 
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
                
            }
        }

        $data['colors'] = $colors;
        $data['totalQty'] = $totalQty;
        $data['current_month_data'] = $currentMonthData;
        $data['previous_month_data'] = $previousMonthData;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;

        return view('admnscrap/dashboardscrap_fa', $data);
    }

    public function admnscrapDashboardFA_price()
    {
        
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $komponen = $this->request->getGet('komponen');
        $part_number = $this->request->getGet('part_number');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $hargaSatuan = $this->UserModelFA->getHargaSatuan($model, $komponen, $part_number);

        $data['scrap_control'] = $this->UserModelFA->findAll();
        $data['scrap_chart_data'] = $this->UserModelFA->getFilteredScrapDataWithPrice($startDate, $endDate, $model, $komponen, $part_number, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin Scrap FA Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'komponen' => $komponen,
            'part_number' => $part_number,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['hargaSatuan'] = $hargaSatuan ? $hargaSatuan['harga'] : '';

        $data['models'] = $this->UserModelFA->where('model is not null')->distinct()->findColumn('model');
        $data['komponens'] = $this->UserModelFA->where('komponen is not null')->distinct()->findColumn('komponen');
        $data['part_numbers'] = $this->UserModelFA->where('part_number is not null')->distinct()->findColumn('part_number');
        $data['tipe_ngs'] = $this->UserModelFA->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModelFA->where('line is not null')->distinct()->findColumn('line');

        $colors = [
            'K0J' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2PG'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K60_K2VG' => 'rgba(255, 159, 64, 1)',
            'K45R' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)',    
            'K15P' => 'rgba(160, 82, 82, 1)' 
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
            }
        }

        $totalHarga = 0;
        foreach ($data['scrap_chart_data'] as $row) {
            $totalHarga += $row['total_harga'];
        }

        $totalQty = 0;
        foreach ($data['scrap_chart_data'] as $row) {
            $totalQty += $row['qty'];
        }

        $data['colors'] = $colors;
        $data['totalHarga'] = $totalHarga;
        $data['totalQty'] = $totalQty;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;
        

        return view('admnscrap/dashboardscrap_fa_price', $data);
    }

    public function admnscrapDashboardSMT_price()
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $model = $this->request->getGet('model');
        $mesin = $this->request->getGet('mesin');
        $part_number = $this->request->getGet('part_number');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $scraptype = $this->request->getGet('scraptype');
        $line = $this->request->getGet('line');
        

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01'); 
            $endDate = date('Y-m-t');   
        }

        $currentMonthStart = $startDate;
        $currentMonthEnd = $endDate;

        $previousMonthStart = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $previousMonthEnd = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));

        $currentMonthName = date('F', strtotime($currentMonthStart));
        $previousMonthName = date('F', strtotime($previousMonthStart));

        $hargaSatuan = $this->UserModel->getHargaSatuan($model, $line, $part_number, $scraptype);

        $data['scrap_control'] = $this->UserModel->findAll();
        $data['scrap_chart_data'] = $this->UserModel->getFilteredScrapDataWithPrice($startDate, $endDate, $model, 
        $part_number, $mesin, $tipe_ng, $line);
        $data['pageTitle'] = 'Admin Scrap SMT Dashboard';
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'model' => $model,
            'mesin' => $mesin,
            'scraptype' => $scraptype,
            'part_number' => $part_number,
            'tipe_ng' => $tipe_ng,
            'line' => $line
        ];

        $data['hargaSatuan'] = $hargaSatuan ? $hargaSatuan['harga'] : '';

        $data['models'] = $this->UserModel->where('model is not null')->distinct()->findColumn('model');
        $data['mesins'] = $this->UserModel->where('mesin is not null')->distinct()->findColumn('mesin');
        $data['part_numbers'] = $this->UserModel->where('part_number is not null')->distinct()->findColumn('part_number');
        $data['tipe_ngs'] = $this->UserModel->where('tipe_ng is not null')->distinct()->findColumn('tipe_ng');
        $data['lines'] = $this->UserModel->where('line is not null')->distinct()->findColumn('line');

        $colors = [
            'K0JG' => 'rgba(75, 192, 192, 1)',
            'K1ZA' => 'rgba(255, 99, 132, 1)',
            'K2P'  => 'rgba(54, 162, 235, 1)',
            'K2SA' => 'rgba(255, 206, 86, 1)',
            'K3VA' => 'rgba(153, 102, 255, 1)',
            'K59_K60' => 'rgba(255, 159, 64, 1)',
            'SIIX' => 'rgba(255, 99, 71, 1)',
            'K1AL' => 'rgba(50, 205, 50, 1)' 	
        ];

        foreach ($data['models'] as $model) {
            if (!isset($colors[$model])) {
                $colors[$model] = 'rgba(' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',' . mt_rand(0, 255) . ',0.6)';
                
            }
        }

        $totalHarga = 0;
        foreach ($data['scrap_chart_data'] as $row) {
            $totalHarga += $row['total_harga'];
        }

        $totalQty = 0;
        foreach ($data['scrap_chart_data'] as $row) {
            $totalQty += $row['qty'];
        }

        $data['colors'] = $colors;
        $data['totalHarga'] = $totalHarga;
        $data['totalQty'] = $totalQty;
        $data['currentMonthName'] = $currentMonthName;
        $data['previousMonthName'] = $previousMonthName;

        return view('admnscrap/dashboardscrap_smt_price', $data);
    }

    public function part_number_scrap()
    {
        $userModel = new UserModel();
        $partModel = new PartNumberSMTModel();
        $ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $MesinSMTModel = new MesinSMTModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeSMTModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinSMTModel->getKategori(); 
        $kategoris2 = $MesinFAModel->getKategori(); 

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;
        $data['kategoris2'] = $kategoris2;

        return view('admnsmt/part_number_scrap', $data);
    }

    public function part_number_scrap_fa()
    {
        $userModel = new UserModelFA();
        $partModel = new PartNumberFAModel();
        $ScrapTypeFAModel = new ScrapTypeFAModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $komponens = $MesinFAModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeFAModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinFAModel->getKategori();  

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['komponens'] = $komponens;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;

        return view('admnfa/part_number_scrap_fa', $data);
    }

    public function part_number_scrap_fa_test()
    {
        $userModel = new UserModelFA();
        $partModel = new PartNumberFAModel();
        $ScrapTypeFAModel = new ScrapTypeFAModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $komponens = $MesinFAModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeFAModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinFAModel->getKategori();  

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['komponens'] = $komponens;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;

        return view('admnfa/part_number_scrap_fa_test', $data);
    }

    public function part_number_scrap_db()
    {
        $userModel = new UserModelFA();
        $partModel = new PartNumberFAModel();
        $ScrapTypeFAModel = new ScrapTypeFAModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $komponens = $MesinFAModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeFAModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinFAModel->getKategori();  

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['komponens'] = $komponens;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;

        return view('admnscrap/part_number_scrap_db', $data);
    }

    public function part_number_scrap_bd()
    {
        $userModel = new UserModel();
        $partModel = new PartNumberSMTModel();
        $ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $MesinSMTModel = new MesinSMTModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $scraptypes = $partModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinSMTModel->getKategori(); 
        $kategoris2 = $MesinFAModel->getKategori(); 

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;
        $data['kategoris2'] = $kategoris2;

        return view('admnscrap/part_number_scrap_bd', $data);
    }

    public function part_number_baru()
    {
        $userModel = new UserModel();
        $partModel = new PartNumberSMTModel();
        $ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $MesinSMTModel = new MesinSMTModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeSMTModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinSMTModel->getKategori(); 

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;

        return view('admnscrap/part_number_baru', $data);
    }

    public function part_number_baru_fa()
    {
        $userModel = new UserModelFA();
        $partModel = new PartNumberFAModel();
        $ScrapTypeFAModel = new ScrapTypeFAModel();
        $MesinFAModel = new MesinFAModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeFAModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris1 = $MesinFAModel->getKategori(); 

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris1'] = $kategoris1;

        return view('admnscrap/part_number_baru_fa', $data);
    }

    public function part_mesin_baru()
    {
        $userModel = new UserModel();
        $partModel = new PartNumberSMTModel();
        $ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $MesinSMTModel = new MesinSMTModel();
        
        $today_entries_wrhs = $userModel->get_today_solder_paste();
        $models = $partModel->getUniqueModels(); 
        $scraptypes = $ScrapTypeSMTModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris = $MesinSMTModel->getKategori(); 

        $data['pageTitle'] = 'Part Number Scrap';
        $data['today_entries_wrhs'] = $today_entries_wrhs;
        $data['models'] = $models;
        $data['scraptypes'] = $scraptypes;
        $data['lines'] = $lines;
        $data['kategoris'] = $kategoris;

        return view('admnscrap/part_mesin_baru', $data);
    }

    public function update_delete_smt()
    {
        $userModel = new UserModel();
        $partModel = new PartNumberSMTModel();
        $ScrapTypeSMTModel = new ScrapTypeSMTModel();
        $MesinSMTModel = new MesinSMTModel();

        $model = $this->request->getGet('model');
        $part_number = $this->request->getGet('part_number');
        $tipe_ng = $this->request->getGet('tipe_ng');
        $line = $this->request->getGet('line');

        $models = $userModel->getFilteredModels($line);
        $part_numbers = $userModel->getFilteredKomponens($line, $model);
        $tipe_ngs = $userModel->getFilteredTipeNGs($line, $model, $part_number);
        
        $today_entries_wrhs = $userModel->get_today_solder_paste(); 
        $scraptypes = $ScrapTypeSMTModel->getScrapType(); 
        $lines = $partModel->getUniqueLines(); 
        $kategoris = $MesinSMTModel->getKategori(); 

        $data = [
            'pageTitle' => 'Part Number Scrap',
            'today_entries_wrhs' => $today_entries_wrhs,
            'models' => $models,
            'part_numbers' => $part_numbers,
            'tipe_ngs' => $tipe_ngs,
            'scraptypes' => $scraptypes,
            'lines' => $lines,
            'kategoris' => $kategoris,
            'scrap_control' => $userModel->orderBy('tgl_bln_thn', 'desc')->findAll(),
        ];

        return view('admnscrap/update_delete_smt', $data);
    }


    public function update_delete_fa()
    {
        $userModel = new UserModelFA();
        $partModel = new PartNumberFAModel();
        $ScrapTypeFAModel = new ScrapTypeFAModel();

        $model = $this->request->getGet('model');
        $komponen = $this->request->getGet('komponen');
        $line = $this->request->getGet('line');

        $models = $userModel->getFilteredModels($line);
        $komponens = $userModel->getFilteredKomponens($line, $model);
        $tipe_ngs = $userModel->getFilteredTipeNGs($line, $model, $komponen);
        
        $today_entries_wrhs = $userModel->get_today_solder_paste(); 
        $lines = $partModel->getUniqueLines(); 

        $data = [
            'pageTitle' => 'Part Number Scrap',
            'today_entries_wrhs' => $today_entries_wrhs,
            'models' => $models,
            'komponens' => $komponens,
            'tipe_ngs' => $tipe_ngs,
            'lines' => $lines,
            'scrap_control' => $userModel->orderBy('tgl_bln_thn', 'desc')->findAll(),
        ];

        return view('admnscrap/update_delete_fa', $data);
    }

    public function getModelByLine($line)
    {
        $models = $this->UserModel->getModelByLine($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getTipeNgByMesin($mesin)
    {
        $tipe_ngs = $this->UserModel->getTipeNgByMesin($mesin);
        return $this->response->setJSON(['tipe_ngs' => $tipe_ngs]);
    }


    public function getModelsByLine($line)
    {
        $models = $this->PartNumberSMTModel->getModelsByLine($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByLineFA($line)
    {
        $models = $this->MesinFAModel->getModelsByLine($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByLineFADB($line)
    {
        $models = $this->UserModelFA->getModelsByLine($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsBySMTL1($line)
    {
        $models = $this->PartNumberSMTModel->getModelsBySMTL1($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsBySMTL2($line)
    {
        $models = $this->PartNumberSMTModel->getModelsBySMTL2($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL1($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL1($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL2($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL2($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL3($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL3($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL4($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL4($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL5($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL5($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFAL6($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFAL6($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getPartNumbersByModelAndLine($model, $line)
    {
        $partNumbers = $this->PartNumberSMTModel->getPartNumbersByModelAndLine($model, $line);
        return $this->response->setJSON(['part_numbers' => $partNumbers]);
    }
    
    public function getPartNumbersByModel($model, $line)
    {
        $partNumbers = $this->UserModel->getPartNumbersByModel($model, $line);
        return $this->response->setJSON(['part_numbers' => $partNumbers]);
    }

    public function getKomponenByModelFA($model)
    {
        $models = $this->MesinFAModel->getKomponenByModel($model);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getPartNumbersByModelAndLineFA($model, $line)
    {
        $komponens = $this->MesinFAModel->getPartNumbersByModelAndLine($model, $line);
        return $this->response->setJSON(['komponens' => $komponens]);
    }

    public function getKomponenByModelAndLineFA($model, $line)
    {
        $komponens = $this->MesinFAModel->getPartNumbersByModelAndLine($model, $line);
        return $this->response->setJSON(['komponens' => $komponens]);
    }

    public function getKomponenByModelAndLineFA_new($model, $line)
    {
        $komponens = $this->PartNumberKompModel->getPartNumbersByModelAndLine($model, $line);
        return $this->response->setJSON(['komponens' => $komponens]);
    }
        

    public function getPartNumbersByModelAndLineFADB($model, $line)
    {
        $komponens = $this->UserModelFA->getPartNumbersByModelAndLine($model, $line);
        return $this->response->setJSON(['komponens' => $komponens]);
    }

    public function getModelsByFALLine($line)
    {
        $models = $this->PartNumberSMTModel->getModelsByFALLine($line);
        return $this->response->setJSON(['models' => $models]);
    }

    public function getModelsByFALLineFA($line)
    {
        $models = $this->PartNumberFAModel->getModelsByFALLine($line);
        return $this->response->setJSON(['models' => $models]);
    }


    public function getKategori($kategori)
    {
        $tipeNg = $this->MesinSMTModel->where('kategori', $kategori)->findAll();
        return $this->response->setJSON($tipeNg);
    }

    public function getTipeNgByKategori($kategori)
    {
        $model = new MesinSMTModel();
        $tipeNg = $model->where('kategori', $kategori)->findAll();
        return $this->response->setJSON(['tipe_ng' => $tipeNg]);
    }


    public function getTipeNgByKomponen($komponen)
    {
        $tipe_ngs = $this->MesinFAModel->getTipeNgByKomponen($komponen);
        return $this->response->setJSON(['tipe_ngs' => $tipe_ngs]);
    }

    public function getTipeNgByKomponenDB($komponen)
    {
        $tipe_ngs = $this->UserModelFA->getTipeNgByKomponen($komponen);
        return $this->response->setJSON(['tipe_ngs' => $tipe_ngs]);
    }

    public function save_temp_data()
    {
        try {
            $requestData = $this->request->getJSON();
            $tempData = $requestData->tempData;

            if (!is_array($tempData)) {
                throw new \Exception('Invalid data format');
            }

            if (empty($tempData)) {
                throw new \Exception('No data to save');
            }

            $userModel = new UserModel();
            $errors = [];
            
            foreach ($tempData as $entry) {
                $search_key = $entry->lot_number . $entry->id;
                
                // Check if search_key already exists
                if ($userModel->searchKeyExists($search_key)) {
                    $errors[] = "Search Key '{$search_key}' already exists.";
                    continue;
                }
                
                date_default_timezone_set('Asia/Jakarta');
                $insertData = [
                    'lot_number' => $entry->lot_number,
                    'id' => $entry->id,
                    'incoming' => date('Y-m-d H:i:s')
                ];
                $userModel->insertData($insertData);
                $userModel->updateSearchKey($entry->lot_number, $entry->id);
            }

            if (!empty($errors)) {
                $response = ['message' => implode(' ', $errors)];
                return $this->response->setStatusCode(400)->setJSON($response);
            }

            $response = ['message' => 'Data saved successfully.'];
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            $response = ['message' => 'Failed to save data: ' . $e->getMessage()];
            return $this->response->setStatusCode(500)->setJSON($response);
        }
    }


    public function check_duplicate()
    {
        try {
            $requestData = $this->request->getJSON();
            $entry = (array) $requestData;

            if (!isset($entry['lot_number']) && !isset($entry['id'])) {
                throw new \Exception('Invalid request');
            }

            $userModel = new UserModel();
            $query = $userModel->where('lot_number', $entry['lot_number'])
                            ->orWhere('id', $entry['id']);

            $isDuplicate = $query->countAllResults() > 0;

            $response = ['isDuplicate' => $isDuplicate];
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            $response = ['message' => 'Failed to check duplicates: ' . $e->getMessage()];
            return $this->response->setStatusCode(500)->setJSON($response);
        }
    }

    public function export_to_excel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Lot Number');

        $userModel = new UserModel();
        $data = $userModel->get_today_return();

        $row = 2;
        foreach ($data as $entry) {
            $sheet->setCellValue('A' . $row, $entry['id']);
            $sheet->setCellValue('B' . $row, $entry['lot_number']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filePath = WRITEPATH . 'exports/Return_Data_' . date('Ymd_His') . '.xlsx';
        
        if (!is_dir(WRITEPATH . 'exports')) {
            mkdir(WRITEPATH . 'exports', 0777, true);
        }

        try {
            $writer->save($filePath);
            return $this->response->download($filePath, null)->setFileName('Return_Data_' . date('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function search_key_offprod()
    {
        $request = service('request');
        $searchTerm = $request->getGet('term');

        if ($searchTerm) {
            $userModel = new UserModel();
            $results = $userModel->search_key_offprod($searchTerm);

            return $this->response->setJSON($results);
        }

        return $this->response->setJSON([]);
    }

    public function submitScrapControl()
    {
        $rules = [
            'tgl_bln_thn' => 'required|valid_date',
            'shift' => 'required|is_natural',
            'line' => 'required',
            'model' => 'required',
            'part_number' => 'required',
            'scraptype' => 'required',
            'kategori' => 'required',
            'tipe_ng' => 'required',
            'remarks' => 'permit_empty|string',
            'qty' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'part_number' => $this->request->getPost('part_number'),
            'scraptype' => $this->request->getPost('scraptype'),
            'mesin' => $this->request->getPost('kategori'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];

        if ($this->UserModel->insert($data)) {
            session()->set('form_data', $data);

            session()->setFlashdata('success', 'Data Berhasil Disimpan.');
            return redirect()->to('admnsmt/part_number_scrap');
        } else {
            session()->setFlashdata('error', 'Data Gagal Untuk Disimpan.');
            return redirect()->back()->withInput();
        }
    }


    public function submitScrapControlFA()
    {
        $rules = [
            'tgl_bln_thn' => 'required|valid_date',
            'shift' => 'required|is_natural',
            'line' => 'required',
            'model' => 'required',
            'komponen' => 'required',
            'part_number' => 'required',
            'tipe_ng' => 'required',
            'remarks' => 'permit_empty|string',
            'qty' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'komponen' => $this->request->getPost('komponen'),
            'part_number' => $this->request->getPost('part_number'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];

        if ($this->UserModelFA->insert($data)) {
            session()->set('form_data', $data);

            session()->setFlashdata('success', 'Data Berhasil Disimpan.');
            return redirect()->to('admnfa/part_number_scrap_fa');
        } else {
            session()->setFlashdata('error', 'Data Gagal Untuk Disimpan.');
            return redirect()->back()->withInput();
        }
    }

    public function submitScrapControl_bd()
    {
        $rules = [
            'tgl_bln_thn' => 'required|valid_date',
            'shift' => 'required|is_natural',
            'line' => 'required',
            'model' => 'required',
            'part_number' => 'required',
            'scraptype' => 'required',
            'kategori' => 'required',
            'tipe_ng' => 'required',
            'remarks' => 'permit_empty|string',
            'qty' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'part_number' => $this->request->getPost('part_number'),
            'scraptype' => $this->request->getPost('scraptype'),
            'mesin' => $this->request->getPost('kategori'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];  

        $partNumberSMTModel = new PartNumberSMTModel();
            $hargaData = $partNumberSMTModel->where([
                'model' => $data['model'],
                'line' => $data['line'],
                'part_number' => $data['part_number'],
                'scraptype' => $data['scraptype']
            ])->first();

        if ($hargaData) {
            $total_harga = $hargaData['harga'] * $data['qty'];
    
            $data['total_harga'] = $total_harga;
    
            $userModel = new UserModel();
            if ($userModel->insert($data)) {
                session()->set('form_data', $data);
                session()->setFlashdata('success', 'Data Berhasil Disimpan.');
                return redirect()->to('admnscrap/part_number_scrap_bd');
            } else {
                session()->setFlashdata('error', 'Data Gagal Untuk Disimpan.');
                return redirect()->back()->withInput();
            }
        } else {
            session()->setFlashdata('error', 'Harga untuk Part Number tidak ditemukan.');
            return redirect()->back()->withInput();
        }
    }

    public function submitScrapControlFA_db()
    {
        $rules = [
            'tgl_bln_thn' => 'required|valid_date',
            'shift' => 'required|is_natural',
            'line' => 'required',
            'model' => 'required',
            'part_number' => 'required',
            'komponen' => 'required',
            'tipe_ng' => 'required',
            'remarks' => 'permit_empty|string',
            'qty' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'part_number' => $this->request->getPost('part_number'),
            'komponen' => $this->request->getPost('komponen'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];

        $partNumberKompModel = new PartNumberKompModel();
            $hargaData = $partNumberKompModel->where([
                'model' => $data['model'],
                'komponen' => $data['komponen'],
                'part_number' => $data['part_number']
            ])->first();

        if ($hargaData) {
            $total_harga = $hargaData['harga'] * $data['qty'];
    
            $data['total_harga'] = $total_harga;
    
            $userModelFA = new UserModelFA();
            if ($userModelFA->insert($data)) {
                session()->set('form_data', $data);
                session()->setFlashdata('success', 'Data Berhasil Disimpan.');
                return redirect()->to('admnscrap/part_number_scrap_db');
            } else {
                session()->setFlashdata('error', 'Data Gagal Untuk Disimpan.');
                return redirect()->back()->withInput();
            }
        } else {
            session()->setFlashdata('error', 'Harga untuk part_number tidak ditemukan.');
            return redirect()->back()->withInput();
        }
    }

    public function submitAddModel()
    {
        $rules = [
            'line' => 'required',
            'model' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error_model', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
        ];

        $PartNumberSMTModel = new PartNumberSMTModel();

        if ($PartNumberSMTModel->insert($data)) {
            session()->setFlashdata('success_model', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_model', 'Data Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnscrap/part_number_baru');
    }

    public function submitPartNumber()
    {
        $rules = [
            'line' => 'required',
            'model' => 'required',
            'part_number' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error_part_number', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'part_number' => $this->request->getPost('part_number'),
        ];

        $PartNumberSMTModel = new PartNumberSMTModel();

        if ($PartNumberSMTModel->insert($data)) {
            session()->setFlashdata('success_part_number', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_part_number', 'Data Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnscrap/part_number_baru');
    }

    public function submitAddMesinSMT()
    {
        $rules = [
            'kategori' => 'required',
            'tipe_ng' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error_mesin_smt', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'kategori' => $this->request->getPost('kategori'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
        ];

        $MesinSMTModel = new MesinSMTModel();

        if ($MesinSMTModel->insert($data)) {
            session()->setFlashdata('success_mesin_smt', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_mesin_smt', 'Data Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnscrap/part_mesin_baru');
    }

    public function submitAddMesinFA()
    {
        $rules = [
            'komponen' => 'required',
            'tipe_ng' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error_mesin_smt', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'komponen' => $this->request->getPost('komponen'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
        ];

        $MesinFAModel = new MesinFAModel();

        if ($MesinFAModel->insert($data)) {
            session()->setFlashdata('success_mesin_smt', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_mesin_smt', 'Data Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnfa/part_mesin_baru_fa');
    }

    public function submitAddModelFA()
    {
        $rules = [
            'model' => 'required',
            'komponen' => 'required|max_length[35]',
            'part_number' => 'required|max_length[40]',
        ];

        if (!$this->validate($rules)) {
            $validationErrors = $this->validator->getErrors();
            session()->setFlashdata('error_model', implode(', ', $validationErrors));
            return redirect()->back()->withInput();
        }

        $model = $this->request->getPost('model');
        $komponen = $this->request->getPost('komponen');
        $part_number = $this->request->getPost('part_number');
        $PartNumbKomp = new PartNumberKompModel();

        $partNumberData = [
            'model' => $model,
            'komponen' => $komponen,
            'part_number' => $part_number,
        ];

        if ($PartNumbKomp->insert($partNumberData)) {
            session()->setFlashdata('success_model', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_model', 'Data Part Number Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnscrap/part_number_baru_fa');
    }
    
    public function submitAddTipeNGFA()
    {
        $rules = [
            'line' => 'required',
            'model' => 'required',
            'komponen' => 'required',
            'part_number' => 'required',
            'tipe_ng' => 'required',
        ];
    
        if (!$this->validate($rules)) {
            session()->setFlashdata('error_tipe_ng', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }
    
        $line = $this->request->getPost('line');
        $model = $this->request->getPost('model');
        $komponen = $this->request->getPost('komponen');
        $part_number = $this->request->getPost('part_number');
        $tipe_ng = $this->request->getPost('tipe_ng');
    
        $MesinFAModel = new MesinFAModel();
        $PartNumbKomp = new PartNumberKompModel();
    
        $existingMesin = $MesinFAModel->where(['line' => $line, 'model' => $model, 'komponen' => $komponen])->first();
    
        if ($existingMesin) {
            $MesinFAModel->update($existingMesin['id_mesin'], ['tipe_ng' => $tipe_ng]);
            session()->setFlashdata('success_tipe_ng', 'Tipe NG updated successfully.');
        } else {
            $mesinData = [
                'line' => $line,
                'model' => $model,
                'komponen' => $komponen,
                'tipe_ng' => $tipe_ng,
            ];
            
            $MesinFAModel->insert($mesinData);
            session()->setFlashdata('success_tipe_ng', 'Data Mesin berhasil disimpan.');
        }
    
        $existingPartNumber = $PartNumbKomp->where(['komponen' => $komponen, 'part_number' => $part_number])->first();
    
        if (!$existingPartNumber) {
            $partNumberData = [
                'komponen' => $komponen,
                'part_number' => $part_number,
            ];
            
            if ($PartNumbKomp->insert($partNumberData)) {
                session()->setFlashdata('success_tipe_ng', 'Part Number berhasil disimpan.');
            } else {
                session()->setFlashdata('error_tipe_ng', 'Gagal menyimpan Part Number.');
            }
        } else {
            session()->setFlashdata('info_tipe_ng', 'Part Number sudah ada, tidak perlu disimpan.');
        }
    
        return redirect()->to('admnscrap/part_number_baru_fa');
    }    

    public function submitPartNumberFA()
    {
        $rules = [
            'line' => 'required',
            'model' => 'required',
            'part_number' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error_part_number', 'Validation failed. Please check your input.');
            return redirect()->back()->withInput();
        }

        $data = [
            'line' => $this->request->getPost('line'),
            'model' => $this->request->getPost('model'),
            'part_number' => $this->request->getPost('part_number'),
        ];

        $PartNumberFAModel = new PartNumberFAModel();

        if ($PartNumberFAModel->insert($data)) {
            session()->setFlashdata('success_part_number', 'Data Berhasil Disimpan.');
        } else {
            session()->setFlashdata('error_part_number', 'Data Gagal Untuk Disimpan.');
        }

        return redirect()->to('admnfa/part_number_baru_fa');
    }


    public function chartData()
    {
        $userModel = new UserModel();
        $data = $userModel->getMonthlyData();
    
        $chartData = [];
        foreach ($data as $row) {
            $date = date('d', strtotime($row['tgl_bln_thn']));
            if (!isset($chartData[$date])) {
                $chartData[$date] = 0;
            }
            $chartData[$date] += $row['qty'];
        }
    
        return json_encode($chartData);
    }

    public function clearSavedData()
    {
        session()->remove('saved_data');
        return $this->response->setJSON(['status' => 'success']);
    }

    public function exportExcelSMT()
    {
        $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    $model = $this->request->getGet('model');
    $mesin = $this->request->getGet('mesin');
    $part_number = $this->request->getGet('part_number');
    $scraptype = $this->request->getGet('scraptype');
    $tipe_ng = $this->request->getGet('tipe_ng');
    $line = $this->request->getGet('line');

    $data = $this->UserModel->getFilteredScrapDataExcel($startDate, $endDate, $model, $mesin, $tipe_ng, $line, $scraptype, $part_number);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Model');
    $sheet->setCellValue('B1', 'Line');
    $sheet->setCellValue('C1', 'Part Number');
    $sheet->setCellValue('D1', 'Date');
    $sheet->setCellValue('E1', 'Shift');
    $sheet->setCellValue('F1', 'Scrap Type');
    $sheet->setCellValue('G1', 'Mesin');
    $sheet->setCellValue('H1', 'Tipe NG');
    $sheet->setCellValue('I1', 'Remarks');
    $sheet->setCellValue('J1', 'Qty NG');
    
    $rowNum = 2;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNum, $row['model'] ?? '');
        $sheet->setCellValue('B' . $rowNum, $row['line'] ?? '');
        $sheet->setCellValue('C' . $rowNum, $row['part_number'] ?? '');
        $sheet->setCellValue('D' . $rowNum, $row['date'] ?? '');
        $sheet->setCellValue('E' . $rowNum, $row['shift'] ?? '');
        $sheet->setCellValue('F' . $rowNum, $row['scraptype'] ?? '');
        $sheet->setCellValue('G' . $rowNum, $row['mesin'] ?? '');
        $sheet->setCellValue('H' . $rowNum, $row['tipe_ng'] ?? ''); 
        $sheet->setCellValue('I' . $rowNum, $row['remarks'] ?? '');
        $sheet->setCellValue('J' . $rowNum, $row['qty'] ?? '');
        $rowNum++;
    }    

    $writer = new Xlsx($spreadsheet);
    $filename = 'rekap_data_scrap_smt.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    }

    public function exportExcelFA()
    {
        $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    $model = $this->request->getGet('model');
    $komponen = $this->request->getGet('komponen');
    $tipe_ng = $this->request->getGet('tipe_ng');
    $line = $this->request->getGet('line');

    $data = $this->UserModelFA->getFilteredScrapDataExcel($startDate, $endDate, $model, $komponen, $tipe_ng, $line);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Model');
    $sheet->setCellValue('B1', 'Line');
    $sheet->setCellValue('C1', 'Date');
    $sheet->setCellValue('D1', 'Shift');
    $sheet->setCellValue('E1', 'Komponen');
    $sheet->setCellValue('F1', 'Tipe NG');
    $sheet->setCellValue('G1', 'Remarks');
    $sheet->setCellValue('H1', 'Qty NG');
    
    $rowNum = 2;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNum, $row['model'] ?? '');
        $sheet->setCellValue('B' . $rowNum, $row['line'] ?? '');
        $sheet->setCellValue('C' . $rowNum, $row['date'] ?? '');
        $sheet->setCellValue('D' . $rowNum, $row['shift'] ?? '');
        $sheet->setCellValue('E' . $rowNum, $row['komponen'] ?? '');
        $sheet->setCellValue('F' . $rowNum, $row['tipe_ng'] ?? ''); 
        $sheet->setCellValue('G' . $rowNum, $row['remarks'] ?? '');
        $sheet->setCellValue('H' . $rowNum, $row['qty'] ?? '');
        $rowNum++;
    }    

    $writer = new Xlsx($spreadsheet);
    $filename = 'rekap_data_scrap_fa.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    }

    public function getPartNumbersByKomponenFA($model, $komponen)
    {
        $partNumbers = $this->PartNumberKompModel->getPartNumbersByKomponen($model, $komponen);
        return $this->response->setJSON(['part_numbers' => $partNumbers]);
    }

    public function getPartNumbersByKomponen($model, $komponen)
    {
        $partNumbers = $this->UserModelFA->getPartNumbersByModelAndKomponen($model, $komponen);
        return $this->response->setJSON(['part_numbers' => $partNumbers]);
    }   
    

    public function get_record()
    {
        $id = $this->request->getPost('id');
        $userModel = new UserModel();
        $data = $userModel->find($id);
        return $this->response->setJSON($data);
    }

    public function update_record()
    {
        $id = $this->request->getPost('id');
        $data = [
            'model' => $this->request->getPost('model'),
            'line' => $this->request->getPost('line'),
            'part_number' => $this->request->getPost('part_number'),
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'scraptype' => $this->request->getPost('scraptype'),
            'mesin' => $this->request->getPost('mesin'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];

        $userModel = new UserModel();
        if ($userModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    public function delete_record()
    {
        $id = $this->request->getPost('id');
        $userModel = new UserModel();
        if ($userModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    public function get_recordfa()
    {
        $id = $this->request->getPost('id');
        $userModel = new UserModelFA();
        $data = $userModel->find($id);
        return $this->response->setJSON($data);
    }

    public function update_recordfa()
    {
        $id = $this->request->getPost('id');
        $data = [
            'model' => $this->request->getPost('model'),
            'line' => $this->request->getPost('line'),
            'komponen' => $this->request->getPost('komponen'),
            'part_number' => $this->request->getPost('part_number'),
            'tgl_bln_thn' => $this->request->getPost('tgl_bln_thn'),
            'shift' => $this->request->getPost('shift'),
            'scraptype' => $this->request->getPost('scraptype'),
            'tipe_ng' => $this->request->getPost('tipe_ng'),
            'remarks' => $this->request->getPost('remarks'),
            'qty' => $this->request->getPost('qty'),
        ];

        $userModel = new UserModelFA();
        if ($userModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    public function delete_recordfa()
    {
        $id = $this->request->getPost('id');
        $userModel = new UserModelFA();
        if ($userModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error']);
        }
    }




}
