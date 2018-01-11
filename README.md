# Faux-Logs
A PHP script to create fake logs in a format of your choosing.  Originally written to push logs into [AuthDNS](https://github.com/Packet-Clearing-House/AuthDNS) but can be extended to write any format you want.

## Configuration

Faux Logs looks for a file called "config.php" which has an array defined of the log format.  The array defines each field for a line in your faux log file.  You can look at `config.dist.php` for examples. 

Options are:
  * `"str::foo"` - the literal string _foo_. Put any literal string you want here ;)
  * `FAUX_NL` - new line. Use when you want to write a multi-line log file
  * `FAUX_IPv4` - generate a faux routable IPv4 address
  * `FAUX_IPv6` - generate a faux routable IPv6 address
  * `array()` - randomly choose a value from this array
  * `"fle::FILE_NAME"` - read in the values from a file called `FILE_NAME` and randomly choose a value from it.  Format is plain text file, one value per line
  * `FAUX_DATE1` - current date and time in the format of "10/Jan/2018:22:45:50 +0000"
  * `"int::START-END"` - an integer between the values of `START` and `END` eg `int::1-100` would render a random value between 1 and 100.
  * `"pre::4"` - Use the 4th pre-cooked values to re-use multiple times in the log line output.  Options are the same as above, but are generated once and then can be re-used

Log fields are written to the `$fl_config['log']` array and pre-cooked values are written to the `$fl_config['log']` array.

### Example 1 - Apache log file

```php
$fl_config['log'] = array(
    FAUX_IPv4,
    "str:: ",
    "str::-",
    "str:: ",
    "str::[",
    FAUX_DATE1,
    "]",
    "str:: ",
    "str::\"",
    array('GET','POST'),
    "fle::urls.txt",
    "str::HTTP/1.1",
    "str::\"",
    array('200','304','404'),
    "int::101-90001",
    "str:: ",
    "str::\"",
    "str::-",
    "str::\"",
    "str:: ",
    "str::\"",
    "fle::useragents.txt",
    "str::\"",
    "fle::cookies.txt",
);
```

### Example 2 - Multi-line log file with pre-cooked IPs and URLs
Q 173.194.103.137 65.22.83.1 0 0 1 tormsdc11.magna.global. 51
R 173.194.103.137 65.22.83.1 0 0 1 tormsdc11.magna.global. 631 0
```php
$fl_config['pre'] = array(
    "fle::clientIPs.txt",
    "fle::urls.txt",
    "int::0-1",
    FAUX_IPv4,
);
$fl_config['log'] = array(
    "str::Q", // Q/R, flag for query or response
    "str:: ",
    "pre::3", // source IP of host making the query
    "str:: ",
    "pre::0", // nameserver IP, used to determine customer
    "str:: ",
    "pre::3", // Proto; 0=UDP, 1=TCP
    "str:: ",
    "str::0", // opCode; 0=Query, 4=Notify, 5=Update, etc.
    "str:: ",
    "str::0", // qType; 1=A, 2=NS, 5=CNAME, 6=SOA, 12=PTR, etc
    "str:: ",
    "pre::2", // query string
    "str:: ",
    "int::50-69", // packet size in bytes (used by roots)
    FAUX_NL,
    "str::R", // Q/R, flag for query or response
    "str:: ",
    "pre::3", // source IP of host making the query
    "str:: ",
    "pre::0", // nameserver IP, used to determine customer
    "str:: ",
    "pre::3", // Proto; 0=UDP, 1=TCP
    "str:: ",
    "str::0", // opCode; 0=Query, 4=Notify, 5=Update, etc.
    "str:: ",
    "str::0", // qType; 1=A, 2=NS, 5=CNAME, 6=SOA, 12=PTR, etc
    "str:: ",
    "pre::2", // query string
    "str:: ",
    "int::400-700", // packet size in bytes (used by roots)
    "str:: ",
    array('0','2','3'),
);
```

## Execution

Faux Logs takes two arguments:
  * destination - where to write the log file
  * iterations - number of lines based on ``$fl_config['log]`` to write to destination