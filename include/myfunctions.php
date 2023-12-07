<?php

function createImage($data = '')
{
    $font = 43;
    $string = $data;
    $angle = 56; // Kąt obrotu

    $textWidth = strlen($string) * $font / 1.3;
    $textHeight = $font;

    // Tworzenie obrazu o odpowiednich wymiarach
    $im = @imagecreatetruecolor($textWidth, $textHeight);
    imagesavealpha($im, true);
    imagealphablending($im, true);
    $white = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefill($im, 0, 0, $white);

    $lime = imagecolorallocate($im, 230, 199, 165);

    $x = $font / 2;
    $y = $textHeight - 3;

    // Dodawanie tekstu obroconego o 45 stopni
    imagettftext($im, $font, 0, round($x), round($y), $lime, "fonts/andada-regular.ttf", $string);
    $rotatedIm = imagerotate($im, $angle, $white);

    // Zapisywanie obrazu
    $save = "temp/" . strtolower('watermark') . ".png";
    imagepng($rotatedIm, $save);

    // Zwalnianie zasobów
    imagedestroy($im);
    imagedestroy($rotatedIm);

    return $save;
}

function createBarCode($data = 'Bar Code')
{
    $save = "temp/" . strtolower('barcode') . ".png";
    $generator = new barcode_generator();
    $image = $generator->render_image('code-128', $data, ['sf'=>'1','h'=>'40']);
    imagepng($image,$save);
    imagedestroy($image);
    return $save;
}

function number2text($i)
{
    $test = array(
        'pierwszy',
        'drugi',
        'trzeci',
        'czwarty',
        'piąty',
        'szósty',
        'siódmy',
        'ósmy',
        'dziewiąty',
        'dziesiąty',
        'jedenasty',
        'dwunasty',
        'trzynasty',
        'czternasty',
        'piętnasty',
        'szesnasty',
        'siedemnasty',
        'osiemnasty',
        'dziewiętnasty',
        'dwudziesty'
    );
    $test2 = array(
        'dziesiąty',
        'dwadziesty',
        'trzydziesty',
        'czterdziesty',
        'pięćdziesiąty',
        'sześćdziesiąty',
        'siedemdziesiąty',
        'osiemdziesiąty',
        'dziewięćdziesiąty',
        'setny'
    );
    $test3 = array(
        'sto',
        'dwieście',
        'trzysta',
        'czterysta',
        'pięćset',
        'sześćset',
        'siedemset',
        'osiemset',
        'dziewięćset',
        'jeden tysiąc'
    );
    $test4 = array(
        'jeden tysiąc',
        'dwa tysiące',
        'trzy tysiące',
        'cztery tysiące'
    );

    if ($i > 0 && $i <= 20) {
        return $test[$i - 1];
    } elseif ($i > 20 && $i <= 100) {
        $counter = floor($i / 10);
        $i %= 10;
        return $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 100 && $i <= 1000) {
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 1000 && $i <= 4999) {
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;

        $result = $test4[$counter3 - 1];

        if ($counter2 > 0) {
            $result .= " " . $test3[$counter2 - 1];
        }

        if ($counter > 0) {
            $result .= " " . $test2[$counter - 1];
        }

        if ($i > 0) {
            $result .= " " . $test[$i - 1];
        }

        return $result;
    } elseif ($i >= 5000 && $i <= 20000) {
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test[$counter3 - 1] . " tysięcy " . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 20000 && $i <= 100000) {
        $counter4 = floor($i / 10000);
        $i %= 10000;
        $tysiac = ($i >= 2000 && $i <= 4000) ? " tysiące " : " tysięcy ";
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test2[$counter4 - 1] . " " . $test[$counter3 - 1] . $tysiac . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i >= 100000 && $i < 1000000) {
        $counter5 = floor($i / 100000);
        $i %= 100000;
        $counter4 = floor($i / 10000);
        $i %= 10000;
        $tysiac = ($i >= 2000 && $i <= 4000) ? " tysiące " : " tysięcy ";
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test3[$counter5 - 1] . " " . $test2[$counter4 - 1] . " " . $test[$counter3 - 1] . $tysiac . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } else {
        return "$i jest liczbą mniejszą od 1 lub większą od 1000000";
    }
}


function convertDate($date) {
    $dateArray = preg_split("/[-.\/]/", $date);
    
    if (count($dateArray) == 3) {
        $day = (int)$dateArray[0];
        $month = (int)$dateArray[1];
        $year = (int)$dateArray[2];
        
        // Sprawdzenie poprawności daty
        if (checkdate($month, $day, $year)) {
            return number2text($day) . ", " . number2text($month) . ", " . number2text($year);
        }
    }
    
    return "Nieprawidłowy format daty";
}

function numberToWords($i) {
    $units = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    $thousands = ['', 'thousand', 'million', 'billion'];
    
    $closure = function () use ($i, $units, $teens, $tens, $thousands) {
        if ($i == 0) {
            return 'zero';
        }
        
        $result = '';
        
        for ($group = 0; $i > 0; ++$group, $i = (int)($i / 1000)) {
            $number = $i % 1000;
            
            if ($number != 0) {
                $result = convert($number, $units, $teens, $tens) . ($group == 0 ? '' : ' ' . $thousands[$group]) . ' ' . $result;
            }
        }
        
        return $result;
    };
    
    // Wywołanie funkcji anonimowej
    return $closure();
}

function convert($number, $units, $teens, $tens)
{
    $result = '';
    
    if ($number >= 100) {
        $result .= $units[(int)($number / 100) - 1] . ' hundred';
        $number %= 100;
        
        if ($number > 0) {
            $result .= ' and ';
        }
    }
    
    if ($number >= 20) {
        $result .= $tens[(int)($number / 10)];
        $number %= 10;
        
        if ($number > 0) {
            $result .= '-';
        }
    }
    
    if ($number > 0) {
        if ($number < 10) {
            $result .= $units[$number - 1];
        } elseif ($number < 20) {
            $result .= $teens[$number - 10];
        }
    }
    
    return $result;
}

function convertDateEng($date) {
    $dateArray = preg_split("/[-.\/]/", $date);
    
    if (count($dateArray) == 3) {
        $day = (int)$dateArray[0];
        $month = (int)$dateArray[1];
        $year = (int)$dateArray[2];
        
        // Sprawdzenie poprawności daty
        if (checkdate($month, $day, $year)) {
            return numberToWords($day) . ", " . numberToWords($month) . ", " . numberToWords($year);
        }
    }
    
    return "Nieprawidłowy format daty";
}

function number2text2($i)
{
    $test = array(
        'pierwsza',
        'druga',
        'trzecia',
        'czwarta',
        'piąta',
        'szósta',
        'siódma',
        'ósma',
        'dziewiąta',
        'dziesiąta',
        'jedenasta',
        'dwunasta',
        'trzynasta',
        'czternasta',
        'piętnasta',
        'szesnasta',
        'siedemnasta',
        'osiemnasta',
        'dziewiętnasta',
        'dwudziesta'
    );
    $test2 = array(
        'dziesiąta',
        'dwadziesta',
        'trzydziesta',
        'czterdziesta',
        'pięćdziesiąta',
        'sześćdziesiąta',
        'siedemdziesiąta',
        'osiemdziesiąta',
        'dziewięćdziesiąta',
        'setna'
    );
    $test3 = array(
        'sto',
        'dwieście',
        'trzysta',
        'czterysta',
        'pięćset',
        'sześćset',
        'siedemset',
        'osiemset',
        'dziewięćset',
        'tysiąc'
    );
    $test4 = array(
        'tysiąc',
        'dwa tysiące',
        'trzy tysiące',
        'cztery tysiące'
    );

    if ($i > 0 && $i <= 20) {
        return $test[$i - 1];
    } elseif ($i > 20 && $i <= 100) {
        $counter = floor($i / 10);
        $i %= 10;
        return $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 100 && $i <= 1000) {
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 1000 && $i <= 4999) {
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test4[$counter3 - 1] . " " . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i >= 5000 && $i <= 20000) {
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test[$counter3 - 1] . " tysięcy " . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i > 20000 && $i <= 100000) {
        $counter4 = floor($i / 10000);
        $i %= 10000;
        $tysiac = ($i >= 2000 && $i <= 4000) ? " tysiące " : " tysięcy ";
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test2[$counter4 - 1] . " " . $test[$counter3 - 1] . $tysiac . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } elseif ($i >= 100000 && $i < 1000000) {
        $counter5 = floor($i / 100000);
        $i %= 100000;
        $counter4 = floor($i / 10000);
        $i %= 10000;
        $tysiac = ($i >= 2000 && $i <= 4000) ? " tysiące " : " tysięcy ";
        $counter3 = floor($i / 1000);
        $i %= 1000;
        $counter2 = floor($i / 100);
        $i %= 100;
        $counter = floor($i / 10);
        $i %= 10;
        return $test3[$counter5 - 1] . " " . $test2[$counter4 - 1] . " " . $test[$counter3 - 1] . $tysiac . $test3[$counter2 - 1] . " " . $test2[$counter - 1] . " " . $test[$i - 1];
    } else {
        return "$i jest liczbą mniejszą od 1 lub większą od 1000000";
    }
}

function roman2num($number)
{
    $num = intval($number);
    $result = "";
    $roman_numbers = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1
    );
    foreach ($roman_numbers as $roman => $value) {
        $check = intval($num / $value);
        $result .= str_repeat($roman, $check);
        $num = $num % $value;
    }
    return $result;
}

function divideEmail($email)
{
    // Sprawdzamy, czy adres email zawiera znak '@'
    if (strpos($email, '@') !== false) {
        // Dzielimy adres email na część przed '@' (nazwa użytkownika) i po '@' (domena)
        list ($userName, $domain) = explode('@', $email);
        // Zwracamy wynik jako tablicę
        return array(
            'uName' => $userName,
            'domain' => $domain
        );
    } else {
        // Jeśli adres email nie zawiera '@', zwracamy null
        return null;
    }
}

function capitalFirstLetters($zdanie,$count,$lenght)
{
    // Usuwanie tagów HTML
    $czystyTekst = strip_tags($zdanie);
    
    // Podział zdania na słowa
    $slowa = preg_split('/\s+/', $czystyTekst);
    
    $wynik = '';
    $i = 0;
    foreach ($slowa as $slowo) {
        // Pominięcie słów krótszych niż dwa znaki
        if (mb_strlen($slowo) <= $lenght) {
            continue;
        }
        // Pobranie pierwszej litery i zamiana na dużą
        $pierwszaLitera = mb_strtoupper(mb_substr($slowo, 0, 1));
        $wynik .= $pierwszaLitera;
        $i++;
        if($i>=$count) return $wynik;
    }
    
    return $wynik;
}
?>
