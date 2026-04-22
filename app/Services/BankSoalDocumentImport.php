<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIO;
use PhpOffice\PhpWord\IOFactory as WordIO;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;

class BankSoalDocumentImport
{
    /**
     * @return array<int, array{pertanyaan: string, jawaban_a: string, jawaban_b: string, jawaban_c: string, jawaban_d: string, jawaban_e: string, kunci_jawaban: string}>
     */
    public static function rowsFromUpload(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        return match ($ext) {
            'csv' => self::parseCsv($file->getPathname()),
            'xlsx', 'xls' => self::parseExcel($file->getPathname()),
            'docx' => self::parseDocx($file->getPathname()),
            default => throw new \InvalidArgumentException(
                'Format file tidak didukung untuk impor otomatis. Gunakan CSV, Excel (.xlsx / .xls), atau Word (.docx) berisi tabel 7 kolom (sama seperti template CSV).'
            ),
        };
    }

    public static function supportedExtensionsMessage(): string
    {
        return 'Didukung: CSV, Excel (.xlsx, .xls), Word (.docx dengan tabel 7 kolom). File PDF/PPT/DOC lama belum di-parse otomatis — silakan ekspor ke Excel/CSV mengikuti template.';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function parseCsv(string $path): array
    {
        $data = [];
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Tidak dapat membaca file CSV.');
        }
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 7) {
                $data[] = [
                    'pertanyaan' => $row[0],
                    'jawaban_a' => $row[1],
                    'jawaban_b' => $row[2],
                    'jawaban_c' => $row[3],
                    'jawaban_d' => $row[4],
                    'jawaban_e' => $row[5],
                    'kunci_jawaban' => $row[6],
                ];
            }
        }
        fclose($handle);

        return $data;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function parseExcel(string $path): array
    {
        $spreadsheet = SpreadsheetIO::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];
        $highestRow = $worksheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $pertanyaan = $worksheet->getCell('A' . $row)->getValue();
            if ($pertanyaan === null || $pertanyaan === '') {
                continue;
            }
            $data[] = [
                'pertanyaan' => (string) $pertanyaan,
                'jawaban_a' => (string) $worksheet->getCell('B' . $row)->getValue(),
                'jawaban_b' => (string) $worksheet->getCell('C' . $row)->getValue(),
                'jawaban_c' => (string) $worksheet->getCell('D' . $row)->getValue(),
                'jawaban_d' => (string) $worksheet->getCell('E' . $row)->getValue(),
                'jawaban_e' => (string) $worksheet->getCell('F' . $row)->getValue(),
                'kunci_jawaban' => (string) $worksheet->getCell('G' . $row)->getValue(),
            ];
        }

        return $data;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function parseDocx(string $path): array
    {
        $phpWord = WordIO::load($path);
        $data = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (! $element instanceof Table) {
                    continue;
                }
                foreach ($element->getRows() as $rowIndex => $row) {
                    if ($rowIndex === 0) {
                        continue;
                    }
                    $cells = $row->getCells();
                    if (count($cells) < 7) {
                        continue;
                    }
                    $vals = [];
                    foreach ($cells as $cell) {
                        $vals[] = self::cellPlainText($cell);
                    }
                    if ($vals[0] === '') {
                        continue;
                    }
                    $data[] = [
                        'pertanyaan' => $vals[0],
                        'jawaban_a' => $vals[1],
                        'jawaban_b' => $vals[2],
                        'jawaban_c' => $vals[3],
                        'jawaban_d' => $vals[4],
                        'jawaban_e' => $vals[5],
                        'kunci_jawaban' => $vals[6],
                    ];
                }
            }
        }

        if ($data === []) {
            throw new \InvalidArgumentException(
                'Dokumen Word tidak berisi tabel impor (minimal 7 kolom per baris, baris pertama = header). Susun seperti lembar Excel template.'
            );
        }

        return $data;
    }

    private static function cellPlainText(Cell $cell): string
    {
        $out = '';
        foreach ($cell->getElements() as $el) {
            if ($el instanceof Text) {
                $out .= $el->getText();
            } elseif ($el instanceof TextRun) {
                foreach ($el->getElements() as $inner) {
                    if ($inner instanceof Text) {
                        $out .= $inner->getText();
                    }
                }
            }
        }

        return trim($out);
    }
}
