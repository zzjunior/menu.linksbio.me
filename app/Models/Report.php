<?php

namespace App\Models;

class Report extends BaseModel
{
    /**
     * Relatório financeiro diário
     */
    public function getDailyFinancialReport($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $sql = "
            SELECT 
                DATE(o.created_at) as date,
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as gross_revenue,
                SUM(o.total_amount) * 0.7 as estimated_profit, -- Assumindo 30% de custo
                AVG(o.total_amount) as avg_order_value,
                COUNT(DISTINCT COALESCE(o.customer_id, o.customer_phone)) as unique_customers,
                SUM(CASE WHEN o.status = 'completed' THEN o.total_amount ELSE 0 END) as completed_revenue,
                SUM(CASE WHEN o.status = 'pending' THEN o.total_amount ELSE 0 END) as pending_revenue,
                SUM(CASE WHEN o.status = 'cancelled' THEN o.total_amount ELSE 0 END) as cancelled_revenue
            FROM orders o
            WHERE DATE(o.created_at) = ?
            GROUP BY DATE(o.created_at)
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$date]);
        $report = $result->fetchAssociative();
        
        if (!$report) {
            return [
                'date' => $date,
                'total_orders' => 0,
                'gross_revenue' => 0,
                'estimated_profit' => 0,
                'avg_order_value' => 0,
                'unique_customers' => 0,
                'completed_revenue' => 0,
                'pending_revenue' => 0,
                'cancelled_revenue' => 0
            ];
        }
        
        return $report;
    }

    /**
     * Relatório financeiro semanal
     */
    public function getWeeklyFinancialReport($startDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('monday this week'));
        }
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        
        $sql = "
            SELECT 
                DATE(o.created_at) as date,
                DAYNAME(o.created_at) as day_name,
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as gross_revenue,
                SUM(o.total_amount) * 0.7 as estimated_profit,
                AVG(o.total_amount) as avg_order_value
            FROM orders o
            WHERE DATE(o.created_at) BETWEEN ? AND ?
                AND o.status != 'cancelled'
            GROUP BY DATE(o.created_at)
            ORDER BY DATE(o.created_at)
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$startDate, $endDate]);
        return $result->fetchAllAssociative();
    }

    /**
     * Relatório financeiro mensal  
     */
    public function getMonthlyFinancialReport($year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $sql = "
            SELECT 
                DAY(o.created_at) as day,
                DATE(o.created_at) as date,
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as gross_revenue,
                SUM(o.total_amount) * 0.7 as estimated_profit,
                AVG(o.total_amount) as avg_order_value
            FROM orders o
            WHERE YEAR(o.created_at) = ? 
                AND MONTH(o.created_at) = ?
                AND o.status != 'cancelled'
            GROUP BY DATE(o.created_at)
            ORDER BY DATE(o.created_at)
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$year, $month]);
        return $result->fetchAllAssociative();
    }

    /**
     * Relatório de produtos mais vendidos em um período
     */
    public function getTopProducts($startDate, $endDate, $limit = 10)
    {
        $sql = "
            SELECT 
                p.name as product_name,
                p.image as product_image,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.unit_price) as total_revenue,
                COUNT(DISTINCT o.id) as orders_count,
                AVG(oi.unit_price) as avg_price
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
                AND o.status != 'cancelled'
            GROUP BY p.id, p.name, p.image
            ORDER BY total_revenue DESC
            LIMIT " . (int)$limit . "
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$startDate, $endDate]);
        return $result->fetchAllAssociative();
    }

    /**
     * Resumo financeiro de um período personalizado
     */
    public function getCustomPeriodReport($startDate, $endDate)
    {
        $sql = "
            SELECT 
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as gross_revenue,
                SUM(o.total_amount) * 0.7 as estimated_profit,
                AVG(o.total_amount) as avg_order_value,
                COUNT(DISTINCT COALESCE(o.customer_id, o.customer_phone)) as unique_customers,
                MIN(o.created_at) as first_order,
                MAX(o.created_at) as last_order,
                SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            FROM orders o
            WHERE DATE(o.created_at) BETWEEN ? AND ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$startDate, $endDate]);
        return $result->fetchAssociative();
    }

    /**
     * Vendas por hora do dia
     */
    public function getSalesByHour($startDate, $endDate)
    {
        $sql = "
            SELECT 
                HOUR(o.created_at) as hour,
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as total_revenue
            FROM orders o
            WHERE DATE(o.created_at) BETWEEN ? AND ?
                AND o.status != 'cancelled'
            GROUP BY HOUR(o.created_at)
            ORDER BY hour
        ";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->executeQuery([$startDate, $endDate]);
        return $result->fetchAllAssociative();
    }

    /**
     * Comparação entre dois períodos
     */
    public function comparePeriodsReport($currentStart, $currentEnd, $previousStart, $previousEnd)
    {
        $current = $this->getCustomPeriodReport($currentStart, $currentEnd);
        $previous = $this->getCustomPeriodReport($previousStart, $previousEnd);
        
        return [
            'current' => $current,
            'previous' => $previous,
            'revenue_change' => $this->calculatePercentageChange($previous['gross_revenue'], $current['gross_revenue']),
            'orders_change' => $this->calculatePercentageChange($previous['total_orders'], $current['total_orders']),
            'avg_order_change' => $this->calculatePercentageChange($previous['avg_order_value'], $current['avg_order_value']),
            'customers_change' => $this->calculatePercentageChange($previous['unique_customers'], $current['unique_customers'])
        ];
    }

    /**
     * Calcular mudança percentual
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }
}