# RDAP CLIENT

RDAP Client For PHP (Formerly Whois via http protocol)

## Requirements

- php `8.1` or later
- `ext-json`
- `ext-intl`

### IETF RDAP Reference

- Registration Data Access Protocol (RDAP) Object Tagging **[#RFC8521](https://datatracker.ietf.org/doc/html/rfc8521)**
- Security Services for the Registration Data Access Protocol (RDAP) **[#RFC7481](https://datatracker.ietf.org/doc/html/rfc7481)**
- Registration Data Access Protocol (RDAP) Query Format **[#RFC9082](https://datatracker.ietf.org/doc/html/rfc9082)**
- JSON Responses for the Registration Data Access Protocol (RDAP) **[#RFC9083](https://datatracker.ietf.org/doc/html/rfc9083)**
- Finding the Authoritative Registration Data (RDAP) Service **[#RFC9224](https://datatracker.ietf.org/doc/html/rfc9224)**

Schema Inventory & Analysis of WHOIS object

- Inventory and Analysis of WHOIS Registration Objects **[#RFC7485](https://datatracker.ietf.org/doc/html/rfc7485)**

### IANA RDAP Data

Iana provide data about rdap

- HTTP : [https://data.iana.org/rdap/](https://data.iana.org/rdap/)

- Bootstrap Service Registry for AS Number Space **[#RFC9224](https://datatracker.ietf.org/doc/html/rfc9224)** [https://www.iana.org/assignments/rdap-asn/rdap-asn.xhtml](https://www.iana.org/assignments/rdap-asn/rdap-asn.xhtml)
- Bootstrap Service Registry for Domain Name Space **[#RFC9224](https://datatracker.ietf.org/doc/html/rfc9224)** [https://www.iana.org/assignments/rdap-dns/rdap-dns.xhtml](https://www.iana.org/assignments/rdap-dns/rdap-dns.xhtml)
- Bootstrap Service Registry for IPv4 Address Space **[#RFC9224](https://datatracker.ietf.org/doc/html/rfc9224)** [https://www.iana.org/assignments/rdap-ipv4/rdap-ipv4.xhtml](https://www.iana.org/assignments/rdap-ipv4/rdap-ipv4.xhtml)
- Bootstrap Service Registry for IPv6 Address Space **[#RFC9224](https://datatracker.ietf.org/doc/html/rfc9224)** [https://www.iana.org/assignments/rdap-ipv6/rdap-ipv6.xhtml](https://www.iana.org/assignments/rdap-ipv6/rdap-ipv6.xhtml)
- Bootstrap Service Registry for Provider Object Tags **[#RFC8521](https://datatracker.ietf.org/doc/html/rfc8521)** [https://www.iana.org/assignments/rdap-object-tags/rdap-object-tags.xhtml](https://www.iana.org/assignments/rdap-object-tags/rdap-object-tags.xhtml)
- Recovered IPv4 Pool **[#RFC8521](https://datatracker.ietf.org/doc/html/rfc8521)** [https://www.iana.org/assignments/ipv4-recovered-address-space/ipv4-recovered-address-space.xhtml](https://www.iana.org/assignments/ipv4-recovered-address-space/ipv4-recovered-address-space.xhtml)

For list of predefined recovered IPv4 addresses: **[RecoveredIPv4.php](src/Services/RecoveredIPv4.php)**

### Example Usage


See [Client.php](src/Client.php) for more methods


```php
use ArrayAccess\RdapClient\Client;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Protocols\AsnProtocol;
use ArrayAccess\RdapClient\Protocols\DomainProtocol;
use ArrayAccess\RdapClient\Protocols\IPv4Protocol;
use ArrayAccess\RdapClient\Protocols\IPv6Protocol;
use ArrayAccess\RdapClient\Protocols\NsProtocol;

$client = new Client();
/**
 * @var RdapRequestInterface<string, DomainProtocol> $request
 */
$domainName = 'example.com';
$request = $client->request($domainName);

/**
 * @var RdapRequestInterface<string, IPv4Protocol> $request
 */
$ipv4 = '192.0.47.59'; // iana.org ipv4
$request = $client->request($ipv4);

/**
 * @var RdapRequestInterface<string, IPv6Protocol> $request
 */
$ipv6 = '2404:6800:4003:c01::66'; // google.com
$request = $client->request($ipv6);

/**
 * @var RdapRequestInterface<string, NsProtocol> $request
 * Name server guessing by prefix (ns[0-9]*).domain-name.ext or [^\.]+.(ns[0-9]*.[^\.]+\.)(?:.+).domain-name.ext
 */
$nameserver = 'ns1.google.com'; // google name server
$request = $client->request($nameserver);

/**
 * @var RdapRequestInterface<string, AsnProtocol> $request
 * Autonomous System Number parsed by "^(?ASN?)?(?<as_number>[0-9]+)$"
 */
$asNumber = 'AS15169'; // Google LLC ASN
// or just put the numeric string / integer
$request = $client->request($asNumber);

```

```php
// getting object response
$response = $request->getResponse();
// getting json data
$jsonResponse = $response->getResponseJson();
// getting definition object
$definition = $response->getDefinition();
// if domain > getting related / another whois server request if possible
$alternateRequest = $definition->getRelatedRequest();
// json serialize
$fallbackToJson = json_encode($definition, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
```

```php
// direct call with custom request target
// makes sure the type of RDAP uri target is equal '/domain/GOOGLE.com' as domain-protocol
$newRequest = $request->withRdapSearchURL('https://rdap.markmonitor.com/rdap/domain/GOOGLE.COM');
$response = $newRequest->getResponse();
```


See [Response/Definitions](src/Response/Definitions) for more details about code


## Note

The code of data definition contains strict types.
Some of the invalid data will throw an error.

## WHOIS Data Collection

Refer to: _(gist)_ [WHOIS List](https://gist.github.com/ArrayIterator/1a8df2b5c59f50990661f11c050c7c2a) to get the list of whois servers / ip range / sTLD etc.

**Caution!** the gist contains huge data.

## LICENSE

[GPL-3.0-or-later](LICENSE)
