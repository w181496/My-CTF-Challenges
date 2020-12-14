import random

arr = []
ans = -1
plus = 0
for i in range(100000,1000000):
    num = random.randint(1,300000000)
    ans = max(ans, num)
    arr.append(str(num))

plus = int(arr[0]) + int(arr[1])

with open("/bot/gen/testcase.txt", "w") as f:
    f.write(' '.join(arr))

with open("/bot/gen/answer0.txt", "w") as f:
    f.write(str(plus))

with open("/bot/gen/answer1.txt", "w") as f:
    f.write(str(ans))

arr = []

# sort

for i in range(100,500):
    num = random.randint(1,300000000)
    arr.append((num))

with open("/bot/gen/testcase2.txt", "w") as f:
    f.write(' '.join(arr))

arr.sort()

with open("/bot/gen/answer2.txt", "w") as f:
    f.write(' '.join(arr))