# L5D-Salvia

- Difficulty: ★★
- Type：KoH, Web, Misc
- Arbitrary unserialize with lots of gadgets
- Target:
    - A+B Problem
    - Find max value in array
    - Sort an array
- Rank
    - Difficulty > Execution Time > Payload length
- Best payload (from [Goburin'](https://gobur.in/)):

```
a:3:{i:0;O:9:"InputAll2":0:{}i:1;O:7:"ForLoop":4:{s:1:"i";i:0;s:3:"len";O:6:"opMUL2":2:{s:4:"arg1";O:7:"GetSize":0:{}s:4:"arg2";r:6;}s:7:"tmp_idx";i:0;s:2:"op";a:4:{i:0;O:7:"SetArg1":1:{s:3:"val";O:6:"opDIV2":2:{s:4:"arg1";O:12:"GetTmpArrVal":1:{s:3:"idx";i:0;}s:4:"arg2";r:6;}}i:1;O:7:"SetArg2":1:{s:3:"val";O:6:"opMOD2":2:{s:4:"arg1";r:12;s:4:"arg2";r:6;}}i:2;O:7:"Bigger2":2:{s:4:"arg1";O:8:"GetValue":1:{s:1:"i";O:7:"GetArg2":0:{}}s:4:"arg2";O:8:"GetValue":1:{s:1:"i";O:7:"GetArg1":0:{}}}i:3;O:8:"IF_ELSE2":1:{s:3:"op1";s:67:"O:4:"Swap":2:{s:1:"i";O:7:"GetArg1":0:{}s:1:"j";O:7:"GetArg2":0:{}}";}}}i:2;O:9:"WriteFile":3:{s:1:"i";i:0;s:3:"len";r:6;s:6:"option";s:10:"before_end";}}
```

(bubble sort with single for loop)

<br>

![](https://github.com/w181496/My-CTF-Challenges/blob/master/Balsn-CTF-2020-Onsite/final_score.png)

