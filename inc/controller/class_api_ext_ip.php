<?php
/*

*/
class ipCtrl extends baseCtrl
{
    public $isDebug=false;

    public function cmd__version($_user_apikey)
    {
		echo "v".APP_VERSION;
	}
	public function cmd__md5($_user_apikey)
    {
		$intLimit = 1000;
		$ipDbObj = new ipModel();
		$arrIps = $ipDbObj->getRawList($intLimit);
        $getRawList="";
        foreach ($arrIps as $value) {
             $getRawList .= $value['ip']."\n";
        }
		echo md5($getRawList);
	}
    public function getAuthToken()
    {
        $headers=array();
        foreach (getallheaders() as $name => $value)
        {
            //$headers[$name] = $value;
            //echo $name .">". $headers[$name].'          ';
            if($name == "Authorization")
            {
                $user_apikey = $value;
            }
            if(strtolower($name) == "debug")
            {
                $this->isDebug = true;
            }
        }
    }
    public function cmd__sha1($_user_apikey)
    {
		$intLimit = 1000;
		$ipDbObj = new ipModel();
		$arrIps = $ipDbObj->getRawList($intLimit);
        $getRawList="";
        foreach ($arrIps as $value) {
             $getRawList .= $value['ip']."\n";
        }
		echo sha1($getRawList);
	}
    /*
        /blacklist/app/ip/show?filter=172.16.4.201
    */
	public function cmd__show($_user_apikey)
    {
        if(!$this->contains($_user_apikey,API_Key,false))
        {
            //header("HTTP/1.1 401 Unauthorized");
            //exit();
        }
		

		$responseData ='{"msg":"N/A"}';
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
		$arrQueryStringParams = $this->getQueryStringFilterParams();
        
		//var_dump($_SERVER['QUERY_STRING']);	
		//var_dump($this->output['ip']);
		
		
		if (strtoupper($requestMethod) == 'GET')
		{
			if (isset($this->output['filter']) && $this->output['filter']) {
				
				$ipDbObj = new ipModel();
				//$cArrFromIp = $ipDbObj->cEntry($this->output['filter']);
				$arrIps = $ipDbObj->getEntry($this->output['filter']);
                //var_dump($arrUsers);
                $responseData = json_encode($arrIps);
			}
			else
			{
				if(isset($responseData))
				{
					$responseData ='{"msg":"N/A"}';
				}
			}
			   // send output

            if (!$strErrorDesc)
            {
                $this->sendOutput($responseData,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
		    }
            else 
            {
				$this->sendOutput(json_encode(array('error' => $strErrorDesc)), array('Content-Type: application/json', $strErrorHeader));
			}
        }
        else
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), array('Content-Type: application/json', $strErrorHeader));
        }
    }
    
	protected function getApiAuthCheck($_user_apikey)
    {
        if(!$this->contains($_user_apikey,API_Key,false))
        {
            header("HTTP/1.1 401 Unauthorized");
            exit();
        }
    }
    protected function getApiUserName($_user_apikey)
    {
        $username="";
        if($this->contains($_user_apikey,API_Key,false))
        {
            $username = $this->getUsernameFromApiKey($_user_apikey);
        }
        return $username;
    }
	
	
    /*
        * "/ip/list" Endpoint - Get list of ip
    */
    public function cmd__update($_user_apikey)
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->getApiAuthCheck($_user_apikey);
        $username   = $this->getApiUserName($_user_apikey);
        $remoteip   = $this->getClientIP();
      
        if (strtoupper($requestMethod) == 'POST') 
        {
            $mode="dt";
            if(isset($_GET['mode']))
            {
                $mode = $_GET['mode'];
            }

            $data   = json_decode(file_get_contents('php://input'), true);
            $ip     = trim($data['ip'] ?? '');
            $fqdn   = trim($data['fqdn'] ?? '');
            $dt     = trim($data['dt'] ?? '');

            $ipDbObj = new ipModel();
            $isValidIp = $this->isValidIp($ip);
            $isValidDt = $this->isValidDt($dt);

            $cArrFromIp     = $ipDbObj->cEntry($ip,$fqdn);
            $cCountIp       = trim($cArrFromIp[0]['cCount'] ?? '');

            if($cCountIp == 0 || !$isValidIp  || !$isValidDt )
            {
                if(! $isValidIp)
                {
                    $strErrorDesc = 'Method not supported, ip not valid';
                }
                else if(! $isValidDt)
                {
                    $strErrorDesc = 'Method not supported, Datetime not valid';
                }
                else
                {
                    $strErrorDesc = 'Method not supported, not found';
                } 
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
            else
            {
                if($fqdn=="")
                {
                    $fqdn="none";
                }
                if($dt=="")
                {
                    $dt=$this->getDatetimeNow();
                }
                if($mode == "full")
                {   //Update full Entry
                    $objEntry = $ipDbObj->updateEntry($ip,$fqdn,$dt);
                }
                else
                {
                    //Update only Date
                    $objEntry = $ipDbObj->updateEntryDate($ip,$fqdn,$dt);
                }
				if($fqdn!="none")
				{
					$objEntry = $ipDbObj->getEntry($fqdn);
				}
				else
				{
					$objEntry = $ipDbObj->getEntry($ip);
				}
                if($this->isDebug)
                {
                    $objEntry[0]["ALIAS"]=APP_ALIAS; //add Alias
                    $objEntry[0]["APP"]=APP_NAME; //add Appname
                    $objEntry[0]["VERSION"]=APP_VERSION; //add version
                    $objEntry[0]["validip"]=$isValidIp; //add valid of ip
                    $objEntry[0]["validdt"]=$isValidDt; //add valid of dt
                }
               
                $objEntry[0]["method"]="update"; //add Method
                $objEntry[0]["mode"]=$mode; //add Update Mode
                $objEntry[0]["dt"]=$dt; //add dt
                $objEntry[0]["ip"]=$ip; //add ip
               
                if($mode == "full")
                {
                    $objEntry[0]["fqdn"]=$fqdn; //add fqdn
                }

				$ipDbObj->addLog("update",$ip,$fqdn,$dt,$username,$remoteip);
                //var_dump($objEntry);
                $responseData = json_encode($objEntry);
            }
			if (!$strErrorDesc) 
            {
                $this->sendOutput($responseData,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
			} 
            else 
            {
				$this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
			}
        }
        else
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
        }
    }
    /*
        * "/ip/dalete" Endpoint - Remove Entry
    */
    public function cmd__delete($_user_apikey)
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->getApiAuthCheck($_user_apikey);
        $username   = $this->getApiUserName($_user_apikey);
        $remoteip   = $this->getClientIP();
        
        if (strtoupper($requestMethod) == 'POST')
        {
            $ipDbObj   = new ipModel();
            $data       = json_decode(file_get_contents('php://input'), true);
            $ip         = trim($data['ip'] ?? '');

            $isValidIp  = $this->isValidIp($ip);
            $cArrFromIp       = $ipDbObj->cEntry($ip);
            //var_dump($cUsersArr);
            $cCount     = trim($cArrFromIp[0]['cCount'] ?? '');
            if($cCount == 0 || !$isValidIp)
            {
                $strErrorDesc = 'Method not supported, ip not found';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
            else
            {
                $objEntry = $ipDbObj->getEntry($ip);
                $delEntry = $ipDbObj->deleteEntry($ip);
				$ipDbObj->addLog("delete",$ip,"","",$username,$remoteip);
               


                if($this->isDebug)
                {
                    $objEntry[0]["ALIAS"]=APP_ALIAS; //add Alias
                    $objEntry[0]["APP"]=APP_NAME; //add Appname
                    $objEntry[0]["VERSION"]=APP_VERSION; //add version
                    $objEntry[0]["validip"]=$isValidIp; //add is valid ip
                }
                $objEntry[0]["method"]="delete"; //add method
                $responseData = json_encode($objEntry);
                //var_dump($objEntry);
                //$responseData ='{"ip":"'.$ip.'","action":"deleted"}';
            }
        }
        else
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output
        if(!$strErrorDesc)
        {
            $this->sendOutput($responseData,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
        } 
        else 
        {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
        }
    }
    /*
        * "/ip/raw" Endpoint - Get list of ip plain Text
    */
    public function cmd__raw($_user_apikey)
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {

            try 
            {
                /* optional log rawlist request */
                $username="";
                $remoteip=$this->getClientIP();
                if($this->contains($_user_apikey,API_Key,false))
                {
                   $username = $this->getUsernameFromApiKey($_user_apikey);
                }
                /* optional log rawlist request */

                $ipDbObj = new ipModel();
                $intLimit = 10;

                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrIps = $ipDbObj->getRawList($intLimit);
                $getRawList="";
                foreach ($arrIps as $value) {
                    $getRawList .= $value['ip']."\n";
                }
            
                 /* optional log rawlist request */
                $ipDbObj->addLog("rawlist","","","",$username,$remoteip);
            } 
            catch (Error $e) 
            {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } 
        else 
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        /* */
        // send output
        if (!$strErrorDesc)
        {
//60*60*3
			$now = time( ); $then = gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $now + 60); header("Expires: $then");
            $this->sendOutput($getRawList,array('Content-Type: text/plain', 'HTTP/1.1 200 OK'));
        } 
        else 
        {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
        }
    }
    /*
        * "/ip/add" Endpoint - add Entry
    */
    public function cmd__add($_user_apikey)
    {
        if(!$this->contains($_user_apikey,API_Key,false))
        {
            header("HTTP/1.1 401 Unauthorized");
            exit();
        }
		else
		{
			$username="";
			$remoteip=$this->getClientIP();
			if($this->contains($_user_apikey,API_Key,false))
			{
			   $username = $this->getUsernameFromApiKey($_user_apikey);
			}
		}
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') 
        {
            $data   = json_decode(file_get_contents('php://input'), true);
			$ip     = trim($data['ip'] ?? '');
            $fqdn   = trim($data['fqdn'] ?? '');
            $dt     = trim($data['dt'] ?? '');

            if($dt=="")
            {
                $dt=$this->getDatetimeNow();
            }
            $ipDbObj = new ipModel();
            $isValidIp = $this->isValidIp($ip);
            $isValidDt = $this->isValidDt($dt);
            $cArrFromIp = $ipDbObj->cEntry($ip);
            //var_dump($cUsersArr[0]['cCount']);
            //var_dump($cUsersArr);
            $cCountIp = trim($cArrFromIp[0]['cCount'] ?? '');
            //$cUsers = trim($cUsersArr['cUsers'] ?? '');
            if($cCountIp == 0 && $isValidIp  && $isValidDt )
            {
                
				if($ip != "")
				{
					$addArr = $ipDbObj->addEntry($ip,$fqdn,$dt);
					$objEntry = $ipDbObj->getEntry($ip);
					$ipDbObj->addLog("add",$ip,$fqdn,$dt,$username,$remoteip);
					//var_dump($UsersArr);
					//$responseData = json_encode($addArr);
                    if($this->isDebug)
                    {
                        $objEntry[0]["ALIAS"]=APP_ALIAS; //add Alias
                        $objEntry[0]["APP"]=APP_NAME; //add Appname
                        $objEntry[0]["VERSION"]=APP_VERSION; //add Alias
                        $objEntry[0]["validip"]=$isValidIp; //add Methode
                        $objEntry[0]["validdt"]=$isValidDt; //add Methode
                    }
                    $objEntry[0]["method"]="add"; //add Methode
                    $responseData = json_encode($objEntry);
				}
				else
				{
					$strErrorDesc = 'Method not supported, ip is matadory';
					$strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
				}
            }
            else
            {
                $strErrorDesc = 'Method not supported, duplicate content';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }
        }
        else
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc)
        {
            $this->sendOutput($responseData,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
        }
        else
        {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
        }

    }
     /*
        * "/ip/list" Endpoint - Get all Entry
    */
    public function cmd__list($_user_apikey)
    {
		$username="";
		$remoteip=$this->getClientIP();
		if($this->contains($_user_apikey,API_Key,false))
        {
           $username = $this->getUsernameFromApiKey($_user_apikey);
        }
		
		

        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') 
        {
            try 
            {

                $ipDbObj = new ipModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {

                    $intLimit = $arrQueryStringParams['limit'];
                }

                $arrIps = $ipDbObj->getEntrys($intLimit);
				$ipDbObj->addLog("list","","","",$username,$remoteip);
                for($n=0;$n<count($arrIps);$n++)
                {
                    //add Field methode
                    $arrIps[$n]["method"]="list";
                }
                $responseData = json_encode($arrIps);

            } 
            catch (Error $e)
            {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }

        } 
        else
        {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) 
        {
            $this->sendOutput($responseData,array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
        }
        else 
        {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),array('Content-Type: application/json', $strErrorHeader));
        }
    }
    /*
    Helper
    */
    function getDatetimeNow() {
		$tz_object = new DateTimeZone('Europe/Zurich');
		$datetime = new DateTime();
		$datetime->setTimezone($tz_object);
        //return "0000-00-00 00:00:00";
		return $datetime->format('Y\-m\-d\ H:i:s');
	}
    function isValidDt($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
	function getClientIP():string
	{
		$keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
		foreach($keys as $k)
		{
			if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP))
			{
				return $_SERVER[$k];
			}
		}
		return "UNKNOWN";
	}
	function getUsernameFromApiKey($apikey):string
	{
        if(isset($apikey))
        {
            $base64 = explode(" ",$apikey);
            $decoded = base64_decode($base64[1]);
            list($username,$password) = explode(":",$decoded);
            //echo $username," ",$password;
            return $username;
        }
        else
        {
            return "";
        }
	}

}?>