<?php

namespace App\Services;

use Barryvdh\DomPDF\PDF;
use Dompdf\Canvas;
use Dompdf\FontMetrics;

class ShipmentPdfCompanyFooter
{
    /**
     * Render the PDF and stamp the MarineCaddie company footer on every page.
     */
    public function output(PDF $pdf, string $createdAt): string
    {
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $size = 7.0;
        $lineHeight = 9.0;
        $marginX = 28.35; // ~10mm
        $bottomOffset = 36.0;

        $leftLines = [
            'MarineCaddie India Private Limited',
            'Innov8 Aerocity, Asset-5A, Hospitality District',
            'Near IGI Airport, Aerocity, New Delhi-110037.',
        ];
        $rightLine1 = '+919560773375 ops@marinecaddie.com';
        $rightLine2 = 'Created on ' . $createdAt;

        $canvas->page_script(function (int $pageNumber, int $pageCount, Canvas $canvas, FontMetrics $fontMetrics) use (
            $font,
            $size,
            $lineHeight,
            $marginX,
            $bottomOffset,
            $leftLines,
            $rightLine1,
            $rightLine2
        ) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            $y = $height - $bottomOffset;

            foreach ($leftLines as $index => $line) {
                $canvas->text($marginX, $y + ($index * $lineHeight), $line, $font, $size);
            }

            $pageLabel = $pageNumber . '/' . $pageCount;
            $pageLabelWidth = $fontMetrics->getTextWidth($pageLabel, $font, $size);
            $canvas->text(($width - $pageLabelWidth) / 2, $y + $lineHeight, $pageLabel, $font, $size);

            $right1Width = $fontMetrics->getTextWidth($rightLine1, $font, $size);
            $right2Width = $fontMetrics->getTextWidth($rightLine2, $font, $size);
            $canvas->text($width - $marginX - $right1Width, $y + $lineHeight, $rightLine1, $font, $size);
            $canvas->text($width - $marginX - $right2Width, $y + (2 * $lineHeight), $rightLine2, $font, $size);
        });

        $output = $dompdf->output();

        if (! is_string($output) || strlen($output) < 100) {
            throw new \RuntimeException('PDF could not be generated with company footer.');
        }

        return $output;
    }
}
