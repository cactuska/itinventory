<?php
/**
 * Created by PhpStorm.
 * User: dposztos
 * Date: 2018. 08. 24.
 * Time: 13:35
 */
namespace App;

use Codedge\Fpdf\Fpdf\Fpdf;

class Returndoc extends Fpdf
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(1);
        // Title
        $this->Cell(0,10,mb_convert_encoding('   Átvételi elismervény', 'ISO-8859-2'),'',0,'L');
        //Logo
        $this->Image('./images/Fiege.png',160,10,30);
        // Line break
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

    function FancyTable($data, $employee)
    {
        $this->Cell(30,6,mb_convert_encoding('Alulírott '.$employee[1].' aláírásommal igazolom, hogy a lentebb részletezett eszközöket, amely','ISO-8859-2'),'',0,'L');
        $this->Ln(6);
        $this->Cell(30,6,mb_convert_encoding('a Fiege Kft tulajdonát képezi, visszavettem '.$employee[0].'-tól/-től','ISO-8859-2'),'',0,'L');
        $this->Ln(20);

        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',10);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',10);
        // Data
        $fill = false;

        for($i=0;$i<count($data);$i=$i+2){
            $this->Cell(30,6,mb_convert_encoding('Készülék típusa:','ISO-8859-2'),'',0,'L',$fill);
            $this->Cell(30);
            $this->Cell(30,6,mb_convert_encoding($data[$i],'ISO-8859-2'),'',0,'L',$fill);
            $this->Ln();
            $this->Cell(30,6,mb_convert_encoding('Egyedi azonosítója:','ISO-8859-2'),'',0,'L',$fill);
            $this->Cell(30);
            $this->Cell(30,6,mb_convert_encoding($data[$i+1],'ISO-8859-2'),'',0,'L',$fill);
            $this->Ln();
            $this->Cell(40,6,mb_convert_encoding('Állapot:','ISO-8859-2'),'',0,'L',$fill);
            $this->Cell(30);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Cell(30,6,mb_convert_encoding('Korának megfelelő','ISO-8859-2'),'',0,'L',$fill);
            $this->Cell(30);
            $this->Cell(30,6,mb_convert_encoding('Hibás','ISO-8859-2'),'',0,'L',$fill);
            $this->rect($x - 8.5,$y + 1,3,3);
            $this->rect($x+52,$y + 1,3,3);
            $this->Cell(30);
            $this->Ln(10);

        }

        $this->Ln(30);
        $this->Cell(30,6,'Kelt:  Budapest, ','',0,'R');
        $this->Cell(50,6,date("Y-m-d"),'',0,'L');
        $this->Ln(30);
        $this->Cell(20);
        $this->Cell(60,6,mb_convert_encoding('Átadó:', 'ISO-8859-2'),'T',0,'C');
        $this->Cell(30);
        $this->Cell(60,6,mb_convert_encoding('Átvevő:', 'ISO-8859-2'),'T',0,'C');
        $this->Ln(30);
        $this->Cell(10,6,'2016-11-30/1.0 ','',0,'L');
        $this->Ln();
    }

}