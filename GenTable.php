<?php
class GenTable {

	private $acl;
	private $title = '';  //tytuł tabeli
	private $formStruct = array(); //struktura formularza
	private $dataOrder = array(); //kolejność danych w tabeli

	public function __construct($aclObj,$tblName,$struct) //konstruktor przyjmuje 3 argumenty 
	{
		$this->acl = $aclObj;
		$this->title = $tblName; //inicializacja tytułu tabeli
		$this->openForm($struct); //inicjalizacja 
	}
	
	public function openForm($formName) // funkcja przyjmuje nazwę formularza
	{
		$formFile = dirname(__FILE__).'/form.'.$formName.'.php'; //ścieżka do pliku formularza na podstawie argumentu przekazanegeo do funkcji
		if(file_exists($formFile)){ //sprawdzenie czy podany plik formularza istnieje
			include($formFile); //dołączenie danych z formularza do kodu 
			$this->formStruct = $form;//przypisanie zmiennej formStruct struktury formularza z dołączonego pliku
		}
	}

	private function tblHeading($formStruct) //
	{
		$reOrder = array_fill(0,count($formStruct),0); // tworzenie nowej tablicy i wypełnianie jej zerami wielkość tablicy zależy od ilości wierszy 
		$this->dataOrder = array_fill(0,count($formStruct),0);
		$th = '';
		foreach($formStruct as $key=>$def){ //pętla ustawia dane w odpowiedniej kolejności w tablicy według kolejności zawartej w srukturze formularza
			$ord = $def[0]; // przypisanie do zmiennej ord cyfry z tablicy asocjacyjnej która oznacza kolejność
			$reOrder[$ord] = array($key,$def[1]); //porządkowanie danych i dodawanie klucza wraz z wartością w odpowiednim miejscu tablicy
			$this->dataOrder[$ord] = $key;
		} 
		foreach($reOrder as $k=>$def){ // tworzenie nagłówków tabeli na podstawie stworzonej wcześniej tablicy
			$th.= '<th>'.$def[1].'</th>';
		}
		$thead = '<tr>'.$th.'</tr>';
		return $thead; // zwracamy gotową strukturę nagłówków tabeli
	}
	// Funkcja do generowania danych tabeli
	private function tblData($data)
	{
		if(count($data)==0){
			return '<tr><td>brak danych</td></tr>';
		}
		$tbody = '';
		foreach($data as $entry){
			$cell = '';
		#	foreach($entry as $cdata){
			foreach($this->dataOrder as $key){
				$cdata = $entry[$key];
				$cell.= '<td>'.$cdata.'</td>';
			}
			$row = '<tr>'.$cell.'</tr>';
			$tbody.= $row;
		}
		return $tbody;
	}
    //funkcja budująca tabelę używająca wcześniej napisanych funkcji
	public function build($data)
	{
		if($this->title!=null){
			$tblCap = '<caption>'.$this->title.'</caption>';
		}
		$thead = $this->tblHeading($this->formStruct);
		$tbody = $this->tblData($data);

		$tbl = '<table>'.$tblCap.$thead.$tbody.'</table>';
		return $tbl;
	}

}
include('firma-krzak.php');
$tbl = new GenTable(null,'Lista uczestników','persons3');
echo $tbl->build($dbData);
