<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 24.
 * Time: 16:14
 */

namespace App;

use Codedge\Fpdf\Fpdf\Fpdf;

class Personal_inventory extends Fpdf
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(1);
        // Title
        $this->Cell(0,10,mb_convert_encoding('   Dolgozói IT eszközök', 'ISO-8859-2'),'',0,'L');
        //Logo
        $this->Image('./images/Fiege.png',160,10,30);
        // Line break

        $this->Ln(15);
        $this->SetFont('Arial','',10);
        $this->Cell(50,6,mb_convert_encoding('Az alábbi dokumentum aláírója átveszi a dokumentumon szereplő, Fiege Kft tulajdonában álló eszközöket.', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('Tudomásul veszi, hogy ezen eszközök továbbra is a Fiege Kft tulajdonát képezik, azokért anyagi felelősséggel tartozik.', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('Használatuk kizárólag az IT szabályzatnak megfelelően történhet. Aláírással kijelenti, hogy a szabályzatot', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('(mely igazgatói utasítás is egyben) ismeri, az abban foglaltakat elfogadja.', 'ISO-8859-2'),'',0,'L');
        $this->Ln(10);
        $this->Cell(50,6,mb_convert_encoding('Kiemelt kötelezettségek:', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('    - A helyhez kötött eszközök (pl aztali gép, monitor) áthelyezése csak az IT osztály engedélyével lehetséges,', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('    - A sérült eszközökről az IT osztályt haladéktalanul értesíteni kell,', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('    - Az esetleges eltulajdonításról azonnal értesíteni kell az IT osztályt az ügyeleti telefonszámon,', 'ISO-8859-2'),'',0,'L');
        $this->Ln(4);
        $this->Cell(50,6,mb_convert_encoding('    - IT osztályt értesíteni kell esetleges vírus megjelenésekor, vagy annak gyanújakor.', 'ISO-8859-2'),'',0,'L');


        $this->Cell(90);
        $this->Ln(20);

    }


// Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

//FancyTable

    function FancyTable($employee, $networklogonname, $header, $data)
    {
        $this->Cell(30,6,mb_convert_encoding('Dolgozó neve:', 'ISO-8859-2'),'',0,'R');
        $this->Cell(10);
        $this->SetFont('Arial','B',10);
        $this->Cell(20,6,mb_convert_encoding($employee, 'ISO-8859-2'),'',0,'L');
        $this->Cell(30);
        $this->SetFont('');
        $this->Cell(20,6,mb_convert_encoding('Bejelentkezési azonosító:', 'ISO-8859-2'),'',0,'L');
        $this->Cell(25);
        $this->SetFont('Arial','B',10);
        $this->Cell(20,6,mb_convert_encoding($networklogonname, 'ISO-8859-2'),'',0,'L');
        $this->SetFont('');
        $this->Ln(20);
        $this->Cell(50,6,mb_convert_encoding('Dolgozó nevére kiírt eszközök:', 'ISO-8859-2'),'',0,'R');
        $this->Ln(10);

        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        // Header
        $w = array(75, 45, 45, 20);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,mb_convert_encoding($header[$i], 'ISO-8859-2'),1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',10);
        // Data
        $fill = false;

        for($i=0;$i<count($data);$i=$i+4){
            $this->Cell($w[0],6,mb_convert_encoding($data[$i], 'ISO-8859-2'),'',0,'L',$fill);
            $this->Cell($w[1],6,mb_convert_encoding($data[$i+1], 'ISO-8859-2'),'',0,'R',$fill);
            $this->Cell($w[2],6,mb_convert_encoding($data[$i+2], 'ISO-8859-2'),'',0,'R',$fill);
            $this->Cell($w[3],6,mb_convert_encoding($data[$i+3], 'ISO-8859-2'),'',0,'R',$fill);
            $this->Ln();
        }

        $this->Cell(array_sum($w),0,'','T');
        $this->Ln(30);
        $this->Cell(50,6,mb_convert_encoding('Dátum:', 'ISO-8859-2'),'',0,'R');
        $this->Cell(10);
        $this->Cell(50,6,date("Y-m-d"),'',0,'L');
        $this->Ln(10);
        $this->Cell(50,6,mb_convert_encoding('Aláírás:', 'ISO-8859-2'),'',0,'R');
        $this->Ln(30);
        $this->Cell(10,6,mb_convert_encoding('2016-11-30/2.0 ', 'ISO-8859-2'),'',0,'L');
        $this->Ln();
    }
}
