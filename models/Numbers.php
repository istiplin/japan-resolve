<?php
namespace models;

use \sys\BaseObject;
use \sys\TArrayAccess;

//класс для работы с числами в строке, по которым строится рисунок
class Numbers extends BaseObject implements \ArrayAccess
{
    use TArrayAccess;
    
    private $_line;
	private $_cells;
    private $_list;
    private $_count=0;
    
    public function __construct($data,$line)
    {
        $this->_line = $line;
        $this->setList($data);
    }
    
    private function setList($data)
    {
        $elem = null;
        $this->_count = count($data);
        for($i=0; $i<$this->_count; $i++)
        {	
            $elem = new Number($this,$data[$i],$i,$elem);
            $this->_list[$i] = $elem;
        }
    }
    
	public function setCells($value)
	{
		$this->cells = $value;
		for ($i=0; $i<$this->_count; $i++)
			$this->_list[$i]->cells = $value;
	}
	
	public function getLine(): Line
	{
		return $this->_line;
	}
	
    public function getList(): array
    {
        return $this->_list;
    }
    
    public function getCount(): int
    {
        return $this->_count;
    }

    public function getMinPos($ind):int
    {
        return $this->_list[$ind]->getPos('min');
    }

    public function getMaxPos($ind):int
    {
        return $this->_list[$ind]->getPos('max');
    }

    //для каждого числа определяем возможные границы нахождения закрашенных клеток
    public function setBounds()
    {
        for($i=0; $i<$this->_count; $i++)
            $this->_list[$i]->setBound();
        
        for($i=$this->_count-1; $i>=0; $i--)
            $this->_list[$i]->setBound();
    }
    
    //помечает клетки крестиками
    private function setEmptyCellsByBounds()
    {
        //перебираем все числа
        for($i=0; $i<$this->_count; $i++)
            $this->_list[$i]->setEmptyCellsByBound();
    }

    //закрашивает клетки для каждого числа
    private function setFullCellsByBounds()
    {
        for($i=0; $i<$this->_count; $i++)
            $this->_list[$i]->setFullCellsByBound();
    }
    
    public function resolve()
    {
        $this->_line->isChange = true;
        while ($this->_line->isChange) {
            $this->_line->isChange = false;
            $this->setFullCellsByBounds();
            $this->setEmptyCellsByBounds();
        }
    }

    public function printBounds()
    {
        for($i=0; $i<$this->_count; $i++)
            $this->_list[$i]->printBound();
    }
    
    public function view()
    {
        for ($i=0; $i<$this->_count; $i++)
            echo $this->_list[$i]->length.'|';
    }
}