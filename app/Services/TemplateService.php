<?php

declare(strict_types=1);

namespace App\Services;

use duncan3dc\Laravel\BladeInstance;


class TemplateService
{
    private BladeInstance $blade;

    public function __construct(string $templatePath = null, string $cachePath = null)
    {
        $views = $templatePath ?? __DIR__ . '/../../views/templates';
        $cache = $cachePath ?? __DIR__ . '/../../storage/cache/blade';
        $this->blade = new BladeInstance($views, $cache);
    }

    public function render(string $template, array $data = []): string
    {
        return $this->blade->render($template, $data);
    }

    public function renderResponse($response, $template, $data = [])
    {
        $html = $this->render($template, $data);
        $response->getBody()->write($html);
        return $response;
    }

    public function renderWithLayout(string $layout, string $content, array $data = []): string
    {
        $data['content'] = $content;
        return $this->render($layout, $data);
    }

    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function formatPrice(float $price): string
    {
        return 'R$ ' . number_format($price, 2, ',', '.');
    }
}