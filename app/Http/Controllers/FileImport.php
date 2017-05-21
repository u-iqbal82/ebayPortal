<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use PHPExcel; 
use PHPExcel_IOFactory;

use App\Batch;
use App\Article;

class FileImport extends Controller
{
    public function import($filename, $batchId){
        
        //$directory = config('app.fileDestinationPath');
        
        $objPHPExcel = PHPExcel_IOFactory::load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        
        for ($row = 2; $row <= $highestRow; ++$row) {
            $url = $objWorksheet->getCellByColumnAndRow(0, $row);
            $subject = $objWorksheet->getCellByColumnAndRow(1, $row);
            $category = $objWorksheet->getCellByColumnAndRow(2, $row);
            
            $article = new Article();
            $article->article_url = $url;
            $article->article_subject = $subject;
            $article->article_category = $category;
            $article->status = 'UnAssigned';
            $article->batch_id = $batchId;
            $article->save();
            
        }
    }
    
    public function export($id)
    {
        $batch = Batch::find($id);
        $articles = Article::where('batch_id', $id)->with('detail')->get();
        
        if (empty($articles))
        {
            return redirect()->back()->with('fail', 'Nothing to download.');
        }
        
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("iThinkMedia")
							 ->setLastModifiedBy("iThinkMedia")
							 ->setTitle($batch->name)
							 ->setSubject($batch->name);
		
		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Article URL')
            ->setCellValue('B1', 'Article Subject')
            ->setCellValue('C1', 'Article Category')
            ->setCellValue('D1', 'Article Description');
		
		$i = 2;
		foreach($articles as $article)
        {
            if (empty($article->detail->description))
            {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $article->article_url)
                ->setCellValue('B'.$i, $article->article_subject)
                ->setCellValue('C'.$i, $article->article_category);
            }
            else
            {
                 $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $article->article_url)
                ->setCellValue('B'.$i, $article->article_subject)
                ->setCellValue('C'.$i, $article->article_category)
                ->setCellValue('D'.$i, str_replace('\r\n', '', $article->detail->description)); 
            }
           
            
            ++$i;
        }
        
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.str_replace(' ', '', $batch->name).'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        
        exit;
    }
}
