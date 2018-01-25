# Faux-Logs
A PHP script to create fake logs in a format of your choosing.  Originally written to push logs into [AuthDNS](https://github.com/Packet-Clearing-House/AuthDNS) but can be extended to write any format you want.


## Execution

Full signature of the script is:

```
Faux-Logs.php <OUTPUT_FILE> <ITERATIONS>
```

Where:
  * OUTPUT_FILE - where to write the log file
  * ITERATIONS - number of lines based on ``$fl_config['log]`` to write to destination
  
### Examples

Write to the file ``access_log`` 500 lines:

```php
php -f Faux-Logs.php access_log 500
```

Write to the file ``mon-01.xyz.foonet.net_2017-10-17.17-07.dmp`` 10,000 lines:

```php
php -f Faux-Logs.php mon-01.xyz.foonet.net_2017-10-17.17-07.dmp 10000
```



## Configuration

Faux Logs looks for a file called "config.php" which has an array defined of the log format.  
The array defines each field for a line in your faux log file.  You can look 
at ``config.dist.php`` for examples.  Be sure to copy the dist to ``config.php`` and edit that
file.  ``config.dis.php`` is ignored and just for reference!

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
    "str::-",
    "str:: ",
    "str::[",
    FAUX_DATE1,
    "str::]",
    "str:: ",
    "str::\"",
    array('GET','POST'),
    "str:: ",
    "fle::urls.txt",
    "str:: ",
    "str::HTTP/1.1",
    "str::\"",
    "str:: ",
    array('200','304','404'),
    "str:: ",
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

Resulting lines are:
```
Q 173.194.103.137 65.22.83.1 0 0 1 tormsdc11.magna.global. 51
R 173.194.103.137 65.22.83.1 0 0 1 tormsdc11.magna.global. 631 0
```

Config: 

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


### Wrapper script

Faux-Logs also ships with a wrapper script.  It will iterate over an array of 
file names, call Faux-Logs for each one and compress (gzip) the 
resulting file. If you you want to create a large repository of historical 
log files with a time stamp in the file name, here you go! 

First, copy ``config2.dist.php`` to  ``config2.php`` and edit it to have the 
file names you want.  

Full signature of the script is:

```
multi-file.gzip.php <OUTPUT_PATH> <ITERATIONS_PER_FILE> [SLEEP] [EPOCH]
```

Where:
* OUTPUT_PATH - (required) directory to write the files t
* ITERATIONS_PER_FILE - (required) How many lines to write to a file
* SLEEP - (optional) Microseconds to wait between looping over $files array
* EPOCH - (optional) Epoch time to start working form (will ignore SLEEP)

This script will not stop until you cancel the call (ctl + c).

#### Examples

Write 100 lines for each file to the ``.`` (current) directory:

```
php -f multi-file.gzip.php . 100 
```

Write 1,000 lines for each file to the ``/tmp/pcaps/`` directory sleeping 2000000 
microseconds and starting from the epoch ``1514834140``:

```
php -f multi-file.gzip.php /tmp/pcaps/ 1000 2000000 1514834140
```

**Warning!** - ``multi-file.gzip.php`` can write a lot of data quickly. Be sure you are careful 
not to fill up your boot drive or somehwere else important! Remember, it won't
stop until you tell it to! 