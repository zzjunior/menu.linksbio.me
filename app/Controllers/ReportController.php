<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Services\TemplateService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReportController
{
    protected $reportModel;
    protected $userModel;
    protected $templateService;

    public function __construct(Report $reportModel, User $userModel, TemplateService $templateService)
    {
        $this->reportModel = $reportModel;
        $this->userModel = $userModel;
        $this->templateService = $templateService;
    }

    /**
     * Dashboard principal de relatórios
     */
    public function dashboard(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $date = $queryParams['date'] ?? date('Y-m-d');
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $storeId = $user['store_id'];
        
        // Relatório do dia
        $dailyReport = $this->reportModel->getDailyFinancialReport($date, $storeId);
        
        // Relatório da semana
        $weeklyReport = $this->reportModel->getWeeklyFinancialReport(null, $storeId);
        
        // Produtos mais vendidos (últimos 7 dias)
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        $topProducts = $this->reportModel->getTopProducts($startDate, $endDate, 5, $storeId);
        
        // Vendas por hora (últimos 7 dias)
        $hourlyData = $this->reportModel->getSalesByHour($startDate, $endDate, $storeId);
        
        // Comparação com período anterior
        $previousStartDate = date('Y-m-d', strtotime('-14 days'));
        $previousEndDate = date('Y-m-d', strtotime('-7 days'));
        $comparison = $this->reportModel->comparePeriodsReport($startDate, $endDate, $previousStartDate, $previousEndDate, $storeId);
        
        $data = [
            'pageTitle' => 'Relatórios Financeiros',
            'selectedDate' => $date,
            'dailyReport' => $dailyReport,
            'weeklyReport' => $weeklyReport,
            'topProducts' => $topProducts,
            'hourlyData' => $hourlyData,
            'comparison' => $comparison
        ];

        $html = $this->templateService->render('admin.reports.dashboard', $data);
        $response->getBody()->write($html);
        return $response;
    }

    /**
     * Relatório diário específico
     */
    public function dailyReport(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $date = $queryParams['date'] ?? date('Y-m-d');
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $storeId = $user['store_id'];
        
        $report = $this->reportModel->getDailyFinancialReport($date, $storeId);
        $topProducts = $this->reportModel->getTopProducts($date, $date, 10, $storeId);
        $hourlyData = $this->reportModel->getSalesByHour($date, $date, $storeId);
        
        // Comparação com dia anterior
        $previousDate = date('Y-m-d', strtotime($date . ' -1 day'));
        $comparison = $this->reportModel->comparePeriodsReport($date, $date, $previousDate, $previousDate, $storeId);
        
        $data = [
            'pageTitle' => 'Relatório Diário - ' . date('d/m/Y', strtotime($date)),
            'selectedDate' => $date,
            'report' => $report,
            'topProducts' => $topProducts,
            'hourlyData' => $hourlyData,
            'comparison' => $comparison
        ];

        $html = $this->templateService->render('admin.reports.daily', $data);
        $response->getBody()->write($html);
        return $response;
    }

    /**
     * API para dados de gráficos (JSON)
     */
    public function getChartData(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $type = $queryParams['type'] ?? 'daily';
        $startDate = $queryParams['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $queryParams['end_date'] ?? date('Y-m-d');
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        $storeId = $user['store_id'];
        
        $data = [];
        
        switch ($type) {
            case 'weekly':
                $data = $this->reportModel->getWeeklyFinancialReport($startDate, $storeId);
                break;
            case 'hourly':
                $data = $this->reportModel->getSalesByHour($startDate, $endDate, $storeId);
                break;
            case 'daily':
            default:
                $data = [$this->reportModel->getDailyFinancialReport($startDate, $storeId)];
                break;
        }
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}