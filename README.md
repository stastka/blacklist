# Simple single ip blacklist written in PHP

Tested on 
- Linux:
  - Synology DS7.1 
  - Apache 2.4, php7.4, php8.0 MariaDB 10
- Windows: 
  - Windows 10
  - Windows Server 2019
  - IIS10, php8, MySQL 5.6

## Security
`Only for Internal Usage`
- Authentication
  - Basic authentication

- Input Validaten of:
  - Valid IPv4
  - Valid DateTime
  - Validation of uri Path

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


## License

MIT

