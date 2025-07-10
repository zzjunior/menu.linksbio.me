<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Serviço simples para renderização de templates
 */
class TemplateService
{
    private string $templatePath;

    public function __construct(string $templatePath = null)
    {
        $this->templatePath = $templatePath ?? __DIR__ . '/../../templates/';
    }

    /**
     * Renderiza um template com dados
     */
    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->templatePath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \Exception("Template not found: {$templateFile}");
        }

        // Extrai variáveis do array de dados
        extract($data);

        // Inicia buffer de saída
        ob_start();
        
        // Inclui o template
        include $templateFile;
        
        // Retorna o conteúdo capturado
        return ob_get_clean();
    }

    /**
     * Renderiza um template e escreve no response
     */
    public function renderResponse($response, string $template, array $data = [])
    {
        $html = $this->render($template, $data);
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }

    /**
     * Renderiza um layout com conteúdo
     */
    public function renderWithLayout(string $layout, string $content, array $data = []): string
    {
        $data['content'] = $content;
        return $this->render($layout, $data);
    }

    /**
     * Escapa HTML para segurança
     */
    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formata preço em real brasileiro
     */
    public function formatPrice(float $price): string
    {
        return 'R$ ' . number_format($price, 2, ',', '.');
    }
}
