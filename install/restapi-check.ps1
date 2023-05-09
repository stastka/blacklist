<# selfsign certificate #>
add-type @"
    using System.Net;
    using System.Security.Cryptography.X509Certificates;
    public class TrustAllCertsPolicy : ICertificatePolicy {
        public bool CheckValidationResult(
            ServicePoint srvPoint, X509Certificate certificate,
            WebRequest request, int certificateProblem) {
            return true;
        }
    }
"@
[System.Net.ServicePointManager]::CertificatePolicy = New-Object TrustAllCertsPolicy

<# const #>
$server = "server.lan"
$app ="blacklist"
$user = "restusername"
$pass = "restpassword"
[string]$mySqlDateTime = $(get-date).AddSeconds(60).ToString("yyyy-MM-dd HH:mm:ss")

<# Basic Authorization #>
$pair = "$($user):$($pass)"
$encodedCreds = [System.Convert]::ToBase64String([System.Text.Encoding]::ASCII.GetBytes($pair))
$basicAuthValue = "Basic $encodedCreds"
$Headers = @{
    Authorization = $basicAuthValue
    debug = $true
}
$Headers = @{
    Authorization = $basicAuthValue
}




<# Get List --------------------------------------------------------------------------------------#>
Invoke-RestMethod -Uri "https://$server/$app/app/ip/list" -Method GET -Headers $Headers | ft


<# Add ip, and FQDN ------------------------------------------------------------------------------#>
$ip = "172.16.4.201"
$fqdn = "app201.local"
[string]$mySqlDateTime =""
$Body = @{
    "ip"="$ip"
    "fqdn"="$fqdn"
    "dt"="$mySqlDateTime"
}
$JsonBody = $Body | ConvertTo-Json -Compress 
Invoke-RestMethod -Uri "https://$server/$app/app/ip/add" -Method POST -Body $JsonBody -Headers $Headers
$Body = @{
    "ip"="$ip"
}
$JsonBody = $Body | ConvertTo-Json -Compress 
Invoke-RestMethod -Uri "https://$server/$app/app/ip/delete" -Method POST -Body $JsonBody -Headers $Headers




<# Add ip, without FQDN --------------------------------------------------------------------------#>
$ip = "172.16.4.199"
$Body = @{
    "ip"="$ip"
}
$JsonBody = $Body | ConvertTo-Json -Compress 
Invoke-RestMethod -Uri "https://$server/$app/app/ip/add" -Method POST -Body $JsonBody -Headers $Headers

<# Update ip with FQDN and Dt#>
[string]$mySqlDateTime = $(get-date).AddSeconds(60).ToString("yyyy-MM-dd HH:mm:ss")
$ip = "172.16.4.199"
$fqdn = "app199.local"
$Body = @{
    "ip"="$ip"
    "fqdn"="$fqdn"
    "dt"="$mySqlDateTime"
}
$JsonBody = $Body | ConvertTo-Json -Compress 

<# Update only Dt #>
Invoke-RestMethod -Uri "https://$server/$app/app/ip/update" -Method POST -Body $JsonBody -Headers $Headers
<# Update Full #>
Invoke-RestMethod -Uri "https://$server/$app/app/ip/update?mode=full" -Method POST -Body $JsonBody -Headers $Headers

<# Show  --------------------------------------------------------------------------#>
Invoke-RestMethod -Uri "https://$server/$app/app/ip/show?filter=172.16.4.201" -Method GET -Headers $Headers | ft



<# Helper  --------------------------------------------------------------------------#>
Invoke-RestMethod -Uri "https://$server/$app/app/ip/md5" -Method GET -Headers $Headers

Invoke-RestMethod -Uri "https://$server/$app/app/ip/sha1" -Method GET -Headers $Headers

Invoke-RestMethod -Uri "https://$server/$app/app/ip/version" -Method GET -Headers $Headers


