# Simple single ip blacklist written in PHP

Tested on 
- Linux:
  - Synology DSM 7.1.x
  - Ubuntu 22.04 LTS
  - Apache 2.4, php7.4, php8.0 MariaDB 10
- Windows: 
  - Windows 10
  - Windows Server 2019
  - Ils 10, php8, MySQL 5.6/8
- Firwall:
  - opensense (URL Table (IPs))

## Security
`Only for Internal Usage`
- Authentication
  - Basic authentication

```powershell
$user = "restusername"
$pass = "restpassword"
$pair = "$($user):$($pass)"
$encodedCreds = [System.Convert]::ToBase64String([System.Text.Encoding]::ASCII.GetBytes($pair))
$Headers = @{
    Authorization = "Basic $encodedCreds"
}
```


- Input validaten of:
  - Valid IPv4
  - Valid DateTime
  - Validation of uri Path
## Functions
- Display Ipv4 Ip-list Plain Text (for Firewalls)
- Singele IPv4 to Add,Update (full), Update (DateTime) or Delete
- Temporary allow Ip for Internetaccess (DatTime)
## Installation
- Clone Repo to your Home Webserver
- Create MySQL/MariaDB (Schema on /install/db.sql)
- Create DB-User
- Rename /inc/config.sample.php to /inc/config.php and change passwords
- Check with Powershell or curl the Rest-Api, sample on /install/restapi-check.ps1

## APIs

List of IPs and fqdn (json)
- [x]  GET /\<folder\>/\<alias\>/app/ip/list

List of IPs (raw/plain text)
- [x]  GET /\<folder>\/\<alias\>/app/ip/raw
- [x]  GET /\<folder\>/\<alias\>/app/ip/raw.txt

Get Status of one IP (json)
- [x]  GET /\<folder>\/\<alias\>/app/ip/show?filter=\<ipv4\>

changes of IPs (json)
- [x]  POST /\<folder\>/\<alias\>/app/ip/add
- [x]  POST /\<folder\>/\<alias\>/app/ip/delete
- [x]  POST /\<folder\>/\<alias\>/app/ip/update
```powershell
#Allow IP for 1 Hour
$server = "server.lan"
$app ="blacklist"
[string]$mySqlDateTime = $(get-date).AddHours(1).ToString("yyyy-MM-dd HH:mm:ss")
[string]$ip = "172.16.4.199"
$Body = @{
    "ip"="$ip"
    "dt"="$mySqlDateTime"
}| ConvertTo-Json -Compress 
Invoke-RestMethod -Uri "https://$server/$app/app/ip/update" -Method POST -Body $Body -Headers $Headers
```

- [x]  POST /\<folder\>/\<alias\>/app/ip/update?mode=full
```powershell
#Full Update IP and FQDN
$server = "server.lan"
$app ="blacklist"
[string]$ip = "172.16.4.199"
[string]$fqdn = "app199.local"
$Body = @{
    "ip"="$ip"
    "fqdn"="$fqdn"
}|ConvertTo-Json -Compress 
Invoke-RestMethod -Uri "https://$server/$app/app/ip/update" -Method POST -Body $Body -Headers $Headers
```



helper (raw/plain text)
- [x]  POST /\<folder\>/\<alias\>/app/ip/md5
- [x]  POST /\<folder\>/\<alias\>/app/ip/sha1
- [x]  POST /\<folder\>/\<alias\>/app/ip/version

## Next Steps
- IPv6 Support and valdidation
- IP-Range Support
- ACL, Readonly API-User
- Caching of List
- API Keys

## Copyright
    Copyright © 2023 D.Stastka // stastka.ch

## License

MIT © [Daniel Stastka](https://github.com/stastka)

