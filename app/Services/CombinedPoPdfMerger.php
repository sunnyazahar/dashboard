<?php

namespace App\Services;

use RuntimeException;
use setasign\Fpdi\Fpdi;

class CombinedPoPdfMerger
{
    public function merge(array $absolutePaths): string
    {
        $pdf = new Fpdi();
        $pagesAdded = 0;

        foreach ($absolutePaths as $file) {
            if (!is_readable($file)) {
                continue;
            }

            try {
                $pageCount = $pdf->setSourceFile($file);
            } catch (\Throwable) {
                continue;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                $pagesAdded++;
            }
        }

        if ($pagesAdded === 0) {
            throw new RuntimeException('Unable to merge the selected PDF documents.');
        }

        return $pdf->Output('S');
    }
}
