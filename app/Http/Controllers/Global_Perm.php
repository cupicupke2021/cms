<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

class Global_Perm extends Controller
{
	//
	public function createMenu($sSession){

        $table_sitemenu     = config('tables.table_sitemenu'); 
        $table_groupmenu    = config('tables.table_groupmenu'); 
        $table_usergroup    = config('tables.table_usergroup'); 
        $table_group        = config('tables.table_group'); 
        $table_user         = config('tables.table_user'); 
        $table_user         = config('tables.table_user'); 
        $table_session      = config('tables.table_session'); 
        $table_sitemenu     = config('tables.table_sitemenu'); 

		$sSQL = "
				select d.id,d.description,d.name,d.icon
				from 
				(
				select a.userid  
				from $table_session b,$table_user a
				where 1=1 
				and b.userid = a.userid 
				and b.session = '$sSession'
				) x, 
				$table_usergroup b, 
				$table_groupmenu c,
				$table_sitemenu d
				where 1=1 
				and b.userid = x.userid 
				and c.id_group = b.id_group
				and d.id = c.id_menu
				and d.type = 'topmenu'
				
				group by d.id,d.description,d.name,d.icon
				order by d.urutan asc
				";
		//wvd($sSQL);
        $results 	= DB::select(DB::raw($sSQL));
        $array 		= json_decode(json_encode($results), true);
        
        $content    = "";
        foreach($array as $x => $val){
			//wvd($val);
			$row		= $val;
            $icona      = $val['icon'];
            $desc       = $val['description'];
			$id 		= $val['id'];

			$sSQL2 = "
						select d.id,d.description,d.name,d.icon,d.model,d.model_menu,d.model_id
						from 
						(
						select a.userid  
						from $table_session b,$table_user a
						where 1=1 
						and b.userid = a.userid 
						and b.session = '$sSession'
						) x, 
						$table_usergroup b, 
						$table_groupmenu c,
						$table_sitemenu d
						where 1=1 
						and b.userid = x.userid 
						and c.id_group = b.id_group
						and d.id = c.id_menu
						and d.parent = '$id'
						group by d.id,d.description,d.name,d.icon,d.model,d.model_menu,d.model_id
						order by d.urutan asc
					";
					
			$results2 	= DB::select(DB::raw($sSQL2));
			$array2 	= json_decode(json_encode($results2), true);
			
			if(!empty($array2)){ 
					$chd = '<ul class="treeview-menu">';
				foreach($array2 as $key2 => $val2){
					$row2 		 = $val2;
					$name2 		 = strtolower($row2['description']);
					$model2 	 = $row2['model'];
					$model_menu2 = $row2['name'];
					$nameid		 = ($model2=="template"?$model_menu2:$row2['name']);
					$linkde		 = ($model2=="template"?'/'.$row2['model_id']:"");

					$icon 		 = $row2['icon'];
					$id3 		 = $row2['id'];

					$sSQL3 = "
								select d.id,d.description,d.name,d.icon,d.model,d.model_menu,d.model_id
								from 
								(
								select a.userid  
								from $table_session b,$table_user a
								where 1=1 
								and b.userid = a.userid 
								and b.session = '$sSession'
								) x, 
								$table_usergroup b, 
								$table_groupmenu c,
								$table_sitemenu d
								where 1=1 
								and b.userid = x.userid 
								and c.id_group = b.id_group
								and d.id = c.id_menu
								and d.parent = '$id3'
								group by d.id,d.description,d.name,d.icon,d.model,d.model_menu,d.model_id
								order by d.urutan asc
								";
					
					$results3 	= DB::select(DB::raw($sSQL3));
					$array3 	= json_decode(json_encode($results3), true);
					
					if(!empty($array3)){
						$chd 	   	.= '<li class="treeview">
										<a href="#">
										<i class="'.$icon.'"></i>'.str_replace("_"," ",ucwords($name2)).'
										<span class="pull-right-container">
										<i class="fa fa-angle-left pull-right"></i>
										</span>
										</a>
										<ul class="treeview-menu">';
						foreach($array3 as $key3 => $val3){
							$icont		 = $val3['icon'];
							$model 		 = $val3['model'];
							$model_menu  = $val3['model_menu'];
							$namet		 = ($model=="template"?$model_menu:$val3['name']);
							$linkd		 = ($model=="template"?'/'.$val3['model_id']:"");
							$chd 		.= '<li><a href="'.url('/').'/system/'.$namet.'/List'.$linkd.'"><i class="'.$icont.'"></i>'.$val3['description'].'</a></li>';
						}
							$chd 		.= '</ul></li>';
					}else{ 

							$chd 	   .= '<li><a href="'.url('/').'/system/'.$nameid.'/List'.$linkde.'"><i class="'.$icon.'"></i>'.str_replace("_"," ",ucwords($name2)).'</a></li>';	
							
					}
				}
							$chd 	   .= '</ul>';
			}

        
            	$content   .= "<li class=\"treeview\">
							   <a href=\"#\">
							   <i class=\"$icona\"></i><span>$desc</span>
							   <span class=\"pull-right-container\"> <i class=\"fa fa-angle-left pull-right\"></i>
							   <span class=\"label label-primary pull-right\"></span>
							   </span>
							   </a>
							   $chd
                               </li>";
					
        }
        return $content;
    }


public function checkSession($sSession){ 

	$table_session      = config('tables.table_session'); 
	$sSQL 				= " 
							select session from $table_session
							where 1=1 
							and session = '$sSession'
							";
	
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);

	if(!empty($array)){ 
		$isUser = 1;
	}else{ 
		$isUser = 0;
	}
	return $isUser;
}

public function checkUserProp($sSession){

	$table_session      = config('tables.table_session');
	$table_user     	= config('tables.table_user');
	$table_usergroup    = config('tables.table_usergroup');

	$sSQL = "select a.userid,b.session,c.id_group,c.main,a.username
				from $table_user a,$table_session b,$table_usergroup c 
				where 1=1 
				and a.userid = b.userid
				and a.userid = c.userid 
				and b.session = '$sSession'
				";
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);

	return $array;

}

public function checkUserPropMain($sSession){ 
	$table_session      = config('tables.table_session');
	$trs_his_position   = config('tables.trs_his_position');
	$table_user   		= config('tables.table_user');
	$table_usergroup   	= config('tables.table_usergroup');

	$sSQLS 		= "
				select a.comp_id 
				from $table_session a
				where 1=1 
				and a.session = '$sSession'";

	$results 	= DB::select(DB::raw($sSQLS));
	$resultS 	= json_decode(json_encode($results), true);
	$clumn		= ($resultS[0]['comp_id']!="DEFF"?'b.comp_id':'d.comp_id');
	$sSQL = "
			select a.userid,
			b.session,
			b.rec_comp_id,
			b.rec_dept,
			b.rec_pos,
			b.rec_emp_id,
			b.rec_emp_name,
			c.id_group,c.main,d.position_id,d.emp_number,$clumn,d.emp_id,d.id as his_id
			from 
			$table_user a left join 
			(
			select a.* from 
			$trs_his_position a
			where 1=1 
			and a.active_now = 'YES'
			)d on a.userid = d.userid,
			$table_session b,$table_usergroup c
			where 1=1 
			and a.userid = b.userid
			and a.userid = c.userid
			and b.session = '$sSession'
			and c.main = '1';
			";
	//wvd($sSQL);
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);
	//wvd($array);
	if(!empty($array)){ 
		return $array[0];
	}  
}

public function checkMenuPerm($sysInfo){

	$table_sitemenu      	= config('tables.table_sitemenu');
	$table_groupmenu   		= config('tables.table_groupmenu');
	$table_usergroup   		= config('tables.table_usergroup');
	$table_group   			= config('tables.table_group');
	$table_user   			= config('tables.table_user');
	$table_session   		= config('tables.table_session');
	$table_sitemenu   		= config('tables.table_sitemenu');

	$sys 					= $sysInfo['sysdet']['sys'];
	$sSession 				= $sysInfo['sysdet']['session'];
	$sSQL = "
			select d.name
			from 
			(
			select a.userid  
			from $table_session b,$table_user a
			where 1=1 
			and b.userid = a.userid 
			and b.session = '$sSession'
			) x, 
			$table_usergroup b, 
			$table_groupmenu c,
			$table_sitemenu d
			where 1=1 
			and b.userid = x.userid 
			and c.id_group = b.id_group
			and d.id = c.id_menu
			and (d.name = '$sys' or d.model_menu = '$sys')
			";
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);
	if(!empty($array)){ 
		$menu = 1;
	}else{ 
		$menu = 0;
	}

	return $menu;
}

public function checkAccess($menuprop,$subsys) {


if($subsys != "List"){
while(list($k,$v) = @each($menuprop)){
	if($k == $subsys){ 
		$isMenu = 1;
		return $isMenu;
	}else{ 
		$isMenu = 0;
	}
	
}
}else{ 
		$isMenu = 1;
		return $isMenu;

}
}

public function checkMenuProp($sysInfo){  
	GLOBAL $dbo,$table_groupmenu,$table_sitemenu_prop,$table_sitemenu;

	$table_groupmenu   		= config('tables.table_groupmenu');
	$table_sitemenu_prop   	= config('tables.table_sitemenu_prop');
	$table_sitemenu   		= config('tables.table_sitemenu');
	$table_site_prop   		= config('tables.table_site_prop');
	
	$sys 					= strtolower($sysInfo['sysdet']['sys']);	
	$subsys 				= strtolower($sysInfo['sysdet']['subsys']);
	
	$group_array 			= $sysInfo['userprop'];
	
	if($subsys != "list"){ 
		$quer = "and x.prop = '$subsys'";
	}else{ 
		$quer = "";
	}
	
	foreach($group_array as $k=>$v){
	//while (list($k,$v) = each($group_array)){
	$groupid = $v['id_group'];
	//$groupname = $v['groupname'];
		
		$sSQL = "
			 select a.*
			 from $table_site_prop a
			 where 1=1 
			";
		
		$resultsP 	= DB::select(DB::raw($sSQL));
		$arrayP 		= json_decode(json_encode($resultsP), true);
		
		$sSQL2 = "
			 select h.name,h.id_group,x.prop,h.posting,h.approval
			 from 
			 (
			 select z.id,y.name,z.id_group,y.model_menu,y.posting,y.approval
			 from $table_groupmenu z,$table_sitemenu y 
			 where y.id = z.id_menu) h 
			 left join $table_sitemenu_prop x on h.id = x.id_groupmenu
			 where 1=1
			 and h.id_group = '$groupid'
			 and (h.name = '$sys' or h.model_menu = '$sys')
			
			 group by x.prop,h.name,h.id_group,h.posting
			";

		$results 	= DB::select(DB::raw($sSQL2));
		$array 		= json_decode(json_encode($results), true);
		if(empty($array)){
		
		$gridProp['list'] = 1; 
		$gridProp['subsys'] = "empty";
		
		}else{ 
		
		$data = array();
		//while(list($k,$v) = each($result)){
		foreach($array as $key => $val) {

		$row = $val;
		if($row['prop']==null){
			$gridProp['list'] = 1;
			$gridProp['subsys'] =  "list";
		}
		
		if($row['prop'] == "edit" and $subsys != "list"){
			$gridProp['edit'] = 1;
			$gridProp['subsys'] = "edit";
		}
		
		if($row['prop'] == "edit" and $subsys == "list"){ 
			$gridProp['edit'] = 1;
			$gridProp['subsys'] = "list";
		}
		
		if($row['prop'] == "excel" and $subsys == "list"){ 
			$gridProp['excel'] = 1;
			$gridProp['subsys'] = "list";
		}
		
		if($row['prop'] == "excelimp" and $subsys == "list"){ 
			$gridProp['excelimp'] = 1;
			$gridProp['subsys'] = "list";
		}
		
		if($row['prop'] == "edit_detail" ){ 
			$gridProp['edit_detail'] = 1;
			$gridProp['subsys'] = "edit_detail";
		}
		
		//=============================================//
		
		if($row['prop'] == "delete" and $subsys != "list"){
			$gridProp['delete'] = 1;
			$gridProp['subsys'] = "delete";
		}
		
		if($row['prop'] == "delete" and $subsys == "list"){ 
			$gridProp['delete'] = 1;
			$gridProp['subsys'] = "list";
		}
		
		if($row['prop'] == "delete_detail" ){ 
			$gridProp['delete_detail'] = 1;
			$gridProp['subsys'] = "delete_detail";
		}
		
		//=======================================================
		if($row['prop'] == "add" and $subsys != "list"){
			$gridProp['add'] = 1;
			$gridProp['subsys'] = "add";
		}
		
		if($row['prop'] == "add" and $subsys == "list"){ 
			$gridProp['add'] = 1;
			$gridProp['subsys'] = "list";
		}
	
		if($row['prop'] == "add_detail"){ 
			
			$gridProp['add_detail'] = 1;
			$gridProp['subsys'] = "add_detail";
		}
		
		//======================== Detail Data =============================
		//wvd($row);
		
		if($row['prop'] == "email" and $subsys != "list"){
		$gridProp['email'] = 1;
		$gridProp['subsys'] = "email";
		}
		if($row['prop'] == "print" and $subsys != "list"){
		$gridProp['print'] = 1;
		$gridProp['subsys'] = "print";
		}
		
		if($row['prop'] == "excel" and $subsys != "list"){
		$gridProp['excel'] = 1;
		$gridProp['subsys'] = "excel";
		}
		
		if($row['prop'] == "excelimpdtl" and $subsys != "list"){
		$gridProp['excelimpdtl'] = 1;
		$gridProp['subsys'] = "excelimpdtl";
		}
		
		if($row['prop'] == "edit_cek" and $subsys != "list"){
		$gridProp['edit_cek'] = 1;
		$gridProp['subsys'] = "edit_cek";
		}
		
		if($row['prop'] == "other" and $subsys != "list"){
		$gridProp['other'] = 1;
		$gridProp['subsys'] = "other";
		}
		
	}
		$gridProp['posting'] 		= $array[0]['posting'];
		$gridProp['approval'] 		= $array[0]['approval'];
		$gridProp['decimal_place'] 	= $arrayP[0]['decimal_places'];
		$gridProp['currency'] 		= $arrayP[0]['currency'];
	return $gridProp;
	}
	}
	
}

public function checkSysDesc($sys){ 
	$table_sitemenu   		= config('tables.table_sitemenu');

	$sSQL = "select a.description
			from $table_sitemenu a
			where 1=1 
			and (a.name = '$sys' or a.model_menu = '$sys')
			";
	
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);
	return $array;
}

public function checkSysEnv(){ 
	$table_site_prop   		= config('tables.table_site_prop');

	$sSQL = "select a.*
			from $table_site_prop a
			where 1=1 
			";
	
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);
	return $array;
}

public function ctrlButton($sys,$check_menu_prop){ 
	/// main ///
	if(isset($check_menu_prop['add'])){ 
	$add	 = "";
	$add	.= '<input type="button" />';
	}else{ 
	$add	 = "";
	}

	if(isset($check_menu_prop['edit'])){
	$edit	 = array();
	$edit['header']	 = "<th>Edit</th>";
	$edit['contrl']	 = '<td><input type="button" /></td>';
	}else{ 
	$edit 	 = "";
	}

	if(isset($check_menu_prop['delete'])){
	$delete	 = array();
	$delete['header']	= "<th>Edit</th>";
	$delete['contrl']	= '<td><input type="button"/></td>';
	}else{ 
	$delete  = "";
	}

	/// detail ///

	if(isset($check_menu_prop['add_detail'])){ 
	$addDtl	 = "";
	$addDtl	.= '<input type="button" />';
	}else{ 
	$addDtl  = "";
	}
	
	if(isset($check_menu_prop['edit_detail'])){
	$editDtl			 = array();
	$editDtl['header']	 = "<th>Edit</th>";
	$editDtl['contrl']	 = "<th>Edit</th>";
	}else{ 
	$editDtl  = "";
	}
	
	if(isset($check_menu_prop['delete_detail'])){
	$deleteDtl				 = array();
	$deleteDtl['header']	 = "<th>Edit</th>";
	$deleteDtl['contrl']	 = "<th>Edit</th>";
	}else{ 
	$deleteDtl 				 = "";
	}

	$aryControl =  [
					'add'=>$add,
					'edit'=>$edit, 
					'delete'=>$delete,
					'add_detail'=>$addDtl,
					'edit_detail'=>$editDtl,
					'delete_detail'=>$deleteDtl
	];
	return $aryControl;
}


public function checkSysAccount($sysInfo,$sys_prop){
		$table_sitemenu   		= config('tables.table_sitemenu');
		$mst_docnum   			= config('tables.mst_docnum');
	
		$sys = $sysInfo['sysdet']['sys'];
		$sSQL = "select a.id,a.docnum,a.posting,a.journ_set
				from $table_sitemenu a
				where 1=1 
				and a.name = '$sys'
				";
		$results 	= DB::select(DB::raw($sSQL));
		$array 		= json_decode(json_encode($results), true);
		$aryRes 	= $array;
		
		foreach($aryRes as $k=>$v){
			$id_menu = $v['id'];
			foreach($v as $x=>$y){
				switch($x){
					case "docnum":
					//wvd($y);
					if($y == "true"){ 
					$sSQL = "select a.id_menu
							 from $mst_docnum a
							 where 1=1 
							 and a.id_menu = '$id_menu'
				            ";
					
					$results 	= DB::select(DB::raw($sSQL));
					$result 	= json_decode(json_encode($results), true);
					if(empty($result)){
						$docnum = 0;
					}else{ 
						$docnum = 1;
					}
					}else{ 
						$docnum = 2;
					}
					break;
				}
				
			}
		
		}
			return array("docnum"=>$docnum,"posting"=>null);
}


public function checkDocProp($sys,$id){
	$trs_posting   		= config('tables.trs_posting');
	$id 				= base64url_decode($id);
	$sys 				= $sys;

	$sSQL 		= "select a.*
				   from $trs_posting a
				   where 1=1 
				   and a.module = '$sys'
				   and a.trs_id = '$id'
				";
	//wvd($sSQL);
	$results 	= DB::select(DB::raw($sSQL));
	$array 		= json_decode(json_encode($results), true);

	if(!empty($array)){
		$posting 	= array("posting"=>1); 
	}else{ 
		$posting 	= array("posting"=>0);
	}
	return $posting;
}

public function checkDocPerm($sys){
	$table_sitemenu   		= config('tables.table_sitemenu');
	$sys 					= $sys;

	$sSQL 		= "select a.posting,a.docnum
				   from $table_sitemenu a
				   where 1=1 
				   and a.name = '$sys'
				";
	// wvd($sSQL);exit;
	if(!empty($results)){
	$array 		= json_decode(json_encode($results[0]), true);
	}else{ 
	//$array 	= array("docnum"=>"","posting"=>"");
	$array 	= null;
	}

	return $array;
}


}
