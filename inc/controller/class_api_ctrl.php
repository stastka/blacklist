<?
class baseCtrl
{
    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }
	function contains($string, $array, $caseSensitive = true)
    {
		$strippedString = $caseSensitive ? str_replace($array, '', $string) : str_ireplace($array, '', $string);
		return $strippedString !== $string;
	}
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }
    protected function isValidIp($ip)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP))
        {
            return true;
        } 
        else
        {
            return false;
        }
    }
    protected function getQueryStringParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }
	protected function getQueryStringFilterParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $this->output);
    }
    protected function sendOutput($data, $httpHeaders=array())
    {
        header_remove('Set-Cookie');
        if (is_array($httpHeaders) && count($httpHeaders))
        {
            foreach ($httpHeaders as $httpHeader)
            {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }
}
?>