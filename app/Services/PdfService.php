<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Serviço de geração de PDF
 * 
 * @package App\Services
 */
class PdfService
{
    private Dompdf $dompdf;
    
    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $this->dompdf = new Dompdf($options);
    }
    
    /**
     * Gera PDF a partir de HTML
     * 
     * @param string $html
     * @param string $filename
     * @param bool $download
     */
    public function generate(string $html, string $filename = 'document.pdf', bool $download = true): void
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        $this->dompdf->stream($filename, ['Attachment' => $download]);
    }
}
