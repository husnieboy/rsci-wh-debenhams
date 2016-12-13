<?php

// include_once("ewms_connection.php");
chdir(dirname(__FILE__));
include_once('db2_connection.php');

class cronDB2 {

	var $instance;

	public function __construct()
	{
		$this->instance = new odbcConnection();
	}

    public function getOpenPicklist($move_doc_number) {
        $sql = "SELECT count(WHMOVE) num_open
                FROM WHSMVH
                WHERE WHMVST = '1' AND WHMOVE IN ({$move_doc_number})";
                //WHERE POMRCH.POSTAT = 3 AND POMRCH.POLOC = 7000

		$query_result 	= $this->instance->runSQL($sql,true);

		return $query_result[0]['NUM_OPEN'];
    }

	public function posDescription()
	
{		$sql = "SELECT INUMBR, POS18 FROM INVDSC";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'pos_description';
	    // move_doc_number | date_completed | time | date_created
		$header_column 	= array('sku', 'pos_description');
		$this->_export($query_result, $filename, $header_column, __METHOD__, $custom_column);
	}

	public function inventory()
	{
		$sql = "SELECT INVUPC.IUPC, WHHAND, WHCOMM, WHSLOT
				FROM WHSLSK
				LEFT JOIN INVUPC ON WHSLSK.INUMBR = INVUPC.INUMBR";

		$query_result 	= $this->instance->runSQL($sql,true);
	    $filename 		= 'inventory';
	    // sku |quantity_on_hand | quantity_committed | slot_id | created_at
	    $header_column 	= array('sku','quantity_on_hand', 'quantity_committed', 'slot_id');

	    // $this->instance->displayResult($query_result, $key);
	    $this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function department()
	{
		$sql = "SELECT IDEPT, ISDEPT, ICLAS, ISCLAS, DPTNAM
				FROM INVDPT";

		$query_result 	= $this->instance->runSQL($sql,true);
	    $filename 		= 'department';
	    //dept_code | sub_dept | class | sub_class | description
	    $header_column 	= array('dept_code','sub_dept', 'class', 'sub_class', 'description');

	    // $this->instance->displayResult($query_result, $key);
	    $this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function letdown()
	{
		//move type us 3 = letdown
		// for checking after letdown was successfully close WHMVQM (qty_moved)
		//$sql = "SELECT WHMOVE, WHMCDT, WHMATM, WHMVDT
		$sql = "SELECT WHMOVE
		        FROM WHSMVH
		        WHERE WHMCDT = 0 AND WHMVTP = 3 AND WHMVDT = {$this->instance->getDate()}"; //
		        // FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'letdown_header';
	    // move_doc_number | date_completed | time | date_created
		$header_column 	= array('move_doc_number');
		$custom_column = array('type'=>'store');
		$this->_export($query_result, $filename, $header_column, __METHOD__, $custom_column);
	}

	public function letdownDetail()
	{
		//SELECT WHSMVD.WHMOVE, INVUPC.IUPC, WHMFSL, WHMVQR, WHMTLC, WHMVSR, WHSMVH.WHMVDT, WHMVSQ, WVSCNM, WHMVSQ, WVSCNM
		$sql = "SELECT WHSMVD.WHMOVE, INVUPC.IUPC, WHMFSL, WHMVQR, WHMTLC, WHSMVH.WHMVDT, WHMVSQ, WVSCNM, WHMVSQ, WVSCNM, WHSMVD.WHMTSL
		        FROM WHSMVH
		        RIGHT JOIN WHSMVD ON WHSMVD.WHMOVE = WHSMVH.WHMOVE AND WVFZON = 'CR' AND WVTZON='PZ'
		        INNER JOIN INVUPC ON WHSMVD.INUMBR = INVUPC.INUMBR
		        WHERE WHSMVH.WHMCDT = 0 AND WHSMVD.WHMVTP = 3 AND WHMVDT = {$this->instance->getDate()}"; //
		        //FETCH FIRST 2 ROWS ONLY
		        //RIGHT JOIN WHSMVD ON WHSMVD.WHMOVE = WHSMVH.WHMOVE AND WVFZON = 'PZ' AND WVTZON='SZ'

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'letdown_detail';
	    // move_doc_number | sku | from_slot_code | quantity_to_pick | store_id | store_order_number | date_created
		// $header_column 	= array('move_doc_number','sku', 'from_slot_code', 'quantity_to_pick', 'store_code', 'so_no', 'date_created', 'sequence_no', 'group_name');
		$header_column 	= array('move_doc_number','sku', 'from_slot_code', 'quantity_to_letdown', 'store_code', 'date_created', 'sequence_no', 'group_name', 'to_slot_code');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function picking()
	{
		//move type is 2 = picking
		// $sql = "SELECT WHMOVE, WHMCDT, WHMATM, WHMVDT
		$sql = "SELECT whsmvd.whmvsr, trfbdt
				FROM  whsmvh
				left join whsmvd on whsmvh.whmove = whsmvd.whmove
				left join trfhdr on whsmvd.whmove = trfhdr.trfbch
				where whsmvh.whmvtp = 2  AND whsmvh.WHMVST = '1'";//



		        // FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'picklist_header';
	    // move_doc_number | date_completed | time | date_created
		$header_column 	= array('move_doc_number','ship_date');
		$custom_column = array('type'=>'store');
		$this->_export($query_result, $filename, $header_column, __METHOD__, $custom_column);
	}

	public function pickingDetail()
	{
		$sql = "SELECT  whsmvd.whmvsr, invupc.IUPC, Whmfsl, WHMVQR, trfhdr.TRFTLC , trfbdt
from whsmvh
left join whsmvd on whsmvh.whmove = whsmvd.whmove
LEFT join invupc on whsmvd.inumbr = invupc.inumbr
LEFT JOIN TRFHDR ON WHSMVD.WHMVSR = TRFHDR.TRFBCH
where whsmvh.whmvtp = 2  AND whsmvh.WHMVST = '1'";

 
// 
// AND WHSMVH.WHMCDT = 0
//FETCH FIRST 2 ROWS ONLY


		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'picklist_detail';
	    // move_doc_number | sku | from_slot_code | quantity_to_pick | store_id | store_order_number | date_created
		$header_column 	= array( 'move_doc_number','sku','from_slot_code','quantity_to_pick', 'store_code', 'created_at');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function pickingv2()
	{
		//kunin muna yung wave cycle sa letdown na ang move status nya ay 2 (which is close na) and yung wave number nya
		//move type is 2 = picking
		$data = self::_getClosedLetdown();

/*		$data = array(array('WHCYCL'=> 181, 'WHWAVE'=> 1),
					  array('WHCYCL'=> 182, 'WHWAVE'=> 1));*/
		$cycle_arr = array();
		foreach($data as $key => $value) {
			$cycle_arr[] = "(".join(',', $value).")";
		}

		$values = implode(",", $cycle_arr);
		//$sql = "SELECT WHMOVE, WHMCDT, WHMATM, WHMVDT, WHCYCL, WHWAVE
		$sql = "SELECT WHMOVE, WHCYCL, WHWAVE
					FROM WHSMVH
					WHERE WHMVST = 2 AND (WHCYCL, WHWAVE) IN (VALUES {$values}) AND WHMVDT = {$this->instance->getDate()} ";//

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'picklist_header';
	    // move_doc_number | date_completed | time | date_created
		$header_column 	= array('move_doc_number', 'cycle', 'wave');
		$custom_column = array('type'=>'store');
		$this->_export($query_result, $filename, $header_column, __METHOD__, $custom_column);
	}

	public function pickingDetailv2()
	{
		$data = self::_getClosedLetdown();
		/*$data = array(array('WHCYCL'=> 181, 'WHWAVE'=> 1),
					  array('WHCYCL'=> 182, 'WHWAVE'=> 1));*/
		$cycle_arr = array();
		foreach($data as $key => $value) {
			$cycle_arr[] = "(".join(',', $value).")";
		}

		$values = implode(",", $cycle_arr);
		$sql = "SELECT WHSMVD.WHMOVE, INVUPC.IUPC, WHMFSL, WHMVQR, WHMTLC, WHMVSR, WHSMVH.WHMVDT, WHMVSQ, WVSCNM, WHMVSQ, WVSCNM, WHMVQM
		        FROM WHSMVH
		        RIGHT JOIN WHSMVD ON WHSMVD.WHMOVE = WHSMVH.WHMOVE AND WVFZON = 'PZ' AND WVTZON='SZ'
		        INNER JOIN INVUPC ON WHSMVD.INUMBR = INVUPC.INUMBR
		        WHERE WHSMVD.WHMVTP = 2
	        		AND (WHSMVH.WHCYCL, WHSMVH.WHWAVE) IN (VALUES {$values}) AND WHMVDT = {$this->instance->getDate()} AND WHSMVH.WHMCDT = 0 ";//
		        //FETCH FIRST 2 ROWS ONLY


		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'picklist_detail';
	    // move_doc_number | sku | from_slot_code | quantity_to_pick | store_id | store_order_number | date_created
		$header_column 	= array('move_doc_number','sku', 'from_slot_code', 'quantity_to_pick', 'store_code', 'so_no', 'date_created', 'sequence_no', 'group_name', 'quantity_moved');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function _getClosedLetdown()
	{
		//kunin muna yung wave cycle sa letdown na ang move status nya ay 2 (which is close na) and yung wave number nya
		//move type = 3(letdown), 2(picking)
		// WHCYCL = wave cycle
		// WHMVST = status
		$sql = "SELECT WHCYCL, WHWAVE, WHMVST FROM WHSMVH WHERE WHMVST = 2 AND WHMVTP = 3";
		$query_result 	= $this->instance->runSQL($sql,true);

		return $query_result;
	}

	public function products()
	{

		$sql = "SELECT INVMST.INUMBR, INVUPC.IUPC, ISORT , IDESCR, ASNUM, IDEPT, ISDEPT, ICLAS, ISCLAS, ISET
				FROM INVMST
				LEFT JOIN INVUPC ON INVMST.INUMBR = INVUPC.INUMBR";
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'product_master_list';
	    // sku | upc | short_description | description | vendor | dept_code | sub_dept | class | subclass | set_code
		$header_column 	= array('sku','upc', 'short_description', 'description', 'vendor', 'dept_code', 'sub_dept', 'class', 'sub_class', 'set_code');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function purchaseOrder()
	{
// POMHDR.POFOB = carton
// POBON = Back order
// //POUNTS = total_qty
// POSHP1 = shipment reference no
 

/*$sql ="SELECT POMRCH.POMRCV, pomrch.poshpr, POMRCH.PONUMB, POMRCH.POUNTS ,   POMHDR.POEDAT 
FROM POMRCH 
LEFT JOIN POMHDR ON POMHDR.PONUMB = POMRCH.PONUMB 
WHERE POMRCH.PONUMB>=10881 and POMRCH.PONUMB<=10892";*/

$sql = "SELECT POMRCH.POMRCV, pomrch.poshpr, POMRCH.PONUMB, POMRCH.POUNTS ,   POMHDR.POEDAT 
FROM POMRCH 
LEFT JOIN POMHDR ON POMHDR.PONUMB = POMRCH.PONUMB 
WHERE POMRCH.POSTAT = 3";
	/**	$ sql = "SELECT POMRCH.POVNUM, POMRCH.POMRCV, POMRCH.PONUMB, POMRCH.POLOC, POMHDR.POFOB, POMRCH.POUNTS, POMRCH.POBON, POMHDR.POSHP1, POMRCH.POSTAT
		        FROM POMRCH
		        LEFT JOIN POMHDR ON POMHDR.PONUMB = POMRCH.
			PONUMB AND POMRCH.POBON = POMHDR.POBON
		        WHERE POMRCH.POSTAT = 3 AND POMRCH.POLOC = 7000"; // get PO with status=3/RELEASE  AND POMRCH.PONUMB IN (3815)
		        //FETCH FIRST 1 ROWS ONLY";**/

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'purchase_order_header';
	    // vendor_id | receiver_no | purchase_order_no | destination | po_status
		$header_column 	= array( 'receiver_no', 'invoice_no', 'po_no',   'total_qty',  'entry_date' );
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function purchaseOrderDetails()
	{
	$sql = "SELECT DISTINCT POMRCD.INUMBR, INVUPC.IUPC, POMRCD.POMRCV,   INVDPT.IDEPT, pomrcd.pomqty, INVDPT.DPTNAM
FROM POMRCD
LEFT JOIN INVUPC ON POMRCD.INUMBR = INVUPC.INUMBR
LEFT JOIN INVMST ON POMRCD.INUMBR = INVMST.INUMBR
LEFT JOIN INVDPT ON INVMST.IDEPT = INVDPT.IDEPT
LEFT JOIN POMRCH ON POMRCD.POMRCV = POMRCH.POMRCV
WHERE POMRCH.POSTAT = 3 AND INVDPT.ISDEPT=0  AND INVDPT.ICLAS=0 AND INVDPT.ISCLAS=0 "; // get PO with status=3/RELEASE  AND POMRCH.PONUMB IN (3815)
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'purchase_order_detail';
	    // vendor_id | sku | quantity_ordered | unit_cost
		$header_column 	= array( 'sku', 'upc','receiver_no', 'dept_number', 'quantity_ordered','dept_name');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
		
	}

	public function slots()
	{


		$sql = "SELECT WHSLOT,    whzone, strnum FROM WHSLOC";
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'slot_master_list';
	    // vendor_id | sku | quantity_ordered | unit_cost
		$header_column 	= array('slot_code', 'zone_code', 'store_code' );
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function stores()
	{

		$sql = "SELECT STRNUM, STRNAM, STADD1, STADD2, STADD3, STCITY FROM TBLSTR";
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'store_master_list';
	    // store_no | store_name | address1 | address2 | address3 | city
		$header_column = array('store_code','store_name', 'address1', 'address2', 'address3', 'city');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	//to test
	public function storeOrder()
	{
		// Logic
		// Get all so_no in the picklist of the day
		// from the gathered picklist match it to the so_no in the Transaction Header/TRFHDR
		 

		$sql = "SELECT STRNUM, STRNAM, STADD1, STADD2, STADD3, STCITY FROM TBLSTR";
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'store_master_list_test';
	    // store_no | store_name | address1 | address2 | address3 | city
		$header_column = array('store_code','store_name', 'address1', 'address2', 'address3', 'city');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	//to test
	public function storeOrderDetails()
	{
		//join by inumbr = inumbr

		//get unique in MVD.WHSVSR  // for header

		$getSoNo = self::_getUniqueSO();
		$ids = "'".implode("' , '", $getSoNo)."'";
		$ids = preg_replace('/\s+/', '', $ids);

		if(! empty($ids) )
		{
			$sql = "SELECT TRFDTL.TRFBCH, INVUPC.IUPC,TRFDTL.TRFREQ, TRFDTL.TRFALC
					FROM TRFHDR
					RIGHT JOIN TRFDTL ON TRFDTL.TRFBCH = TRFHDR.TRFBCH
					INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
					WHERE TRFDTL.TRFBCH IN ($ids)";
			$query_result 	= $this->instance->runSQL($sql,true);
			$filename 		= 'store_order_detail';
		    // so_no | sku | ordered_qty | alloctated_qty | created_at
			$header_column = array('so_no','sku', 'ordered_qty', 'allocated_qty');
			$this->_export($query_result, $filename, $header_column, __METHOD__);
		}
	}

	public function getShippedStoreOrder()
	{
		$sql = "SELECT DISTINCT TRFHDR.TRFBCH, TRFHDR.TRFTLC, TRFHDR.TRFSTS, WHSCTH.CHLOID,WHSCTH.CHCTID
					FROM TRFHDR
					INNER JOIN WHSMVD ON TRFHDR.TRFBCH = WHSMVD.WHMVSR
					INNER JOIN WHSCTH ON WHSMVD.WVCTID = WHSCTH.CHCTID
					WHERE TRFHDR.TRFSTS = 'S'";
		$query_result 	= $this->instance->runSQL($sql,true);

		$filename 		= 'store_order_cloud_header';
	    // so_no | store_name | so_status | order_date | created_at
		$header_column = array('so_no','store_code', 'so_status', 'load_code', 'box_code');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function getShippedStoreOrderDetails()
	{
		$sql = "SELECT TRFDTL.TRFBCH, INVUPC.IUPC,TRFDTL.TRFREQ, TRFDTL.TRFALC
					FROM TRFHDR
					INNER JOIN WHSMVD ON TRFHDR.TRFBCH = WHSMVD.WHMVSR
					INNER JOIN WHSCTH ON WHSMVD.WVCTID = WHSCTH.CHCTID
					RIGHT JOIN TRFDTL ON TRFDTL.TRFBCH = TRFHDR.TRFBCH
					INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
					WHERE TRFHDR.TRFSTS = 'S'";
		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'store_order_cloud_detail';
	    // so_no | sku | ordered_qty | alloctated_qty | created_at
		$header_column = array('so_no','sku', 'ordered_qty', 'allocated_qty');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function _getUniqueSO()
	{
		$getSoNumbers = "SELECT DISTINCT WHMVSR
		        FROM WHSMVH
		        RIGHT JOIN WHSMVD ON WHSMVD.WHMOVE = WHSMVH.WHMOVE AND WVFZON = 'PZ' AND WVTZON='SZ'
		        WHERE WHSMVH.WHMCDT = 0 AND WHMVDT = {$this->instance->getDate()}";

		$query_result = $this->instance->getUnique($getSoNumbers, "WHMVSR");

		return $query_result;
	}

	public function vendors()
	{

		$sql = "SELECT ASNUM, ASNAME FROM APSUPP";
		        //FETCH FIRST 1 ROWS ONLY";

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'vendor_master_list';
	    // vendor_code  | vendor_name
		$header_column = array('vendor_code', 'vendor_name');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function storeReturn()
	{
		$sql = "SELECT trfhdr.trfbch, trfhdr.trfflc, trfhdr.trftlc
				FROM trfhdr
				left join tblstr on tblstr.strnum = trfhdr.trftlc 
				WHERE trfsts = 'S' and trftlc != 8001 and trftyp = 1 and tblstr.strtyp='U'";

		$query_result 	= $this->instance->runSQL($sql,true);

		$filename 		= 'store_return_header';
	    // so_no | store_name | so_status | order_date | created_at
		$header_column = array('so_no', 'from_store_code', 'to_store_code');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	//to test
	public function storeReturnDetails()
	{

		$sql  = "SELECT trfdtl.trfbch, INVUPC.IUPC, trfdtl.trfshp
				from TRFHDR
				left join trfdtl on trfhdr.trfbch = trfdtl.trfbch
				left JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
				left join tblstr on tblstr.strnum = trfhdr.trftlc
				WHERE trfhdr.trfsts = 'S' and trfhdr.trftlc != 8001 and tblstr.strtyp='U'"; // OR and trfhdr.trftyp = 1";

		/*$sql = "SELECT TRFDTL.TRFBCH, INVUPC.IUPC,TRFDTL.TRFREQ, TRFDTL.TRFALC
				FROM TRFHDR
				RIGHT JOIN TRFDTL ON TRFDTL.TRFBCH = TRFHDR.TRFBCH
				INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
				WHERE TRFDTL.TRFSTS = 'S' AND TRFDTL.TRFTLC = 7000";*/

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'store_return_detail';
	    // so_no | sku | ordered_qty | alloctated_qty | created_at
		$header_column = array('so_no','sku', 'delivered_qty');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function storeReturn_pick()
	{
		$sql = "SELECT trfhdr.trfbch
				from trfhdr
				left join tblstr on tblstr.strnum = trfhdr.trfflc
				where trfsts = 'W'and tblstr.strtyp = 'U'";

		$query_result 	= $this->instance->runSQL($sql,true);

		$filename 		= 'store_return_pickinglist';
	    // so_no | store_name | so_status | order_date | created_at
		$header_column = array('move_doc_number');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	//to test
	public function storeReturnDetails_pick()
	{
		$sql  = "SELECT trfhdr.trfbch, trfdtl.trftlc, INVUPC.IUPC, trfdtl.trfflc, trfdtl.trfreq
				from trfhdr
				left join trfdtl on trfhdr.trfbch = trfdtl.trfbch
				left join tblstr on trfhdr.trfflc = tblstr.strnum
				INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR 
				where trfhdr.trfsts = 'W'and tblstr.strtyp = 'U'";

		/*$sql = "SELECT TRFDTL.TRFBCH, INVUPC.IUPC,TRFDTL.TRFREQ, TRFDTL.TRFALC
				FROM TRFHDR
				RIGHT JOIN TRFDTL ON TRFDTL.TRFBCH = TRFHDR.TRFBCH
				INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
				WHERE TRFDTL.TRFSTS = 'S' AND TRFDTL.TRFTLC = 7000";*/

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'store_return_pick_details';
	    // so_no | sku | ordered_qty | alloctated_qty | created_at
		$header_column = array('so_no','to_store_code', 'upc', 'from_store_code','quantity_to_pick');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}
	public function storeReturn_return()
	{

		$sql = "SELECT trfhdr.trfbch, trfhdr.trfflc
				from trfhdr
				where trfsts = 'S' and trftlc = 8001";

	/*	$sql = "SELECT trfhdr.trfbch
				from trfhdr
				left join tblstr on tblstr.strnum = trfhdr.trfflc
				where trfsts = 'W'and tblstr.strtyp = 'U'";*/

		$query_result 	= $this->instance->runSQL($sql,true);

		$filename 		= 'reverse_logistic';
	    // so_no | store_name | so_status | order_date | created_at
		$header_column = array('move_doc_number', 'from_store_code');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	//to test
	public function storeReturnDetails_return()
	{
		$sql  = "SELECT trfhdr.trfbch, invupc.iupc, trfdtl.TRFshp
				from trfhdr
				left join trfdtl on trfhdr.trfbch = trfdtl.trfbch
				INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR 
				where trfhdr.trfsts = 'S' and trfhdr.trftlc = 8001";

		/*$sql = "SELECT TRFDTL.TRFBCH, INVUPC.IUPC,TRFDTL.TRFREQ, TRFDTL.TRFALC
				FROM TRFHDR
				RIGHT JOIN TRFDTL ON TRFDTL.TRFBCH = TRFHDR.TRFBCH
				INNER JOIN INVUPC ON TRFDTL.INUMBR = INVUPC.INUMBR
				WHERE TRFDTL.TRFSTS = 'S' AND TRFDTL.TRFTLC = 7000";*/

		$query_result 	= $this->instance->runSQL($sql,true);
		$filename 		= 'reverse_logistic_det';
	    // so_no | sku | ordered_qty | alloctated_qty | created_at
		$header_column = array('move_doc_number','upc',  'delivered_qty');
		$this->_export($query_result, $filename, $header_column, __METHOD__);
	}

	public function test()
	{
		$sql = "SELECT COUNT(*) AS counter
		        FROM WHSMVH
		        WHERE WHMCDT = 0 AND WHMVDT = {$this->instance->getDate()}
		        ";

		$this->instance->count($sql);
	}

	private function _export($query_result, $filename, $header_column, $methodName, $custom_column = NULL)
	{
		if(!$query_result)
		{
		    //No Results - Your Error Code Here
			echo "Error: {$methodName} / empty results. \n";
		}else{
			if(empty($custom_column)) $custom_column = array();
		    //Get the results
		    $key = $this->instance->tempFieldNames;
		    $result = $this->instance->export($query_result, $filename, $header_column, $custom_column);
		    // $this->instance->displayResult($query_result, $key);
		    return $result;
		}

		return false;
	}

	public function close()
	{
		$this->instance->close();
	}

}