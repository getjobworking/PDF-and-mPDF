<?php
use Mpdf\Tag\Summary;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/barcode.php';
require_once __DIR__ . '/include/myfunctions.php';
require_once __DIR__ . '/include/dsclass.php';

// Kod inicjalizacyjny (jeśli potrzebny tylko raz)
$pdfGenerator = new dsclass();
$pdfGenerator->generatePDF();