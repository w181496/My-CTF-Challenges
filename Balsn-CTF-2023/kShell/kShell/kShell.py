#!/usr/bin/python
# coding: UTF-8

import os
import sys
import signal
import socket
import getpass
from time import sleep
from distutils.spawn import find_executable
from subprocess import CalledProcessError, call
import shlex


COMMANDS = [
    'id', 
    'ping', 
    'traceroute', 
    'ssh',
    'arp', 
    'netstat', 
    'pwd'
]

COLORS = {
    'header': '\033[95m', 
    'blue': '\033[94m', 
    'cyan': '\033[96m', 
    'green': '\033[92m', 
    'warning': '\033[93m', 
    'fail': '\033[91m', 
    'endc': '\033[0m', 
    'bold': '\033[1m', 
    'underline': '\033[4m', 
    'blink': '\033[5m', 
}

BLOCK_KEYOWRD = "COMMAND"

def _signal_handler(sig, frame):
    sys.stdout.write('\n' + _color(get_prompt(), 'green'))
    sys.stdout.flush()

def _sanity_check():
    for cmd in COMMANDS:
        assert find_executable(cmd), "'%s' not found!" % cmd

def _print_banner():
    print(_color('Welcome to ', 'bold'))
    print(_color('''
 _    ___  _          _  _
| |__/ __|| |_   ___ | || |
| / /\__ \| ' \ / -_)| || |
|_\_\|___/|_||_|\___||_||_|
'''.lstrip('\n'), 'bold'))
        
def _cmd(cmd, args):
    cmds = [cmd]
    if args:
        cmds += args
    try:
        return call(cmds)
    except CalledProcessError as e:
        return e.output

def _color(s, color=''):
    code = COLORS.get(color)
    if code:
        return COLORS['bold'] + code + s + COLORS['endc'] + COLORS['endc']
    else:
        return s

def _exit(msg):
    print(_color("\n\n" + msg, 'warning'))
    os._exit(1)

def syslog(message, level=7, facility=1): 
    host = os.environ.get('LOG_HOST', '172.17.0.1')
    team_name = os.environ.get('TEAM_NAME', 'unknown')
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    message = '<kshell-logger> (%s) %s' % (team_name, message)
    data = '<%d>%s' % (level + facility*8, message)
    sock.sendto(data.encode(), (host, 514))
    sock.close()

def get_prompt():
    prompt = 'kshell~$ '
    return prompt

def usage():
    cmds = []
    cmds.append('help')
    cmds.append('exit')
    cmds.extend(COMMANDS)

    print(_color('Available commands: ', "bold"))
    print('  ' + '\n  '.join(cmds))

signal.signal(signal.SIGINT, _signal_handler)

if __name__ == '__main__':

    _sanity_check()
    _print_banner()

    while True:
        try:
            prompt = get_prompt()
            line = input(_color(prompt, 'green'))
            line = line.strip()

            if not line:
                continue

            syslog(line)

            cmd_args = shlex.split(line)
            cmd = cmd_args[0]
            args = cmd_args[1:] if len(cmd_args) > 1 else None

            is_ok = True
            if args:
                for arg in args:
                    if BLOCK_KEYOWRD.lower() in arg.lower():
                        print(_color('keyword "%s" is not allowed!' % BLOCK_KEYOWRD, 'fail'))
                        is_ok = False
                        break
            if not is_ok:
                continue

            if cmd == 'exit' or cmd == 'quit' or cmd == 'q':
                _exit('Bye!')

            if cmd == 'help' or cmd == '?':
                usage()

            elif cmd in COMMANDS:
                _cmd(cmd, args)

            else:
                print('command not found, try "help"')
        except:
            print(_color('Meow! An error occurred!', 'fail'))
