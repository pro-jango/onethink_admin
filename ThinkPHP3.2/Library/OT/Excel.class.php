<?php

//如果数据量太多，直接读取所有数据再一次过写入会导致内存溢出。
//这个类做了改进，获取一条数据就写一条。
namespace OT;

class Excel
{

	var $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?\>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

	var $footer = "</Workbook>";
	var $fp = NULL;//文件指针
	var $filename = '';//文件路径
	var $output_type = 'flow';//输出文件还是输出流
	var $worksheet_title = "Table1";
	var $head_out = false;//头是否已输出

	//初始化。默认使用流方式输出。
	public function __construct ($output_type = 'flow' , $filename = '')
	{
		if (!strlen($filename)) {
			$filename = uniqid();
		}

		$this->filename = $filename;

		if ($output_type == 'flow')
		{
			$this->output_type = 'flow';
		}
		else
		{
			$this->output_type = 'file';
			$this->fp = fopen($this->filename , 'w');
			if (!$this->fp) die('Excel_XML_v2:cannot open write file!');
		}
	}

	function addRow ($array)
	{
		$cells = "";
		foreach ($array as $k => $v):
			$cells .= "<Cell><Data ss:Type=\"String\">" . ($v) . "</Data></Cell>\n";
		endforeach;
		$str = "<Row>\n" . $cells . "</Row>\n";

		if (!$this->head_out) {
			$out = stripslashes ($this->header);
			$out .= "\n<Worksheet ss:Name=\"" . $this->worksheet_title . "\">\n<Table>\n";
			$out .= "<Column ss:Index=\"1\" ss:AutoFitWidth=\"0\" ss:Width=\"110\"/>\n";

			if ($this->output_type == 'flow') {
				header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
				header("Content-Disposition: inline; filename=\"" . $this->filename . ".xls\"");
				echo $out;
			} else {
				fwrite($this->fp , $out);
			}

			$this->head_out = true;
		}

		if ($this->output_type == 'flow') {
			echo $str;
			ob_flush();
			flush();//立即输出
		} else {
			fwrite($this->fp , $str);
		}
	}

	function addArray ($array)
	{
		foreach ($array as $k => $v):
			$this->addRow ($v);
		endforeach;
	}

	function setWorksheetTitle ($title)
	{
		$title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);
		$title = substr ($title, 0, 31);
		$this->worksheet_title = $title;
	}

	function out(){
		$out = "</Table>\n</Worksheet>\n";
		$out .= $this->footer;

		if ($this->output_type == 'flow')
		{
			echo $out;
		}
		else
		{
			fwrite($this->fp , $out);
			fclose($this->fp);
		}
	}

}
