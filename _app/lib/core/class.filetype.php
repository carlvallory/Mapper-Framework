<?php
class Filetype{

	public function is($match, $against){
		
		$match = strtolower($match);
		
		switch($match):
			case "excel":
				$types = array(
				"application/excel",
				"application/excel",
				"application/x-excel",
				"application/x-msexcel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/excel",
				"application/x-excel",
				"application/excel",
				"application/x-excel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/x-msexcel",
				"application/excel",
				"application/x-excel",
				"application/excel",
				"application/x-excel",
				"application/excel",
				"application/vnd.ms-excel",
				"application/x-excel",
				"application/x-msexcel");
				break;
			case "pdf":
				$types = array("application/pdf");
				break;
		endswitch;
	}
	
}
?>