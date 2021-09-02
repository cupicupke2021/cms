<?php
function erpSysIndex($aryCnt){
    $sys        = route::input('sys');
    $subsys     = route::input('subsys');
    
    function sysList($aryCnt,$sys){
        $subsys         = $aryCnt['sysinfo']['sysdet']['subsys'];
        $mode           = $aryCnt['sysinfo']['sysdet']['mode'];
        $menuProp       = $aryCnt['menuProp'];
        $gen            = $aryCnt['gen'];
        $ssp            = $aryCnt['ssp'];
        $userProp       = $aryCnt['userProp'];
        $userPropMain   = $aryCnt['userPropMain'];

        $view   = "";
        view()->addLocation(app_path('Systems/'.$sys));
        $view   .= View::make('flist')->with(['success' => 'Welcome '.$userPropMain['userid'].'']);

        return $view;
    }

    function sysEdit($sys){ 
        $view   = "";
        
    }

    switch($subsys){ 
        case "List":
            $render = sysList($aryCnt,$sys);
            return $render;
        break;
    }
}