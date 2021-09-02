<?php
function erpSysIndex($aryCnt){

    $sys            = $aryCnt['sysinfo']['sysdet']['sys'];
    $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];

    function sysList($sys,$aryCnt){
        $table_session = config('tables.table_session');
        
        //base variable 
        $subsys     = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode       = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp   = $aryCnt['menuProp'];
        $gen        = $aryCnt['gen'];
        $ssp        = $aryCnt['ssp'];
        $userProp   = $aryCnt['userProp'];
        $request    = $aryCnt['request'];
        

		$sSession	= $userProp[0]['session'];
		
		DB::table($table_session)->where('session', '=', $sSession)->delete();
		$request->session()->forget('dSession');
		$delete 	= $gen->deleteRowData($table_session,'session',$sSession);
		$url = url('/');
		erpRedirect($url,"toast","Logout Success !!","success");
		//return redirect()->intended($url);
    }
	

    switch($subsys){
        case "List":
            return sysList($sys,$aryCnt);
        break;

}
}