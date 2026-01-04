<?php

namespace App\Helpers;

class BusinessHoursHelper
{
    /**
     * Verifica se a loja está aberta no momento
     * 
     * @param array $store Dados da loja com business_hours e is_open
     * @return array ['is_open' => bool, 'message' => string, 'next_opening' => string|null]
     */
    public static function isStoreOpen(array $store): array
    {
        // Se a loja está manualmente fechada
        if (isset($store['is_open']) && !$store['is_open']) {
            return [
                'is_open' => false,
                'message' => $store['closed_message'] ?? 'No momento estamos fechados. Volte em breve!',
                'next_opening' => null
            ];
        }

        // Se não há horários configurados, assume que está sempre aberto
        if (empty($store['business_hours'])) {
            return [
                'is_open' => true,
                'message' => 'Aberto agora',
                'next_opening' => null
            ];
        }

        // Decodifica os horários se for string JSON
        $businessHours = is_string($store['business_hours']) 
            ? json_decode($store['business_hours'], true) 
            : $store['business_hours'];

        if (!$businessHours) {
            return [
                'is_open' => true,
                'message' => 'Aberto agora',
                'next_opening' => null
            ];
        }

        // Obter dia e hora atuais
        $now = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        // Mapear nomes dos dias em inglês para português
        $dayMap = [
            'monday' => 'segunda',
            'tuesday' => 'terça',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sábado',
            'sunday' => 'domingo'
        ];

        // Verificar se hoje está habilitado e dentro do horário
        if (isset($businessHours[$currentDay])) {
            $todayHours = $businessHours[$currentDay];
            
            // Se o dia está desabilitado
            if (!$todayHours['enabled']) {
                $nextOpening = self::getNextOpening($businessHours, $now);
                return [
                    'is_open' => false,
                    'message' => 'Fechado hoje',
                    'next_opening' => $nextOpening
                ];
            }

            // Verificar se está dentro do horário
            if ($currentTime >= $todayHours['open'] && $currentTime < $todayHours['close']) {
                return [
                    'is_open' => true,
                    'message' => sprintf('Aberto até às %s', $todayHours['close']),
                    'next_opening' => null
                ];
            } else {
                $nextOpening = self::getNextOpening($businessHours, $now);
                $message = $currentTime < $todayHours['open'] 
                    ? sprintf('Abrimos hoje às %s', $todayHours['open'])
                    : 'Fechado agora';
                    
                return [
                    'is_open' => false,
                    'message' => $message,
                    'next_opening' => $nextOpening
                ];
            }
        }

        // Fallback: assumir que está aberto
        return [
            'is_open' => true,
            'message' => 'Aberto agora',
            'next_opening' => null
        ];
    }

    /**
     * Retorna a próxima abertura da loja
     */
    private static function getNextOpening(array $businessHours, \DateTime $now): ?string
    {
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $currentDayIndex = (int)$now->format('N') - 1; // 0 = Monday, 6 = Sunday
        
        // Procurar pelos próximos 7 dias
        for ($i = 1; $i <= 7; $i++) {
            $nextDayIndex = ($currentDayIndex + $i) % 7;
            $nextDay = $daysOfWeek[$nextDayIndex];
            
            if (isset($businessHours[$nextDay]) && $businessHours[$nextDay]['enabled']) {
                $dayNames = [
                    'segunda-feira', 'terça-feira', 'quarta-feira', 
                    'quinta-feira', 'sexta-feira', 'sábado', 'domingo'
                ];
                
                if ($i == 1) {
                    return sprintf('Abrimos amanhã às %s', $businessHours[$nextDay]['open']);
                } else {
                    return sprintf('Abrimos na %s às %s', 
                        $dayNames[$nextDayIndex], 
                        $businessHours[$nextDay]['open']
                    );
                }
            }
        }
        
        return null;
    }

    /**
     * Retorna horários formatados para exibição
     */
    public static function getFormattedHours(array $store): array
    {
        if (empty($store['business_hours'])) {
            return [];
        }

        $businessHours = is_string($store['business_hours']) 
            ? json_decode($store['business_hours'], true) 
            : $store['business_hours'];

        if (!$businessHours) {
            return [];
        }

        $dayNames = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];

        $formatted = [];
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($businessHours[$day])) {
                $hours = $businessHours[$day];
                $formatted[] = [
                    'day' => $dayNames[$day],
                    'enabled' => $hours['enabled'],
                    'hours' => $hours['enabled'] 
                        ? sprintf('%s - %s', $hours['open'], $hours['close'])
                        : 'Fechado'
                ];
            }
        }

        return $formatted;
    }
}
