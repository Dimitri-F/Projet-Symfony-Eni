<?php

namespace App\Service;

use League\Csv\Reader;

class CsvImporterService
{
    public function importCsv($fileName)
    {
        $csv = Reader::createFromPath($fileName, 'r');
//        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $data = [];

        foreach ($records as $record) {
            $data[] = $record;
        }

        // Afficher les données ultérieurement ou les utiliser dans votre logique métier
        var_dump($data);
    }

}
